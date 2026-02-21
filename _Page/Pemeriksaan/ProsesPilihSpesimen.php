<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");
    function fail($message) {
        echo json_encode([
            'status'  => 'error',
            'message' => $message
        ]);
        exit;
    }

    // Validasi sesi
    if (empty($SessionIdAccess)) {
        fail('Sesi Akses Sudah Berakhir! Silahkan Login Ulang!');
    }

    // Wajib dari form
    if (empty($_POST['id_laboratorium_rincian'])) {
        fail('ID Rincian Pemeriksaan Tidak Boleh Kosong!');
    }

    // Wajib dari form
    if (empty($_POST['id_laboratorium_spesimen'])) {
        fail('Anda belum memilih spesimen manapun!');
    }
    $id_laboratorium_rincian  = (int) validateAndSanitizeInput($_POST['id_laboratorium_rincian']);
    $id_laboratorium_spesimen = (int) validateAndSanitizeInput($_POST['id_laboratorium_spesimen']);
    if ($id_laboratorium_rincian <= 0) {
        fail('ID Rincian Pemeriksaan Tidak Valid!');
    }
    if ($id_laboratorium_spesimen <= 0) {
        fail('ID Spesimen Tidak Valid!');
    }

    // Validasi data rincian
    $stmtRincian = $Conn->prepare("SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium_rincian = ? LIMIT 1");
    if (!$stmtRincian) {
        fail('Terjadi kesalahan saat menyiapkan validasi data rincian pemeriksaan!');
    }
    $stmtRincian->bind_param("i", $id_laboratorium_rincian);
    if (!$stmtRincian->execute()) {
        $stmtRincian->close();
        fail('Terjadi kesalahan saat validasi data rincian pemeriksaan!');
    }
    $dataRincian = $stmtRincian->get_result()->fetch_assoc();
    $stmtRincian->close();
    if (empty($dataRincian)) {
        fail('Data rincian pemeriksaan tidak ditemukan!');
    }

    // Validasi data spesimen
    $stmtSpesimen = $Conn->prepare("SELECT id_laboratorium_spesimen FROM laboratorium_spesimen WHERE id_laboratorium_spesimen = ? LIMIT 1");
    if (!$stmtSpesimen) {
        fail('Terjadi kesalahan saat menyiapkan validasi data spesimen!');
    }
    $stmtSpesimen->bind_param("i", $id_laboratorium_spesimen);
    if (!$stmtSpesimen->execute()) {
        $stmtSpesimen->close();
        fail('Terjadi kesalahan saat validasi data spesimen!');
    }
    $dataSpesimen = $stmtSpesimen->get_result()->fetch_assoc();
    $stmtSpesimen->close();
    if (empty($dataSpesimen)) {
        fail('Data spesimen tidak ditemukan!');
    }

    // Proses Update
    $query = $Conn->prepare("UPDATE laboratorium_rincian SET id_laboratorium_spesimen = ? WHERE id_laboratorium_rincian = ?");
    if (!$query) {
        fail('Terjadi kesalahan saat menyiapkan update rincian pemeriksaan!');
    }
    $query->bind_param("ii", $id_laboratorium_spesimen, $id_laboratorium_rincian);
    if (!$query->execute()) {
        $query->close();
        fail('Terjadi Kesalahan Pada Saat Update Rincian Pemeriksaan!');
    }
    $query->close();
    $Conn->close();
    echo json_encode([
        'status'  => 'success',
        'message' => 'Data spesimen berhasil disimpan'
    ]);
    exit;
?>
