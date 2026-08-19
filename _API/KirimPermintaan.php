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

    // ======================================================
    // REQUIRED FIELDS (MANDATORY)
    // ======================================================
    $required = [
        "id_pasien", 
        "id_kunjungan", 
        "nama", 
        "gender", 
        "tanggal_lahir", 
        "tujuan", 
        "pembayaran", 
        "fakses", 
        "unit", 
        "priority",
        "kode_dokter_pengirim",
        "nama_dokter_pengirim",
        "nama_diagnosis",
        "kode_diagnosis",
        "system_diagnosis"
    ];

    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === "") {
            http_response_code(422);
            echo json_encode([
                "status" => "error",
                "message" => "Field $field wajib, tidak boleh kosong!"
            ]);
            exit;
        }
    }

    // Apabila Rincian Pemeriksaan Tidak Ada
    if (empty($data['referensi_pemeriksaan']) || empty(count($data['referensi_pemeriksaan']))) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Rincian pemeriksaan tidak boleh kosong!"
        ]);
        exit;
    }

    // ======================================================
    // VALIDATE GENDER
    // ======================================================
    if (!in_array($data['gender'], ['Laki-laki', 'Perempuan'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Jenis Kelamin (Gender) Tidak Valid"
        ]);
        exit;
    }

    // ======================================================
    // VALIDATE TUJUAN
    // ======================================================
    $tujuan = $data['tujuan'];
    if (!in_array($tujuan, ['Rajal', 'Ranap'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Tujuan hanya boleh Rajal atau Ranap"
        ]);
        exit;
    }

    // ======================================================
    // VALIDATE PRIORITY
    // ======================================================
    if (!in_array($data['priority'], ['routine', 'urgent', 'stat'])) {
        http_response_code(422);
        echo json_encode([
            "status" => "error",
            "message" => "Priority hanya boleh routine, urgent, stat"
        ]);
        exit;
    }

    // ======================================================
    // GENERATE id_laboratorium
    // ======================================================
    $id_laboratorium = generateUUIDv4();

    // ======================================================
    // PREPARE DATA
    // ======================================================
    $id_pasien             = (int) $data['id_pasien'];
    $ihs_pasien            = $data['ihs_pasien'];
    $id_kunjungan          = (int) $data['id_kunjungan'];
    $id_encounter          = trim($data['id_encounter'] ?? '');
    $nama                  = trim($data['nama']);
    $gender                = trim($data['gender']);
    $tanggal_lahir         = trim($data['tanggal_lahir']);
    $tujuan                = $data['tujuan'];
    $pembayaran            = $data['pembayaran'];
    $fakses                = trim($data['fakses']);
    $unit                  = trim($data['unit']);
    $priority              = $data['priority'];
    $kode_dokter_pengirim  = $data['kode_dokter_pengirim'] ?? '';
    $ihs_dokter_pengirim   = $data['ihs_dokter_pengirim'] ?? '';
    $nama_dokter_pengirim  = trim($data['nama_dokter_pengirim']);
    $kode_petugas          = $data['kode_petugas'] ?? '';
    $ihs_petugas           = $data['ihs_petugas'] ?? '';
    $nama_petugas          = $data['nama_petugas'] ?? '';
    $nama_diagnosis        = $data['nama_diagnosis'] ?? '';
    $kode_diagnosis        = $data['kode_diagnosis'] ?? '';
    $system_diagnosis      = $data['system_diagnosis'] ?? '';
    $keterangan            = trim((string)($data['keterangan'] ?? ''));
    $referensi_pemeriksaan = $data['referensi_pemeriksaan'];

    // Buat Payload Diagnosis
    $payload_diagnosis = [
        "code"    => $kode_diagnosis,
        "display" => $nama_diagnosis,
        "system"  => $system_diagnosis
    ];
    $diagnosis     = json_encode($payload_diagnosis, JSON_UNESCAPED_UNICODE);

    // Menetapkan Tanggal Diminta dan Status
    $status           = "Diminta";
    $datetime_diminta = date('Y-m-d H:i');
    $form_system      = $api_name;

    // ======================================================
    // INSERT DATABASE
    // ======================================================
    $sql = "
    INSERT INTO laboratorium (
        id_laboratorium, 
        id_pasien, 
        id_kunjungan, 
        ihs_pasien,
        id_encounter, 
        nama, 
        gender, 
        tanggal_lahir,
        tujuan, 
        pembayaran, 
        fakses, 
        unit,
        priority, 
        kode_dokter_pengirim, 
        ihs_dokter_pengirim, 
        nama_dokter_pengirim,
        kode_petugas,
        ihs_petugas,
        nama_petugas,
        diagnosis,
        status,
        datetime_diminta,
        keterangan,
        form_system
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $Conn->begin_transaction();

    $stmt = $Conn->prepare($sql);

    $stmt->bind_param(
        "ssssssssssssssssssssssss",
        $id_laboratorium,
        $id_pasien,
        $id_kunjungan,
        $ihs_pasien,
        $id_encounter,
        $nama,
        $gender,
        $tanggal_lahir,
        $tujuan,
        $pembayaran,
        $fakses,
        $unit,
        $priority,
        $kode_dokter_pengirim,
        $ihs_dokter_pengirim,
        $nama_dokter_pengirim,
        $kode_petugas,
        $ihs_petugas,
        $nama_petugas,
        $diagnosis,
        $status,
        $datetime_diminta,
        $keterangan,
        $form_system
    );

    if (!$stmt->execute()) {
        $Conn->rollback();
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan permintaan pemeriksaan laboratorium",
            "error" => $stmt->error
        ]);
        exit;
    }

    // Lanjutkan melakukan looping data pemeriksaan yang diterima
    // Inisiasai Jumlah Berhasil
    $rincian_berhasil = 0;
    $log_proses_entry_rincian = [];
    $status_rincian = "success";
    $detail_lookup = $Conn->prepare("
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
                "status" => $status_rincian
            ];
            continue;
        }

        $Data = $detail_lookup->get_result()->fetch_assoc();
        if (!$Data) {
            $status_rincian = "Referensi tidak ditemukan";
            $log_proses_entry_rincian[] = [
                "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
                "status" => $status_rincian
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

        if (!$stmt_rincian->execute()) {
            $status_rincian = $stmt_rincian->error;
        } else {
            $status_rincian = "success";
            $rincian_berhasil++;
        }

        $log_proses_entry_rincian[] = [
            "id_referensi_pemeriksaan" => $id_referensi_pemeriksaan,
            "status" => $status_rincian
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
    $permintaan_laboratorium = [
        "id_pasien"            => $id_pasien,
        "id_kunjungan"         => $id_kunjungan,
        "ihs_pasien"           => $ihs_pasien,
        "id_encounter"         => $id_encounter,
        "nama"                 => $nama,
        "gender"               => $gender,
        "tanggal_lahir"        => $tanggal_lahir,
        "tujuan"               => $tujuan,
        "pembayaran"           => $pembayaran,
        "fakses"               => $fakses,
        "unit"                 => $unit,
        "priority"             => $priority,
        "kode_dokter_pengirim" => $kode_dokter_pengirim,
        "ihs_dokter_pengirim"  => $ihs_dokter_pengirim,
        "nama_dokter_pengirim" => $nama_dokter_pengirim,
        "kode_petugas"         => $kode_petugas,
        "ihs_petugas"          => $ihs_petugas,
        "nama_petugas"         => $nama_petugas,
        "diagnosis"            => $payload_diagnosis,
        "status"               => $status,
        "datetime_diminta"     => $datetime_diminta,
        "keterangan"           => $keterangan,
        "form_system"          => $form_system
    ];
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Permintaan Pemeriksaan Berhasil Dibuat",
        "data" => [
            "id_laboratorium" => $id_laboratorium,
            "permintaan_laboratorium" => $permintaan_laboratorium,
            "log_proses_entry_rincian" => $log_proses_entry_rincian
        ]
    ]);
    exit;
?>
