<?php
    // ======================================================
    // SET HEADER & TIMEZONE
    // ======================================================
    date_default_timezone_set("Asia/Jakarta");

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // ======================================================
    // METHOD VALIDATION
    // ======================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    // Buka Nama Akun API
    $api_name = GetDetailData($Conn, 'api_account', 'id_api_account', $id_api_account, 'api_name');

    // ======================================================
    // GET JSON BODY
    // ======================================================
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Payload JSON tidak valid"
        ]);
        exit;
    }
    // Apabila Rincian Pemeriksaan Tidak Ada
    if (empty($data) || empty(count($data))) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Rincian pemeriksaan tidak boleh kosong!"
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
    // GENERATE id_laboratorium
    // ======================================================
    $id_laboratorium = $_GET['id'];

    // Validasi 'id_laboratorium'
    $id_laboratorium = GetDetailData($Conn, 'laboratorium', 'id_laboratorium', $id_laboratorium, 'id_laboratorium');
    if(empty($id_laboratorium)){
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "ID Laboratorium : $id_laboratorium Tidak Valid! Atau data tidak ditemukan pada database aplikasi!"
        ]);
        exit;
    }
    // Variabel 'referensi_pemeriksaan'
    $referensi_pemeriksaan = $data;
    
    // ======================================================
    // Lanjutkan melakukan looping data pemeriksaan yang diterima
    // ======================================================
    
    // Inisiasai Jumlah Berhasil
    $rincian_berhasil         = 0;
    $log_proses_entry_rincian = [];
    $status_rincian           = "success";
    $detail_lookup            = $Conn->prepare("
        SELECT 
            rp.id_referensi_pemeriksaan,
            rp.nama_pemeriksaan,
            rp.category_pemeriksaan,
            rp.code_pemeriksaan,
            rp.display_pemeriksaan,
            rp.system_pemeriksaan,
            rp.result_type,
            rp.result_interpertation_type,
            rmp.nama_metode_pemeriksaan,
            rmp.display_metode_pemeriksaan,
            rmp.code_metode_pemeriksaan,
            rmp.system_metode_pemeriksaan
        FROM referensi_pemeriksaan rp
        LEFT JOIN referensi_pemeriksaan_relasi rpr 
            ON rpr.id_referensi_pemeriksaan = rp.id_referensi_pemeriksaan
        LEFT JOIN referensi_metode_pemeriksaan rmp 
            ON rmp.id_referensi_metode_pemeriksaan = rpr.id_referensi_metode_pemeriksaan
        WHERE rp.id_referensi_pemeriksaan = ?
    ");
    // Cek duplikat rincian di tabel laboratorium_rincian untuk laboratorium yang sama
    $cek_duplikat = $Conn->prepare("
        SELECT 1 
        FROM laboratorium_rincian 
        WHERE id_laboratorium = ? AND id_referensi_pemeriksaan = ? 
        LIMIT 1
    ");

    $sql_rincian = "
        INSERT INTO laboratorium_rincian (
            id_laboratorium, 
            id_referensi_pemeriksaan, 
            nama_pemeriksaan, 
            category_pemeriksaan, 
            metode_pemeriksaan, 
            metode_pemeriksaan_display, 
            metode_pemeriksaan_code, 
            metode_pemeriksaan_system
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt_rincian = $Conn->prepare($sql_rincian);

    foreach ($referensi_pemeriksaan as $referensi_pemeriksaan_list) {
        $id_referensi_pemeriksaan = $referensi_pemeriksaan_list['id_referensi_pemeriksaan'];

        // Ambil data referensi + metode dalam satu query
        $detail_lookup->bind_param("i", $id_referensi_pemeriksaan);
        if (!$detail_lookup->execute()) {
            $status_rincian   = $detail_lookup->error;
            $log_proses_entry_rincian[] = [
                "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
                "status" => $status_rincian,
                "id_laboratorium_rincian" => null
            ];
            continue;
        }

        $Data = $detail_lookup->get_result()->fetch_assoc();
        if (!$Data) {
            $status_rincian = "Referensi tidak ditemukan";
            $log_proses_entry_rincian[] = [
                "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
                "status" => $status_rincian,
                "id_laboratorium_rincian" => null
            ];
            continue;
        }

        // Validasi duplikat untuk laboratorium yang sama
        $cek_duplikat->bind_param("ss", $id_laboratorium, $id_referensi_pemeriksaan);
        if ($cek_duplikat->execute()) {
            $hasil_cek = $cek_duplikat->get_result();
            if ($hasil_cek && $hasil_cek->num_rows > 0) {
                $status_rincian = "Duplikat: id_referensi_pemeriksaan sudah ada pada laboratorium ini";
                $log_proses_entry_rincian[] = [
                    "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
                    "status" => $status_rincian,
                    "id_laboratorium_rincian" => null
                ];
                continue;
            }
        } else {
            $status_rincian   = $cek_duplikat->error;
            $log_proses_entry_rincian[] = [
                "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
                "status" => $status_rincian,
                "id_laboratorium_rincian" => null
            ];
            continue;
        }

        // Simpan Rincian ke tabel 'laboratorium_rincian'
        $stmt_rincian->bind_param(
            "ssssssss",
            $id_laboratorium,
            $Data['id_referensi_pemeriksaan'],
            $Data['nama_pemeriksaan'],
            $Data['category_pemeriksaan'],
            $Data['nama_metode_pemeriksaan'],
            $Data['display_metode_pemeriksaan'],
            $Data['code_metode_pemeriksaan'],
            $Data['system_metode_pemeriksaan']
        );

        $id_laboratorium_rincian = null;
        if (!$stmt_rincian->execute()) {
            $status_rincian = $stmt_rincian->error;
        } else {
            $status_rincian = "success";
            $rincian_berhasil++;
            $id_laboratorium_rincian = $Conn->insert_id;
        }

        $log_proses_entry_rincian[] = [
            "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
            "status" => $status_rincian,
            "id_laboratorium_rincian" => $id_laboratorium_rincian
        ];
    }

    // Commit atau rollback bila seluruh rincian gagal
    if ($rincian_berhasil === 0) {
        $Conn->rollback();
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Tidak ada rincian laboratorium yang berhasil disimpan",
            "log_proses_entry_rincian" => $log_proses_entry_rincian
        ]);
        exit;
    }

    $Conn->commit();

    // ======================================================
    // SUCCESS RESPONSE
    // ======================================================
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Rincian Pemeriksaan Berhasil Dibuat",
        "data" => [
            "id_laboratorium" => $id_laboratorium,
            "referensi_pemeriksaan" => $referensi_pemeriksaan,
            "log_proses_entry_rincian" => $log_proses_entry_rincian
        ]
    ]);
    exit;
?>
