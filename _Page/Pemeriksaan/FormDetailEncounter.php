<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi id_encounter tidak boleh kosong
    if(empty($_POST['id_encounter'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Encounter Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel id_encounter dan sanitasi
    $id_encounter       = validateAndSanitizeInput($_POST['id_encounter']);

    // Membuka Pengaturan Koneksi SATUSEHAT
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal Membuka Koneksi Satusehat!</small>
            </div>
        ';
        exit;
    }
    if(empty($config['organization_id'])){
       echo '
            <div class="alert alert-danger">
                <small>Koneksi Satusehat Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

   // GENERATE TOKEN SATUSEHAT
    $tokenResult = generateTokenSatuSehat($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan pada saat membuat token satusehat <br>Pesan : '.$tokenResult['message'].'!</small>
            </div>
        ';
        exit;
    }

    $token = $tokenResult['token'];

    // Menentukan URL
    $url_api = rtrim($config['url_connection_satu_sehat'], '/');
    $url_api = $url_api . '/fhir-r4/v1/Encounter/'.$id_encounter.'';
    $metode = "GET";

    // KIRIM KE SATUSEHAT
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $metode,
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
        echo '
            <div class="alert alert-danger">
                <small>cURL Error! '.$curl_error.'</small>
            </div>
        ';
        exit;
    }

   // DECODE RESPONSE
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '
            <div class="alert alert-danger">
                <small>Response Tidak Valid! <br> Response : '.$response.'</small>
            </div>
        ';
        exit;
    }

    // VALIDASI RESPONSE SATUSEHAT
    if ($http_code !== 200) {
        $msg = 'Gagal mengirim data permintaan ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }
        echo '
            <div class="alert alert-danger">
                <small>Response Tidak Valid! <br> Response : '.$msg.'</small>
            </div>
        ';
        exit;
    }
    $PayloadJson = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $PayloadJsonHtml = htmlspecialchars($PayloadJson, ENT_QUOTES, 'UTF-8');
    echo '
        <pre>'.$PayloadJsonHtml.'</pre>
    ';
?>