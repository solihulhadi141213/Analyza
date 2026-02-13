<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    date_default_timezone_set("Asia/Jakarta");

    // Select2 mengirim term pencarian pada key "search".
    $keyword = isset($_POST['search']) ? trim($_POST['search']) : '';
    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }
    $limit = 10;

    $status_connection_simrs = 1;
    $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

    $token = GetSimrsToken($Conn);
    if ($token === false || empty($url_connection_simrs)) {
        echo json_encode([
            'results' => [],
            'more' => false
        ]);
        exit;
    }

    $params = http_build_query([
        'limit'      => $limit,
        'page'       => $page,
        'order_by'   => 'id_diagnosa',
        'short_by'   => 'DESC',
        'keyword_by' => '',
        'keyword'    => $keyword
    ]);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url_connection_simrs . '/API/SIMRS/get_icd10.php?' . $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'token: ' . $token,
            'X-API-Key: ******'
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err || empty($response)) {
        echo json_encode([
            'results' => [],
            'more' => false
        ]);
        exit;
    }

    $dataArr = json_decode($response, true);
    $results = [];
    $more = false;

    if (
        isset($dataArr['response']['code']) &&
        (int) $dataArr['response']['code'] === 200 &&
        isset($dataArr['metadata']['list']) &&
        is_array($dataArr['metadata']['list'])
    ) {
        foreach ($dataArr['metadata']['list'] as $item) {
            $kode     = isset($item['kode']) ? $item['kode'] : '';
            $shortDes = isset($item['short_des']) ? $item['short_des'] : '';
            $longDes  = isset($item['long_des']) ? $item['long_des'] : '';
            $text     = trim($kode . ' - ' . $shortDes, ' -');

            $results[] = [
                'id'        => isset($item['id_diagnosa']) ? $item['id_diagnosa'] : '',
                'text'      => $text,
                'kode'      => $kode,
                'short_des' => $shortDes,
                'long_des'  => $longDes,
                'system'    => 'http://hl7.org/fhir/sid/icd-10'
            ];
        }

        $pageCount = isset($dataArr['metadata']['page_count']) ? (int) $dataArr['metadata']['page_count'] : $page;
        $more = $page < $pageCount;
    }

    echo json_encode([
        'results' => $results,
        'more' => $more
    ]);
?>
