<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
    //Zona Waktu Pakai UTC
    date_default_timezone_set('UTC');
    $datetime_now = new DateTime();

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    //id_speciment wajib terisi
    if(empty($_POST['id_speciment'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Specimen Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_speciment' dan sanitasi
    $id_speciment = validateAndSanitizeInput($_POST['id_speciment']);

    // Membuka Pengaturan Koneksi Satusehat
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT * FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi SATUSEHAT Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }
    if(empty($config['organization_id'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi SATUSEHAT Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    // GENERATE TOKEN SATUSEHAT
    $tokenResult = generateTokenSatuSehat($Conn);
    if ($tokenResult['status'] !== 'success') {
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal membuat token satusehat! <br> Pesan : '.$tokenResult['message'].'</small>
            </div>
        ';
        exit;
    }
    $token = $tokenResult['token'];

    // Menentukan URL
    $url_api = rtrim($config['url_connection_satu_sehat'], '/');
    $url_api = $url_api . '/fhir-r4/v1/Specimen/'.$id_speciment.'';

    // KIRIM KE SATUSEHAT
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url_api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
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
            <div class="alert alert-danger text-center">
                <small>CURL Error! <br> Pesan : '.$curl_error.'</small>
            </div>
        ';
        exit;
    }

   // DECODE RESPONSE
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Response bukan JSON valid! <br> Pesan : '.substr($response, 0, 300).'</small>
            </div>
        ';
        exit;
    }

    // VALIDASI RESPONSE SATUSEHAT
    if ($http_code !== 200) {
        $msg = 'Gagal mengirim Procedure ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal mengirim Data ke SATUSEHAT! <br> Pesan : '.$http_code.'</small>
            </div>
        ';
        exit;
    }
   

    $PayloadJson = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $PayloadJsonHtml = htmlspecialchars($PayloadJson, ENT_QUOTES, 'UTF-8');

    echo '
        <div class="row mb-3">
            <div class="col-12">
                <b>Payload :</b>
                <pre class="bg-light border rounded p-3 mb-0"><code>'.$PayloadJson.'</code></pre>
            </div> 
        </div>
    ';
    
?>
