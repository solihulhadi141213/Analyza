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
    $id_referensi_metode_pemeriksaan = validateAndSanitizeInput($_POST['id_referensi_metode_pemeriksaan'] ?? '');

    if (empty($id_referensi_metode_pemeriksaan)) {
        $response['message'] = 'ID Referensi tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Validasi Keberadaan Data
    $QryCheck = $Conn->prepare("SELECT id_referensi_metode_pemeriksaan FROM referensi_metode_pemeriksaan WHERE id_referensi_metode_pemeriksaan = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_referensi_metode_pemeriksaan);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Referensi tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // Proses Hapus Data Dari Database
    $Conn->begin_transaction();

    try {

        $QryDelete = $Conn->prepare("DELETE FROM referensi_metode_pemeriksaan WHERE id_referensi_metode_pemeriksaan = ?");
        if (!$QryDelete) {
            throw new Exception($Conn->error);
        }
        $QryDelete->bind_param("i", $id_referensi_metode_pemeriksaan);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data.');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Hapus Data Referensi Berhasil.';

    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    // Response JSON
    echo json_encode($response);
?>
