<?php
    date_default_timezone_set("Asia/Jakarta");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Metode Pengiriman Data Tidak Diijinkan"
        ]);
        exit;
    }

    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    # ======================================================
    # AUTH TOKEN
    # ======================================================

    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Token Bearer tidak ditemukan"
        ]);
        exit;
    }

    $token = $matches[1];

    $stmt = $Conn->prepare("SELECT id_api_account, token FROM api_token WHERE expired_at > UTC_TIMESTAMP()");
    $stmt->execute();
    $result = $stmt->get_result();

    $token_valid = false;
    $id_api_account = null;

    while ($row = $result->fetch_assoc()) {
        if (password_verify($token, $row['token'])) {
            $token_valid = true;
            $id_api_account = $row['id_api_account'];
            break;
        }
    }

    if (!$token_valid) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Token tidak valid atau expired"
        ]);
        exit;
    }

    # ======================================================
    # DAFTAR KOLOM VALID
    # ======================================================

    $allowed_columns = [
    "id_laboratorium",
    "id_pasien",
    "id_kunjungan",
    "ihs_pasien",
    "id_encounter",
    "nama",
    "gender",
    "tanggal_lahir",
    "tujuan",
    "pembayaran",
    "fakses",
    "unit",
    "priority",
    "kode_dokter_pengirim",
    "ihs_dokter_pengirim",
    "nama_dokter_pengirim",
    "kode_dokter_penerima",
    "ihs_dokter_penerima",
    "nama_dokter_penerima",
    "kode_petugas",
    "ihs_petugas",
    "nama_petugas",
    "diagnosis",
    "puasa",
    "status",
    "datetime_diminta",
    "datetime_diterima",
    "datetime_spesimen",
    "datetime_hasil",
    "keterangan",
    "alasan",
    "form_system"
    ];

    # ======================================================
    # PARAMETER
    # ======================================================

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $order_by = $_GET['order_by'] ?? "datetime_diminta";
    $short_by = strtoupper($_GET['short_by'] ?? "DESC");

    $keyword_by = $_GET['keyword_by'] ?? "";
    $keyword = $_GET['keyword'] ?? "";

    # ======================================================
    # VALIDASI ORDER
    # ======================================================

    if(!in_array($order_by,$allowed_columns)){
        $order_by="datetime_diminta";
    }

    if(!in_array($short_by,["ASC","DESC"])){
        $short_by="DESC";
    }

    # ======================================================
    # FILTER KEYWORD
    # ======================================================

    $where="";
    $params=[];
    $types="";

    if(!empty($keyword)){

        if(!empty($keyword_by) && in_array($keyword_by,$allowed_columns)){

            $where=" WHERE $keyword_by LIKE ?";
            $params[]="%$keyword%";
            $types.="s";

        }else{

            $like=[];
            foreach($allowed_columns as $col){
                $like[]="$col LIKE ?";
                $params[]="%$keyword%";
                $types.="s";
            }

            $where=" WHERE ".implode(" OR ",$like);

        }

    }

    # ======================================================
    # HITUNG TOTAL DATA
    # ======================================================

    $count_sql="SELECT COUNT(*) as total FROM laboratorium $where";

    $count_stmt=$Conn->prepare($count_sql);

    if(!empty($params)){
        $count_stmt->bind_param($types,...$params);
    }

    $count_stmt->execute();
    $count_result=$count_stmt->get_result()->fetch_assoc();

    $total_data=$count_result['total'];

    if($total_data==0){

        http_response_code(404);
        echo json_encode([
            "status"=>"Not Found",
            "message"=>"Data Tidak Ditemukan"
        ]);
        exit;

    }

    # ======================================================
    # HITUNG PAGE
    # ======================================================

    $total_page=ceil($total_data/$limit);

    if($page>$total_page){

        http_response_code(200);
        echo json_encode([
            "status"=>"success",
            "message"=>"Halaman melebihi jumlah halaman",
            "jumlah_data"=>$total_data,
            "total_page"=>$total_page,
            "current_page"=>$page,
            "data_list"=>[]
        ]);
        exit;

    }

    $offset=($page-1)*$limit;

    # ======================================================
    # AMBIL DATA
    # ======================================================

    $data_sql="
    SELECT *
    FROM laboratorium
    $where
    ORDER BY $order_by $short_by
    LIMIT ?, ?
    ";

    $data_stmt=$Conn->prepare($data_sql);

    if(!empty($params)){

        $types.="ii";
        $params[]=$offset;
        $params[]=$limit;

        $data_stmt->bind_param($types,...$params);

    }else{

        $data_stmt->bind_param("ii",$offset,$limit);

    }

    $data_stmt->execute();
    $data_result=$data_stmt->get_result();

    $data_list=[];

    while($row=$data_result->fetch_assoc()){
        $diagnosis = null;
        if(!empty($row['diagnosis'])){
            $diagnosis = json_decode($row['diagnosis'], true);
        }

        $row['diagnosis'] = $diagnosis;
        $data_list[]=$row;
    }

    # ======================================================
    # RESPONSE
    # ======================================================

    http_response_code(200);

    echo json_encode([
        "status"=>"success",
        "message"=>"Data Ditemukan",
        "jumlah_data"=>$total_data,
        "total_page"=>$total_page,
        "current_page"=>$page,
        "limit"=>$limit,
        "data_list"=>$data_list
    ]);

    exit;

?>