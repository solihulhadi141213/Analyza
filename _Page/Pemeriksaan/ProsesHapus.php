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
        echo json_encode(['status'  => 'error','message' => 'ID Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium = $_POST['id_laboratorium'];

    // Hapus Data
    $HapusLaboratorium = mysqli_query($Conn, "DELETE FROM laboratorium WHERE id_laboratorium='$id_laboratorium'") or die(mysqli_error($Conn));
    if ($HapusLaboratorium) {
        echo json_encode(['status'  => 'success','message' => 'Hapus Pemeriksaan Berhasil']);
        exit; 
    }else{
        echo json_encode(['status'  => 'error','message' => 'Hapus Pemeriksaan Gagal!']);
        exit; 
    }
?>