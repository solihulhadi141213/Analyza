<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>
                    Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!
                </small>
            </div>
        ';
        exit;
    }

    //Tangkap NIK
    if(empty($_POST['nik'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Oopssss!</b> Silahkan isi form NIK terlebih dulu
                </small>
            </div>
        ';
        exit;
    }

    // Buat Variabel dan sanitasi
    $nik=validateAndSanitizeInput($_POST['nik']);

    // MEMBUAT TOKEN SATU SEHAT
    $tokenResult = generateTokenSatuSehat($Conn);

    if ($tokenResult['status'] !== 'success') {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    '.$tokenResult['message'].'
                </small>
            </div>
        ';
        exit;
    }

    $token = $tokenResult['token'];

    // AMBIL KONFIGURASI SATUSEHAT AKTIF
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT url_connection_satu_sehat, organization_id FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Oopssss!</b> Koneksi SATUSEHAT tidak ditemukan.
                </small>
            </div>
        ';
        exit;
    }

    $organization_id = $config['organization_id'];

    // BUAT URL
    $url_api    = rtrim($config['url_connection_satu_sehat'], '/');
    $url_tujuan = $url_api . '/fhir-r4/v1/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|'.$nik.'';

    // KIRIM KE SATUSEHAT
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url_tujuan,
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
                <small>
                    <b>Oopssss!</b> CURL Error<br>Keterangan : '.$curl_error.'
                </small>
            </div>
        ';
        exit;
    }

    // DECODE RESPONSE
    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Oopssss!</b> Response bukan JSON valid<br>Keterangan : '.substr($response, 0, 300).'
                </small>
            </div>
        ';
        exit;
    }

    // VALIDASI RESPONSE SATUSEHAT
    if ($http_code !== 200) {
        $msg = 'Gagal mengirim Medication ke SATUSEHAT';

        if (($result['resourceType'] ?? '') === 'OperationOutcome') {
            $msg = $result['issue'][0]['details']['text']
                ?? $result['issue'][0]['diagnostics']
                ?? $msg;
        }
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Oopssss!</b> Gagal mengirim request ke SATUSEHAT<br>Keterangan : '.$http_code.'
                </small>
            </div>
        ';
        exit;
    }

    // Jika Berhasil Tampilkan data
    // echo '<pre>'.$response.'</pre>';
    // Buat dalam Arry
    $result = json_decode($response, true);
    if(!empty($result ['entry'])){
        $entry     = $result ['entry'];

        if(!empty($entry)){
            foreach ($entry as $entry_list){
                $resource  = $entry_list ['resource'];

                echo '
                    <div class="row mb-2">
                        <div class="col-4"><small>Tanggal Lahir</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7"><small class="text text-grayish">'.date('d F Y', strtotime($resource['birthDate'])).'</small></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>Gender</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7"><small class="text text-grayish">'.$resource['gender'].'</small></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>ID Pracitioner</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-6">
                            <small class="text text-grayish put_id_practitioner">
                                '.$resource['id'].'
                            </small>
                        </div>
                        <div class="col-1">
                            <a href="javascript:void(0);" class="get_id_practitioner" data-id="'.$resource['id'].'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tempelkan ID Practitioner">
                                <small><i class="bi bi-clipboard-check"></i></small>
                            </a>
                        </div>
                    </div>
                ';

                foreach ($entry_list['resource']['address'] as $address){
                    echo '
                        <div class="row mb-2">
                            <div class="col-4"><small>Negara</small></div>
                            <div class="col-1"><small>:</small></div>
                            <div class="col-7"><small class="text text-grayish">'.$address['country'].'</small></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><small>Kab/Kota</small></div>
                            <div class="col-1"><small>:</small></div>
                            <div class="col-7"><small class="text text-grayish">'.$address['city'].'</small></div>
                        </div>
                    ';
                }
            }
        }else{
             echo '
                <div class="alert alert-danger text-center">
                    <small>
                        <b>Oopssss!</b> Data Practitioner Tidak Ditemukan!
                    </small>
                </div>
            ';
            exit;
        }
        
    }else{
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Oopssss!</b> Data Practitioner Tidak Ditemukan!
                </small>
            </div>
        ';
        exit;
    }
?>