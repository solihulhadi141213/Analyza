<?php
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
    $id_dokumentasi = validateAndSanitizeInput($_POST['id_dokumentasi'] ?? '');

    if (empty($id_dokumentasi)) {
        $response['message'] = 'ID dokumentasi Tidak Boleh Kosong!.';
        echo json_encode($response);
        exit;
    }

    // Validasi Keberadaan Data
    $QryCheck = $Conn->prepare("SELECT id_dokumentasi FROM dokumentasi WHERE id_dokumentasi = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_dokumentasi);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Dokumentasi tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // Proses Hapus Data Dari Database
    $Conn->begin_transaction();

    try {

        // ===============================
        // 1. Ambil semua konten terkait
        // ===============================
        $QryContent = $Conn->prepare("
            SELECT type_content, value_content 
            FROM dokumentasi_content 
            WHERE id_dokumentasi = ?
        ");
        $QryContent->bind_param("i", $id_dokumentasi);
        $QryContent->execute();
        $ResultContent = $QryContent->get_result();

        while($row = $ResultContent->fetch_assoc()){

            $type  = $row['type_content'];
            $value = $row['value_content'];

            // Hapus file image
            if($type == "image"){
                $path = "../../assets/Dokumentasi/image/".$value;
                if(file_exists($path)){
                    unlink($path);
                }
            }

            // Hapus file video
            if($type == "video"){
                $path = "../../assets/Dokumentasi/video/".$value;
                if(file_exists($path)){
                    unlink($path);
                }
            }
        }

        $QryContent->close();


        // ===============================
        // 2. Hapus isi dokumentasi_content
        // ===============================
        $QryDeleteContent = $Conn->prepare("
            DELETE FROM dokumentasi_content 
            WHERE id_dokumentasi = ?
        ");
        $QryDeleteContent->bind_param("i", $id_dokumentasi);
        if(!$QryDeleteContent->execute()){
            throw new Exception("Gagal menghapus konten dokumentasi");
        }
        $QryDeleteContent->close();


        // ===============================
        // 3. Hapus dokumentasi utama
        // ===============================
        $QryDelete = $Conn->prepare("
            DELETE FROM dokumentasi 
            WHERE id_dokumentasi = ?
        ");
        $QryDelete->bind_param("i", $id_dokumentasi);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data dokumentasi.');
        }
        $QryDelete->close();

        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Hapus Data Dokumentasi Berhasil Beserta File.';

    } catch (Exception $e) {

        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    // Response JSON
    echo json_encode($response);
?>
