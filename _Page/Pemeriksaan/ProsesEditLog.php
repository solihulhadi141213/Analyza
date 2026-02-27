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

    /**
     * Ubah pasangan tanggal + jam menjadi datetime SQL.
     * Jika keduanya kosong, kembalikan NULL.
     */
    function normalizeDateTimeInput($tanggal, $jam, $label) {
        $tanggal = trim((string)$tanggal);
        $jam     = trim((string)$jam);

        if ($tanggal === '' && $jam === '') {
            return null;
        }

        if ($tanggal === '' || $jam === '') {
            fail($label . ' harus diisi lengkap (tanggal dan jam).');
        }

        $jamNormalized = (substr_count($jam, ':') === 1) ? ($jam . ':00') : $jam;
        $datetime = $tanggal . ' ' . $jamNormalized;

        if (strtotime($datetime) === false) {
            fail('Format ' . strtolower($label) . ' tidak valid.');
        }

        return $datetime;
    }

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        fail('Sesi Akses Sudah Berakhir! Silahkan Login Ulang!');
    }

    // Validasi Data Wajib
    if (empty($_POST['id_laboratorium'])) {
        fail('ID Pemeriksaan Laboratorium Tidak Boleh Kosong!');
    }

    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    // Normalisasi input log datetime dari form
    $datetime_diminta = normalizeDateTimeInput(
        $_POST['tanggal_diminta'] ?? '',
        $_POST['jam_diminta'] ?? '',
        'Tanggal/Jam Diminta'
    );
    $datetime_diterima = normalizeDateTimeInput(
        $_POST['tanggal_diterima'] ?? '',
        $_POST['jam_diterima'] ?? '',
        'Tanggal/Jam Diterima'
    );
    $datetime_spesimen = normalizeDateTimeInput(
        $_POST['tanggal_spesimen'] ?? '',
        $_POST['jam_spesimen'] ?? '',
        'Tanggal/Jam Spesimen'
    );
    $datetime_hasil = normalizeDateTimeInput(
        $_POST['tanggal_hasil'] ?? '',
        $_POST['jam_hasil'] ?? '',
        'Tanggal/Jam Hasil'
    );

    // Validasi data laboratorium harus ada
    $stmtCek = $Conn->prepare("SELECT id_laboratorium FROM laboratorium WHERE id_laboratorium = ? LIMIT 1");
    if (!$stmtCek) {
        fail('Gagal menyiapkan query validasi data laboratorium');
    }
    $stmtCek->bind_param("s", $id_laboratorium);
    if (!$stmtCek->execute()) {
        $stmtCek->close();
        fail('Gagal memvalidasi data laboratorium');
    }
    $resultCek = $stmtCek->get_result();
    if ($resultCek->num_rows === 0) {
        $stmtCek->close();
        fail('Data pemeriksaan laboratorium tidak ditemukan');
    }
    $stmtCek->close();

    // Update log datetime pemeriksaan
    $stmtUpdate = $Conn->prepare("
        UPDATE laboratorium
        SET
            datetime_diminta  = ?,
            datetime_diterima = ?,
            datetime_spesimen = ?,
            datetime_hasil    = ?
        WHERE id_laboratorium = ?
    ");
    if (!$stmtUpdate) {
        fail('Gagal menyiapkan query update log laboratorium');
    }

    $stmtUpdate->bind_param(
        "sssss",
        $datetime_diminta,
        $datetime_diterima,
        $datetime_spesimen,
        $datetime_hasil,
        $id_laboratorium
    );

    if (!$stmtUpdate->execute()) {
        $stmtUpdate->close();
        fail('Gagal memperbarui log pemeriksaan laboratorium');
    }
    $stmtUpdate->close();
    $Conn->close();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Log pemeriksaan laboratorium berhasil diperbarui'
    ]);
    exit;
?>
