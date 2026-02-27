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
    $id_laboratorium = $_POST['id_laboratorium'];
    
    // Menangkap Data Yang Tidak Wajib
    if(empty($_POST['keterangan'])){
        $keterangan = "";
    }else{
        $keterangan = $_POST['keterangan'];
    }

    // Update Ke Database laboratorium
    $query = $Conn->prepare("
        UPDATE laboratorium SET
            keterangan     = ?
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
        "ss",
        $keterangan,
        $id_laboratorium
    );

    // Eksekusi
    if (!$query->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data laboratorium: ' . $query->error
        ]);
        $query->close();
        $Conn->close();
        exit;
    } 
    echo json_encode([
        'status'  => 'success',
        'message' => 'Pemeriksaan Laboratorium Berhasil Diperbaharui'
    ]);
    $query->close();
    $Conn->close();
?>