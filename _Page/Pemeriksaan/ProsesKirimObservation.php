<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    /* Response default */
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if(empty($_POST['id_laboratorium_rincian'])){
        echo json_encode(['status'  => 'error','message' => 'ID Rincian Laboratorium Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['payload'])){
        echo json_encode(['status'  => 'error','message' => 'Payload Data Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium_rincian = $_POST['id_laboratorium_rincian'];
    $payload                 = $_POST['payload'];

    // Membuka Pengaturan Koneksi SATUSEHAT
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo json_encode(['status'  => 'error','message' => 'Gagal Membuka Koneksi Satusehat!']);
        exit;
    }
    if(empty($config['organization_id'])){
       echo json_encode(['status'  => 'error','message' => 'Koneksi Satusehat Tidak Ditemukan']);
        exit;
    }

   // GENERATE TOKEN SATUSEHAT
    $tokenResult = generateTokenSatuSehat($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo json_encode([
            'status'  => 'error',
            'message' => $tokenResult['message']
        ]);
        exit;
    }

    $token = $tokenResult['token'];

    // Menentukan URL
    $url_api = rtrim($config['url_connection_satu_sehat'], '/');
    $url_api = $url_api . '/fhir-r4/v1/Observation';

    // KIRIM KE SATUSEHAT
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    // HANDLE ERROR CURL
    if ($curl_error) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'cURL Error: ' . $curl_error
        ]);
        exit;
    }

   // DECODE RESPONSE
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Response bukan JSON valid.',
            'response_raw' => substr($response, 0, 300)
        ]);
        exit;
    }

    // VALIDASI RESPONSE SATUSEHAT
    if ($http_code !== 201) {
        $msg = 'Gagal mengirim Observation ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }

        echo json_encode([
            'status'  => 'error',
            'message' => $msg,
            'http_code' => $http_code
        ]);
        exit;
    }

    // SIMPAN ID KE DATABASE
    $id_observation = $result['id'] ?? null;

     if ($id_observation) {
        $upd = $Conn->prepare("UPDATE laboratorium_rincian SET id_observation = ? WHERE id_laboratorium_rincian = ?");
        $upd->bind_param("si", $id_observation, $id_laboratorium_rincian);
        $upd->execute();
        $upd->close();
    }

    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'        => 'success',
        'message'       => 'Observation Berhasil dikirim ke SATUSEHAT'
    ]);
    exit;

?>