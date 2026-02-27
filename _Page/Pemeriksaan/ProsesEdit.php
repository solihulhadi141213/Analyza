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
    if(empty($_POST['nama_pasien'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Pasien Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['fakses'])){
        echo json_encode(['status'  => 'error','message' => 'Faskes Pengirim Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['unit'])){
        echo json_encode(['status'  => 'error','message' => 'Unit Pengirim Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['id_kunjungan'])){
        echo json_encode(['status'  => 'error','message' => 'ID Kunjungan Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['tujuan'])){
        echo json_encode(['status'  => 'error','message' => 'Tujuan Kunjungan Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['pembayaran'])){
        echo json_encode(['status'  => 'error','message' => 'Metode Pembayaran Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['priority'])){
        echo json_encode(['status'  => 'error','message' => 'Prioritas Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['gender'])){
        echo json_encode(['status'  => 'error','message' => 'Gender Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium = $_POST['id_laboratorium'];
    $id_pasien       = $_POST['id_pasien'];
    $nama_pasien     = $_POST['nama_pasien'];
    $fakses          = $_POST['fakses'];
    $unit            = $_POST['unit'];
    $id_kunjungan    = $_POST['id_kunjungan'];
    $tujuan          = $_POST['tujuan'];
    $pembayaran      = $_POST['pembayaran'];
    $priority        = $_POST['priority'];
    $gender          = $_POST['gender'];
 
    // Menangkap Data Yang Tidak Wajib
    if(empty($_POST['ihs_pasien'])){
        $ihs_pasien = "";
    }else{
        $ihs_pasien = $_POST['ihs_pasien'];
    }
    if(empty($_POST['tanggal_lahir'])){
        $tanggal_lahir = "";
    }else{
        $tanggal_lahir = $_POST['tanggal_lahir'];
    }
    if(empty($_POST['id_encounter'])){
        $id_encounter = "";
    }else{
        $id_encounter = $_POST['id_encounter'];
    }

    // Update Ke Database laboratorium
    $query = $Conn->prepare("
        UPDATE laboratorium SET
            id_pasien     = ?,
            nama          = ?,
            fakses        = ?,
            unit          = ?,
            id_kunjungan  = ?,
            tujuan        = ?,
            pembayaran    = ?,
            priority      = ?,
            gender        = ?,
            ihs_pasien    = ?,
            tanggal_lahir = ?,
            id_encounter  = ?
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
        "sssssssssssss",
        $id_pasien,
        $nama_pasien,
        $fakses,
        $unit,
        $id_kunjungan,
        $tujuan,
        $pembayaran,
        $priority,
        $gender,
        $ihs_pasien,
        $tanggal_lahir,
        $id_encounter,
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
