<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    /* Response default */
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if(empty($_POST['id_referensi_usia'])){
        echo json_encode(['status'  => 'error','message' => 'ID Klasifikasi Usia Tidak Boleh Kosong!']);
        exit;
    }
    if(!is_numeric($_POST['id_referensi_usia']) || (int)$_POST['id_referensi_usia'] <= 0){
        echo json_encode(['status'  => 'error','message' => 'ID Klasifikasi Usia tidak valid!']);
        exit;
    }
    if(empty($_POST['umur_kategori'])){
        $umur_kategori = "";
    }else{
        $umur_kategori = validateAndSanitizeInput($_POST['umur_kategori']);
    }
    if(empty($_POST['umur_min'])){
        $umur_min = 0;
    }else{
        $umur_min = (int) $_POST['umur_min'];
    }
    if(empty($_POST['umur_max'])){
        $umur_max = 0;
    }else{
        $umur_max = (int) $_POST['umur_max'];
    }
    if(empty($_POST['umur_unit'])){
        $umur_unit = "";
    }else{
        $umur_unit = validateAndSanitizeInput($_POST['umur_unit']);
    }
    

    // Validasi Data Wajib Ada (Mandatory)
    if(empty($umur_kategori)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Kategori Usia Tidak Boleh Kosong'
        ]);
        exit;
    }
    if(empty($umur_unit)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Unit Usia Tidak Boleh Kosong'
        ]);
        exit;
    }
    if(empty($umur_min) && empty($umur_max)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Rentang Usia Tidak Boleh Kosong'
        ]);
        exit;
    }
    $enum_umur_unit = ['Hari','Bulan','Tahun'];
    if (!in_array($umur_unit, $enum_umur_unit)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Tipe satuan usia tidak valid'
        ]);
        exit;
    }
    $id_referensi_usia = (int) $_POST['id_referensi_usia'];

    // Pastikan data yang akan diupdate ada
    $check = $Conn->prepare("SELECT id_referensi_usia FROM referensi_usia WHERE id_referensi_usia = ?");
    $check->bind_param("i", $id_referensi_usia);
    $check->execute();
    $result_check = $check->get_result();
    if ($result_check->num_rows === 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Data klasifikasi usia tidak ditemukan'
        ]);
        $check->close();
        $Conn->close();
        exit;
    }
    $check->close();

    // Update Data Ke Database
    $query = $Conn->prepare("
        UPDATE referensi_usia SET
            umur_kategori = ?,
            umur_min = ?,
            umur_max = ?,
            umur_unit = ?
        WHERE id_referensi_usia = ?
    ");
    if (!$query) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mempersiapkan query update'
        ]);
        $Conn->close();
        exit;
    }

    $query->bind_param(
        "siisi",
        $umur_kategori,
        $umur_min,
        $umur_max,
        $umur_unit,
        $id_referensi_usia
    );
    if (!$query->execute()) {
         echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan data'
        ]);
        exit;
    }
    $query->close();

    // Lanjut Update 'referensi_range'
    $query2 = $Conn->prepare("
        UPDATE  referensi_range SET
            umur_kategori = ?,
            umur_min = ?,
            umur_max = ?,
            umur_unit = ?
        WHERE id_referensi_usia = ?
    ");
    if (!$query2) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mempersiapkan query update'
        ]);
        $Conn->close();
        exit;
    }

    $query2->bind_param(
        "siisi",
        $umur_kategori,
        $umur_min,
        $umur_max,
        $umur_unit,
        $id_referensi_usia
    );
    if (!$query2->execute()) {
         echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal Update Data Ke Tabel referensi_range'
        ]);
        exit;
    }
    $query2->close();

    // Lanjut Update 'referensi_category'
    $query3 = $Conn->prepare("
        UPDATE  referensi_category SET
            umur_kategori = ?,
            umur_min = ?,
            umur_max = ?,
            umur_unit = ?
        WHERE id_referensi_usia = ?
    ");
    if (!$query3) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mempersiapkan query update'
        ]);
        $Conn->close();
        exit;
    }

    $query3->bind_param(
        "siisi",
        $umur_kategori,
        $umur_min,
        $umur_max,
        $umur_unit,
        $id_referensi_usia
    );
    if (!$query3->execute()) {
         echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal Update Data Ke Tabel referensi_category'
        ]);
        exit;
    }
    $query3->close();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Data referensi usia berhasil diperbarui'
    ]);
    $Conn->close();

?>
