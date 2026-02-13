<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");
    // Buka URL SIMRS
    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);
    
    // Parameter select2
    $search = strtolower(trim($_POST['search'] ?? ''));
    $page   = (int)($_POST['page'] ?? 1);
    if ($page < 1) {
        $page = 1;
    }

    // Inisiasi data
    $results = [];
    $limit   = 10;
    $offset  = ($page - 1) * $limit;
    $total   = 0;

    // Jika token valid, ambil data dokter dari SIMRS
    if (!empty($token) && $token !== true) {
        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_dokter.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'token: '.$token.'',
                'X-API-Key: ••••••'
            ),
        ));
        $response_dokter = curl_exec($curl2);
        curl_close($curl2);
        
        // Ubah response menjadi array
        $data_dokter = json_decode($response_dokter, true);

        // Jika response valid, proses filter + pagination lokal
        if (!empty($data_dokter['response']['code']) && (int)$data_dokter['response']['code'] === 200) {
            $metadata_dokter = $data_dokter['metadata'] ?? [];
            $list_dokter     = $metadata_dokter['list_dokter'] ?? [];

            if (!empty($search)) {
                $list_dokter = array_values(array_filter($list_dokter, function ($dokter) use ($search) {
                    $nama = strtolower($dokter['nama'] ?? '');
                    $kode = strtolower($dokter['kode'] ?? '');
                    return (strpos($nama, $search) !== false || strpos($kode, $search) !== false);
                }));
            }

            $total = count($list_dokter);
            $paged = array_slice($list_dokter, $offset, $limit);

            foreach ($paged as $dokter) {
                $results[] = [
                    "id"                  => $dokter['nama'] ?? '',
                    "text"                => $dokter['nama'] ?? '',
                    "nama"                => $dokter['nama'] ?? '',
                    "kode"                => $dokter['kode'] ?? '',
                    "id_dokter"           => $dokter['id_dokter'] ?? '',
                    "id_ihs_practitioner" => $dokter['id_ihs_practitioner'] ?? ''
                ];
            }
        }
    }

    echo json_encode([
        "results" => $results,
        "more"    => ($offset + $limit) < $total
    ]);

?>
