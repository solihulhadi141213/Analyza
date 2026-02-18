<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    /**
     * Helper kirim response JSON lalu hentikan proses
     */
    function fail($message) {
        echo json_encode([
            'status'  => 'error',
            'message' => $message
        ]);
        exit;
    }

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        fail('Sesi Akses Sudah Berakhir! Silahkan Login Ulang!');
    }

    // Validasi Data Wajib (Mandatory)
    $mandatory_fields = [
        'id_laboratorium' => 'ID Laboratorium Tidak Boleh Kosong!'
    ];

    foreach ($mandatory_fields as $field => $message) {
        $value = $_POST[$field] ?? null;

        if (is_array($value)) {
            if (count(array_filter($value, function ($item) {
                return trim((string)$item) !== '';
            })) === 0) {
                fail($message);
            }
            continue;
        }

        if ($value === null || trim((string)$value) === '') {
            fail($message);
        }
    }

    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    $id_referensi_pemeriksaan_raw = $_POST['id_referensi_pemeriksaan'] ?? [];
    if (!is_array($id_referensi_pemeriksaan_raw)) {
        $id_referensi_pemeriksaan_raw = [$id_referensi_pemeriksaan_raw];
    }

    $id_referensi_pemeriksaan = [];
    foreach ($id_referensi_pemeriksaan_raw as $id_ref) {
        $id_ref = (int)trim((string)$id_ref);
        if ($id_ref > 0) {
            $id_referensi_pemeriksaan[] = $id_ref;
        }
    }
    $id_referensi_pemeriksaan = array_values(array_unique($id_referensi_pemeriksaan));

    if (empty($id_referensi_pemeriksaan)) {
        fail('Permintaan Pemeriksaan Tidak Boleh Kosong!');
    }

    // Prepare statement lookup referensi pemeriksaan
    $stmtRef = $Conn->prepare("
        SELECT 
            nama_pemeriksaan,
            category_pemeriksaan
        FROM referensi_pemeriksaan
        WHERE id_referensi_pemeriksaan = ?
        LIMIT 1
    ");
    if (!$stmtRef) {
        throw new Exception('Gagal menyiapkan query referensi pemeriksaan');
    }

    // Prepare insert laboratorium_rincian
    $stmtRincian = $Conn->prepare("
        INSERT INTO laboratorium_rincian (
            id_laboratorium,
            id_referensi_pemeriksaan,
            nama_pemeriksaan,
            category_pemeriksaan
        ) VALUES (?,?,?,?)
    ");
    if (!$stmtRincian) {
        throw new Exception('Gagal menyiapkan query insert laboratorium rincian');
    }

    // Multiple insert detail pemeriksaan
    foreach ($id_referensi_pemeriksaan as $id_ref) {
        $stmtRef->bind_param("i", $id_ref);
        if (!$stmtRef->execute()) {
            throw new Exception('Gagal mengambil referensi pemeriksaan');
        }

        $rsRef = $stmtRef->get_result();
        if ($rsRef->num_rows === 0) {
            throw new Exception('Referensi pemeriksaan dengan ID ' . $id_ref . ' tidak ditemukan');
        }

        $dataRef = $rsRef->fetch_assoc();
        $nama_pemeriksaan_ref     = $dataRef['nama_pemeriksaan'] ?? '';
        $category_pemeriksaan_ref = $dataRef['category_pemeriksaan'] ?? '';

        $stmtRincian->bind_param(
            "siss",
            $id_laboratorium,
            $id_ref,
            $nama_pemeriksaan_ref,
            $category_pemeriksaan_ref
        );

        if (!$stmtRincian->execute()) {
            throw new Exception('Gagal menyimpan rincian pemeriksaan');
        }
    }

    $stmtRef->close();
    $stmtRincian->close();

    $Conn->commit();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Data pemeriksaan berhasil disimpan'
    ]);
    exit;

?>