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
    # VALIDASI PARAMETER
    # ======================================================

    if(empty($_GET['id_laboratorium'])){
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "ID Pemeriksaan Tidak Boleh Kosong"
        ]);
        exit;
    }

    $id_laboratorium = validateAndSanitizeInput($_GET['id_laboratorium']);

    # ======================================================
    # DATA LABORATORIUM
    # ======================================================

    $Qry = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry->bind_param("s", $id_laboratorium);
    $Qry->execute();

    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (!$Data) {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Data Tidak Ditemukan"
        ]);
        exit;
    }

    $detail = [
        "id_pasien"            => $Data['id_pasien'],
        "id_kunjungan"         => $Data['id_kunjungan'],
        "ihs_pasien"           => $Data['ihs_pasien'],
        "id_encounter"         => $Data['id_encounter'],
        "nama"                 => $Data['nama'],
        "gender"               => $Data['gender'],
        "tanggal_lahir"        => $Data['tanggal_lahir'],
        "tujuan"               => $Data['tujuan'],
        "pembayaran"           => $Data['pembayaran'],
        "fakses"               => $Data['fakses'],
        "unit"                 => $Data['unit'],
        "priority"             => $Data['priority'],
        "kode_dokter_pengirim" => $Data['kode_dokter_pengirim'],
        "ihs_dokter_pengirim"  => $Data['ihs_dokter_pengirim'],
        "nama_dokter_pengirim" => $Data['nama_dokter_pengirim'],
        "kode_dokter_penerima" => $Data['kode_dokter_penerima'],
        "ihs_dokter_penerima"  => $Data['ihs_dokter_penerima'],
        "nama_dokter_penerima" => $Data['nama_dokter_penerima'],
        "kode_petugas"         => $Data['kode_petugas'],
        "ihs_petugas"          => $Data['ihs_petugas'],
        "nama_petugas"         => $Data['nama_petugas'],
        "diagnosis"            => json_decode($Data['diagnosis'], true),
        "puasa"                => $Data['puasa'],
        "status"               => $Data['status'],
        "datetime_diminta"     => $Data['datetime_diminta'],
        "datetime_diterima"    => $Data['datetime_diterima'],
        "datetime_spesimen"    => $Data['datetime_spesimen'],
        "datetime_hasil"       => $Data['datetime_hasil'],
        "keterangan"           => $Data['keterangan'],
        "alasan"               => $Data['alasan'],
        "form_system"          => $Data['form_system']
    ];

    # ======================================================
    # RINCIAN
    # ======================================================

    $stmt = $Conn->prepare("
        SELECT 
            lr.*,
            rp.unit,
            rp.unit_code
        FROM laboratorium_rincian AS lr
        LEFT JOIN referensi_pemeriksaan AS rp
            ON lr.id_referensi_pemeriksaan = rp.id_referensi_pemeriksaan
        WHERE lr.id_laboratorium = ?
        ORDER BY lr.category_pemeriksaan ASC
    ");

    $stmt->bind_param("s", $id_laboratorium);
    $stmt->execute();

    $rincian = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    # ======================================================
    # SPESIMEN
    # ======================================================

    $stmt = $Conn->prepare("SELECT * FROM laboratorium_spesimen WHERE id_laboratorium=? ORDER BY id_laboratorium_spesimen ASC");
    $stmt->bind_param("s", $id_laboratorium);
    $stmt->execute();
    $spesimen = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    # ======================================================
    # PROCEDURE
    # ======================================================

    $stmt = $Conn->prepare("SELECT * FROM laboratorium_procedure WHERE id_laboratorium=? ORDER BY id_laboratorium_procedure ASC");
    $stmt->bind_param("s", $id_laboratorium);
    $stmt->execute();
    $procedure = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    # ======================================================
    # DIAGNOSTIC
    # ======================================================

    $stmt = $Conn->prepare("SELECT * FROM laboratorium_diagnostic WHERE id_laboratorium=? ORDER BY id_laboratorium_diagnostic ASC");
    $stmt->bind_param("s", $id_laboratorium);
    $stmt->execute();
    $diagnostic = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    # ======================================================
    # RESPONSE
    # ======================================================

    http_response_code(200);
    echo json_encode([
        "status"     => "success",
        "message"    => "Data Ditemukan",
        "detail"     => $detail,
        "rincian"    => $rincian,
        "spesimen"   => $spesimen,
        "procedure"  => $procedure,
        "diagnostic" => $diagnostic
    ], JSON_UNESCAPED_UNICODE);

    exit;
?>