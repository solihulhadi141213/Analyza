<?php
    // ======================================================
    // SET HEADER & TIMEZONE
    // ======================================================
    date_default_timezone_set("Asia/Jakarta");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // ======================================================
    // METHOD VALIDATION
    // ======================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Method tidak diizinkan"
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

    // Validasi id_laboratorium melalui GET id
    if (empty($_GET['id'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "ID Laboratorium Tidak Boleh Kosong!"
        ]);
        exit;
    }

    // ======================================================
    // BUAT VARIABEL id_laboratorium
    // ======================================================
    $id_laboratorium = validateAndSanitizeInput($_GET['id']);

    // Validasi Status Pemeriksaan Hanya Boleh Ketika Masih 'Diminta'
    $status_lama = GetDetailData($Conn, 'laboratorium', 'id_laboratorium', $id_laboratorium, 'status');
    if($status_lama!=="Diminta"){
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Status Permintaan Sudah Diubah! Anda Tidak Bisa Menghapus Permintaan Ini!"
        ]);
        exit;
    }

    // ======================================================
    // DELETE DATABASE
    // ======================================================
    $sql_delete_perm    = "DELETE FROM laboratorium WHERE id_laboratorium = ?";

    $Conn->begin_transaction();

    $stmt_perm = $Conn->prepare($sql_delete_perm);
    $stmt_perm->bind_param("s", $id_laboratorium);

    if (!$stmt_perm->execute()) {
        $Conn->rollback();
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menghapus permintaan pemeriksaan laboratorium",
            "error" => $stmt_perm->error
        ]);
        exit;
    }

    // Pastikan data benar-benar terhapus
    if ($stmt_perm->affected_rows === 0) {
        $Conn->rollback();
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Permintaan laboratorium tidak ditemukan"
        ]);
        exit;
    }

    $Conn->commit();

    // ======================================================
    // SUCCESS RESPONSE
    // ======================================================
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Permintaan Pemeriksaan Berhasil Dihapus",
        "data" => [
            "id_laboratorium" => $id_laboratorium
        ]
    ]);
    exit;
?>
