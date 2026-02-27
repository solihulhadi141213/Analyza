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
    if(empty($_POST['kode_dokter_pengirim'])){
        echo json_encode(['status'  => 'error','message' => 'Kode Dokter Pengirim Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['nama_dokter_pengirim'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Dokter Pengirim Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['kode_dokter_penerima'])){
        echo json_encode(['status'  => 'error','message' => 'Kode Dokter Penerima Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['nama_dokter_penerima'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Dokter Penerima Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium = $_POST['id_laboratorium'];
    $kode_dokter_pengirim = $_POST['kode_dokter_pengirim'];
    $nama_dokter_pengirim = $_POST['nama_dokter_pengirim'];

    // variabel Tidak Wajib
    if(empty($_POST['ihs_dokter_pengirim'])){
       $ihs_dokter_pengirim = "";
    }else{
        $ihs_dokter_pengirim = $_POST['ihs_dokter_pengirim'];
    }
    if(empty($_POST['ihs_dokter_penerima'])){
       $ihs_dokter_penerima = "";
    }else{
        $ihs_dokter_penerima = $_POST['ihs_dokter_penerima'];
    }
    if(empty($_POST['kode_dokter_penerima'])){
       $kode_dokter_penerima = "";
    }else{
        $kode_dokter_penerima = $_POST['kode_dokter_penerima'];
    }
    if(empty($_POST['nama_dokter_penerima'])){
       $nama_dokter_penerima = "";
    }else{
        $nama_dokter_penerima = $_POST['nama_dokter_penerima'];
    }

    // Update Ke Database laboratorium
    $query = $Conn->prepare("
        UPDATE laboratorium SET
            kode_dokter_pengirim = ?,
            ihs_dokter_pengirim = ?,
            nama_dokter_pengirim  = ?,
            kode_dokter_penerima  = ?,
            ihs_dokter_penerima = ?,
            nama_dokter_penerima = ?
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
        "sssssss",
        $kode_dokter_pengirim,
        $ihs_dokter_pengirim,
        $nama_dokter_pengirim,
        $kode_dokter_penerima,
        $ihs_dokter_penerima,
        $nama_dokter_penerima,
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