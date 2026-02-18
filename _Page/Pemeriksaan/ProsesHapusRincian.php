<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Response default
    $response = [
        'status'       => 'error',
        'message'      => 'Terjadi kesalahan sistem'
    ];

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // Validasi Data Wajib Ada (Mandatory)
    $id_laboratorium_rincian = validateAndSanitizeInput($_POST['id_laboratorium_rincian'] ?? '');

    if (empty($id_laboratorium_rincian)) {
        $response['message'] = 'ID Rincian Pemeriksaan tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Validasi Keberadaan Data
    $QryCheck = $Conn->prepare("SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_laboratorium_rincian);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Referensi Pemeriksaan tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // Proses Hapus Data Dari Database
    $Conn->begin_transaction();

    try {

        $QryDelete = $Conn->prepare("DELETE FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
        if (!$QryDelete) {
            throw new Exception($Conn->error);
        }
        $QryDelete->bind_param("i", $id_laboratorium_rincian);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data.');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Hapus Data Rincian Pemeriksaan Berhasil.';

    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    // Response JSON
    echo json_encode($response);
?>
