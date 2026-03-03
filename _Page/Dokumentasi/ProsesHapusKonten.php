<?php
    header('Content-Type: application/json');

    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // Validasi Data Wajib Ada (Mandatory)
    $id_dokumentasi_content = validateAndSanitizeInput($_POST['id_dokumentasi_content'] ?? '');
    $id_dokumentasi = validateAndSanitizeInput($_POST['id_dokumentasi'] ?? '');

    if (empty($id_dokumentasi_content)) {
        $response['message'] = 'ID Konten dokumentasi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }
    if (empty($id_dokumentasi)) {
        $response['message'] = 'ID Konten dokumentasi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    // Validasi Keberadaan Data
    $QryCheck = $Conn->prepare("SELECT * FROM dokumentasi_content WHERE id_dokumentasi_content = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_dokumentasi_content);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Konten Dokumentasi tidak ditemukan.';
        echo json_encode($response);
        exit;
    }
    // Buat Variabel
    $type_content  = $Data['type_content'];
    $order_content = $Data['order_content'];
    $value_content = $Data['value_content'];

    // Hapus file fisik (jika tipe konten file)
    $filePath = '';
    if($type_content=="image"){
        $filePath = __DIR__ . "/../../assets/Dokumentasi/image/" . $value_content;
    }
    if($type_content=="video"){
        $filePath = __DIR__ . "/../../assets/Dokumentasi/video/" . $value_content;
    }

    if(!empty($filePath) && is_file($filePath)){
        if(!unlink($filePath)){
            $response['message'] = 'File gagal dihapus dari server';
            echo json_encode($response);
            exit;
        }
    }
    $QryDelete = $Conn->prepare("
        DELETE FROM dokumentasi_content 
        WHERE id_dokumentasi_content = ?
    ");
    $QryDelete->bind_param("i", $id_dokumentasi_content);
    if (!$QryDelete->execute()) {
        $response['message'] = 'Gagal menghapus data';
        echo json_encode($response);
        exit;
    }
    $QryDelete->close();
    $response['status']  = 'success';
    $response['id']  = $id_dokumentasi;
    $response['message'] = 'Hapus Konten Dokumentasi Berhasil Beserta File.';
   
    // Response JSON
    echo json_encode($response);
?>
