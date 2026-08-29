<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");
    $datetime_hasil = date('Y-m-d H:i');

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
     * Ubah input menjadi integer nullable untuk kolom FK.
     * Nilai kosong / bukan angka dianggap NULL.
     */
    function nullableInt($value) {
        if ($value === null) {
            return null;
        }
        $value = trim((string)$value);
        if ($value === '' || strtolower($value) === 'null') {
            return null;
        }
        if (!ctype_digit($value)) {
            return null;
        }
        return (int)$value;
    }

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        fail('Sesi Akses Sudah Berakhir! Silahkan Login Ulang!');
    }

    if (empty($_POST['id_laboratorium_rincian'])) {
        fail('ID Rincian Pemeriksaan Tidak Boleh Kosong!');
    }

    if (empty($_POST['id_referensi_metode_pemeriksaan'])) {
        fail('Referensi Metode Pemeriksaan Tidak Boleh Kosong!');
    }

    // Membuat Variabel Dan Sanitasi
    $id_laboratorium_rincian         = nullableInt($_POST['id_laboratorium_rincian'] ?? null);
    $id_referensi_category           = nullableInt($_POST['id_referensi_category'] ?? null);
    $id_referensi_range              = nullableInt($_POST['id_referensi_range'] ?? null);
    $id_referensi_metode_pemeriksaan = nullableInt($_POST['id_referensi_metode_pemeriksaan'] ?? null);
    
    if ($id_laboratorium_rincian === null) {
        fail('ID Rincian Pemeriksaan tidak valid.');
    }
   
    // Jika ada keterangan
    if(!empty($_POST['hasil'])){
        $hasil     = validateAndSanitizeInput($_POST['hasil']);
    }else{
        $hasil     = "";
    }
    
    if(!empty($_POST['hasil_interpertasi'])){
        $hasil_interpertasi     = validateAndSanitizeInput($_POST['hasil_interpertasi']);
    }else{
        $hasil_interpertasi     = "";
    }
    if(!empty($_POST['hasil_conclusion'])){
        $hasil_conclusion     = validateAndSanitizeInput($_POST['hasil_conclusion']);
    }else{
        $hasil_conclusion     = "";
        
    }
    if(!empty($_POST['hasil_keterangan'])){
        $hasil_keterangan     = validateAndSanitizeInput($_POST['hasil_keterangan']);
    }else{
        $hasil_keterangan     = "";
    }

    // Metode Pemeriksaan
    if(!empty($_POST['metode_pemeriksaan'])){
        $metode_pemeriksaan = validateAndSanitizeInput($_POST['metode_pemeriksaan']);
    }else{
        $metode_pemeriksaan = "";
    }
    if(!empty($_POST['metode_pemeriksaan_display'])){
        $metode_pemeriksaan_display = validateAndSanitizeInput($_POST['metode_pemeriksaan_display']);
    }else{
        $metode_pemeriksaan_display = "";
    }
    if(!empty($_POST['metode_pemeriksaan_code'])){
        $metode_pemeriksaan_code = validateAndSanitizeInput($_POST['metode_pemeriksaan_code']);
    }else{
        $metode_pemeriksaan_code = "";
    }
    if(!empty($_POST['metode_pemeriksaan_system'])){
        $metode_pemeriksaan_system = validateAndSanitizeInput($_POST['metode_pemeriksaan_system']);
    }else{
        $metode_pemeriksaan_system = "";
    }

    // Update Ke Database 'laboratorium_rincian'
    $query = $Conn->prepare("UPDATE laboratorium_rincian SET
            id_referensi_category = ?,
            id_referensi_range = ?,
            metode_pemeriksaan = ?,
            metode_pemeriksaan_display = ?,
            metode_pemeriksaan_code = ?,
            metode_pemeriksaan_system = ?,
            hasil = ?,
            interpertasi = ?,
            conclusion = ?,
            keterangan = ?
        WHERE id_laboratorium_rincian = ?
    ");

    if (!$query) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query update'
        ]);
        exit;
    }

    $query->bind_param(
        "iissssssssi",
        $id_referensi_category,
        $id_referensi_range,
        $metode_pemeriksaan,
        $metode_pemeriksaan_display,
        $metode_pemeriksaan_code,
        $metode_pemeriksaan_system,
        $hasil,
        $hasil_interpertasi,
        $hasil_conclusion,
        $hasil_keterangan,
        $id_laboratorium_rincian
    );

    // Eksekusi
    if (!$query->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui rincian: ' . $query->error
        ]);
        exit;
    }
    $query->close();

    // Buka ID Lab
    $id_laboratorium = GetDetailData($Conn, 'laboratorium_rincian', 'id_laboratorium_rincian', $id_laboratorium_rincian, 'id_laboratorium');

    // Update Waktu Keluar hasil
    $status = "Keluar Hasil";
    $QryUpdateLab = $Conn->prepare("UPDATE laboratorium SET datetime_hasil = ?, status = ? WHERE id_laboratorium = ?");
    if (!$QryUpdateLab) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal Mempersiapkan Query Update Ke Tabel Laboratorium'
        ]);
        exit;
    }
    $QryUpdateLab->bind_param(
        "sss",
        $datetime_hasil,
        $status,
        $id_laboratorium
    );

    // Eksekusi
    if (!$QryUpdateLab->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal Update Tabel Laboratorium: ' . $QryUpdateLab->error
        ]);
        exit;
    }

    // Response Berhasil
    echo json_encode([
        'status'  => 'success',
        'message' => 'Rincian Hasil Berhasil Disimpan'
    ]);
    $QryUpdateLab->close();
?>
