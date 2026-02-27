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
    if(empty($_POST['id_kunjungan'])){
        echo json_encode(['status'  => 'error','message' => 'ID Kunjungan Tidak Boleh Kosong!']);
        exit;
    }
    
    $id_laboratorium = $_POST['id_laboratorium'];
    $id_kunjungan    = $_POST['id_kunjungan'];
    
    // Menangkap Data Yang Tidak Wajib
    if(empty($_POST['id_encounter'])){
        $id_encounter = "";
    }else{
        $id_encounter = $_POST['id_encounter'];
    }
    if(empty($_POST['tujuan'])){
        $tujuan = "";
    }else{
        $tujuan = $_POST['tujuan'];
    }
     if(empty($_POST['pembayaran'])){
        $pembayaran = "";
    }else{
        $pembayaran = $_POST['pembayaran'];
    }


    // Update Ke Database laboratorium
    $query = $Conn->prepare("
        UPDATE laboratorium SET
            id_encounter = ?,
            tujuan       = ?,
            pembayaran   = ?
        WHERE id_laboratorium = ?
    ");

    if (!$query) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query update: ' . $Conn->error
        ]);
        exit;
    }

    $query->bind_param(
        "ssss",
        $id_encounter,
        $tujuan,
        $pembayaran,
        $id_laboratorium
    );

    // Eksekusi
    if (!$query->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data kunjungan: ' . $query->error
        ]);
        $query->close();
        $Conn->close();
        exit;
    } 
    echo json_encode([
        'status'  => 'success',
        'message' => 'Informasi kunjungan Berhasil Diperbaharui'
    ]);
    $query->close();
    $Conn->close();
?>