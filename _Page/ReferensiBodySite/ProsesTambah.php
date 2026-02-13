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
    if(empty($_POST['body_site_nama'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Lokasi Tubuh (Body Site) Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['body_site_display'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Display Lokasi Tubuh (Body Site) Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['body_site_code'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Kode Lokasi Tubuh (Body Site) Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['body_site_system'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi System Lokasi Tubuh (Body Site) Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $body_site_nama      = $_POST['body_site_nama'];
    $body_site_display   = $_POST['body_site_display'];
    $body_site_code      = $_POST['body_site_code'];
    $body_site_system    = $_POST['body_site_system'];

    // Validasi Duplikat Data body_site_code
    $validasi_duplikat_data = GetDetailData($Conn, 'referensi_body_site', 'body_site_code', $body_site_code, 'id_referensi_body_site');
    if(!empty($validasi_duplikat_data)){
        echo json_encode(['status'  => 'error','message' => 'Kode Lokasi Tubuh (Body Site) Yang Anda Gunakan Sudah Terdaftar']);
        exit;
    }

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_body_site (
            body_site_nama,
            body_site_display,
            body_site_code,
            body_site_system
        ) VALUES (?,?,?,?)
    ");

    $query->bind_param(
        "ssss",
        $body_site_nama,
        $body_site_display,
        $body_site_code,
        $body_site_system
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Lokasi Tubuh (Body Site) berhasil disimpan'
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