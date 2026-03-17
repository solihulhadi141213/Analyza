<?php
    // ======================================================
    // SET HEADER & TIMEZONE
    // ======================================================
    date_default_timezone_set("Asia/Jakarta");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // ======================================================
    // METHOD VALIDATION
    // ======================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Metode Pengiriman Data Tidak Diijinkan"
        ]);
        exit;
    }

    // ======================================================
    // CONFIG
    // ======================================================
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";

    // ======================================================
    // AUTH BEARER TOKEN
    // ======================================================
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

    // Validate Token
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
    // Buka Nama Akun API
    $api_name = GetDetailData($Conn, 'api_account', 'id_api_account', $id_api_account, 'api_name');

    // Menghitung Jumlah Data
    $count_stmt = $Conn->prepare("SELECT COUNT(*) AS total FROM referensi_pemeriksaan");
    $count_stmt->execute();
    $count_result = $count_stmt->get_result()->fetch_assoc();
    $jumlah_data = $count_result['total'] ?? 0;

    if (empty($jumlah_data)) {
        http_response_code(404);
        echo json_encode([
            "status" => "Not Found",
            "message" => "Data Tidak Ditemukan"
        ]);
        exit;
    }

    $message = "Data Ditemukan";
    $data_list = [];

    // Ambil seluruh data sekaligus, urutkan lalu kelompokkan di PHP untuk menghindari N+1 query.
    $data_stmt = $Conn->prepare("
        SELECT category_pemeriksaan, id_referensi_pemeriksaan, nama_pemeriksaan, code_pemeriksaan,
               display_pemeriksaan, system_pemeriksaan, unit, unit_display, unit_code, unit_system,
               result_type, result_interpertation_type, allow_age, allow_sex
        FROM referensi_pemeriksaan
        ORDER BY category_pemeriksaan ASC, nama_pemeriksaan ASC
    ");
    $data_stmt->execute();
    $data_result = $data_stmt->get_result();

    $current_category = null;
    $list_by_category = [];

    while ($row = $data_result->fetch_assoc()) {
        if ($current_category !== $row['category_pemeriksaan']) {
            if ($current_category !== null) {
                $data_list[] = [
                    "category_pemeriksaan" => $current_category,
                    "list_by_category"     => $list_by_category,
                ];
            }
            $current_category = $row['category_pemeriksaan'];
            $list_by_category = [];
        }

        $list_by_category[] = [
            "id_referensi_pemeriksaan"   => $row['id_referensi_pemeriksaan'],
            "nama_pemeriksaan"           => $row['nama_pemeriksaan'],
            "code_pemeriksaan"           => $row['code_pemeriksaan'],
            "display_pemeriksaan"        => $row['display_pemeriksaan'],
            "system_pemeriksaan"         => $row['system_pemeriksaan'],
            "unit"                       => $row['unit'],
            "unit_display"               => $row['unit_display'],
            "unit_code"                  => $row['unit_code'],
            "unit_system"                => $row['unit_system'],
            "result_type"                => $row['result_type'],
            "result_interpertation_type" => $row['result_interpertation_type'],
            "allow_age"                  => $row['allow_age'],
            "allow_sex"                  => $row['allow_sex']
        ];
    }

    // Push kategori terakhir bila ada data
    if ($current_category !== null) {
        $data_list[] = [
            "category_pemeriksaan" => $current_category,
            "list_by_category"     => $list_by_category,
        ];
    }
    // ======================================================
    // SUCCESS RESPONSE
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => $message,
        "data" => $data_list
    ]);
    exit;
?>
