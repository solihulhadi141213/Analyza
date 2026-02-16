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
    if(empty($_POST['id_laboratorium_procedure'])){
        echo json_encode(['status'  => 'error','message' => 'ID Lokal Prosedur Puasa Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium_procedure = $_POST['id_laboratorium_procedure'];
    
    // Variabel Tidak Wajib
    if(empty($_POST['id_procedure'])){
       $id_procedure = "";
    }else{
        $id_procedure = $_POST['id_procedure'];
    }
    if(empty($_POST['update_procedure'])){
       $update_procedure = 0;
    }else{
        $update_procedure = $_POST['update_procedure'];
    }

    // Apabila User berkenan update ke satu sehat
    if($update_procedure==1){

        // Validasi 
        if(empty($_POST['id_procedure'])){
            echo json_encode(['status'  => 'error','message' => 'ID Prosedur Puasa Tidak Boleh Kosong!']);
            exit;
        }
        $payload = [
            [
                "op" => "replace",
                "path" => "/status",
                "value" => "entered-in-error"
            ]
        ];
        $PayloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
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
        $url_api = $url_api . '/fhir-r4/v1/Procedure/'.$id_procedure.'';

        // KIRIM KE SATUSEHAT
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => $PayloadJson,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json-patch+json',
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
                'message' => 'cURL Error: ' . $curl_error,
                'payload' => $PayloadJson
            ]);
            exit;
        }

        // DECODE RESPONSE
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Response bukan JSON valid.',
                'response_raw' => substr($response, 0, 300),
                'payload' => $PayloadJson
            ]);
            exit;
        }

        // VALIDASI RESPONSE SATUSEHAT
        if ($http_code !== 200) {
            $msg = 'Gagal mengirim ServiceRequest ke SATUSEHAT';

            if (($result['resourceType'] ?? '') === 'OperationOutcome') {
                $msg = $result['issue'][0]['details']['text']
                    ?? $result['issue'][0]['diagnostics']
                    ?? $msg;
            }

            echo json_encode([
                'status'  => 'error',
                'message' => $msg,
                'http_code' => $http_code,
                'payload' => $PayloadJson
            ]);
            exit;
        }

        //id_procedure
        $id_procedure = $result['id'] ?? null;

        if(empty($id_procedure)){
            echo json_encode(['status'  => 'error','message' => 'Tidak Ada Response ID procedure yang diterima!']);
            exit;
        }
    }

    // Hapus Data
    $HapusProcedure = mysqli_query($Conn, "DELETE FROM laboratorium_procedure WHERE id_laboratorium_procedure='$id_laboratorium_procedure'") or die(mysqli_error($Conn));
    if ($HapusProcedure) {
        echo json_encode(['status'  => 'success','message' => 'Hapus Procedure Berhasil']);
        exit; 
    }else{
        echo json_encode(['status'  => 'error','message' => 'Hapus Procedure Gagal!']);
        exit; 
    }
?>