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
    if(empty($_POST['id_laboratorium'])){
        echo json_encode(['status'  => 'error','message' => 'ID Laboratorium Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['id_pasien'])){
        echo json_encode(['status'  => 'error','message' => 'ID Pasien Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['nama'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Pasien Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium = $_POST['id_laboratorium'];
    $id_pasien = $_POST['id_pasien'];
    $nama = $_POST['nama'];
    
    // Menangkap Data Yang Tidak Wajib
    if(empty($_POST['ihs_pasien'])){
        $ihs_pasien = "";
    }else{
        $ihs_pasien = $_POST['ihs_pasien'];
    }
    if(empty($_POST['gender'])){
        $gender = "";
    }else{
        $gender = $_POST['gender'];
    }
     if(empty($_POST['tanggal_lahir'])){
        $tanggal_lahir = "";
    }else{
        $tanggal_lahir = $_POST['tanggal_lahir'];
    }


    // Update Ke Database laboratorium
    $query = $Conn->prepare("
        UPDATE laboratorium SET
            nama          = ?,
            ihs_pasien    = ?,
            gender        = ?,
            tanggal_lahir = ?
        WHERE id_pasien = ?
    ");

    if (!$query) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query update: ' . $Conn->error
        ]);
        exit;
    }

    $query->bind_param(
        "ssssi",
        $nama,
        $ihs_pasien,
        $gender,
        $tanggal_lahir,
        $id_pasien
    );

    // Eksekusi
    if (!$query->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data pasien: ' . $query->error
        ]);
        $query->close();
        $Conn->close();
        exit;
    } 
    echo json_encode([
        'status'  => 'success',
        'message' => 'Informasi Pasien Berhasil Diperbaharui'
    ]);
    $query->close();
    $Conn->close();
?>