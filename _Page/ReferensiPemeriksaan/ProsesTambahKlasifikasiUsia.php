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
    if(empty($_POST['id_referensi_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'ID Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['umur_kategori'])){
        $umur_kategori = "";
    }else{
        $umur_kategori = $_POST['umur_kategori'];
    }
    if(empty($_POST['umur_min'])){
        $umur_min = 0;
    }else{
        $umur_min = $_POST['umur_min'];
    }
    if(empty($_POST['umur_max'])){
        $umur_max = 0;
    }else{
        $umur_max = $_POST['umur_max'];
    }
    if(empty($_POST['umur_unit'])){
        $umur_unit = "";
    }else{
        $umur_unit = $_POST['umur_unit'];
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
    $id_referensi_pemeriksaan = $_POST['id_referensi_pemeriksaan'];

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_usia (
            id_referensi_pemeriksaan,
            umur_kategori,
            umur_min,
            umur_max,
            umur_unit
        ) VALUES (?,?,?,?,?)
    ");

    $query->bind_param(
        "issss",
        $id_referensi_pemeriksaan,
        $umur_kategori,
        $umur_min,
        $umur_max,
        $umur_unit
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi usia berhasil disimpan'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan data'
        ]);
    }

    $query->close();
    $Conn->close();

?>