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
    $id_laboratorium_spesimen = validateAndSanitizeInput($_POST['id_laboratorium_spesimen'] ?? '');

    if (empty($id_laboratorium_spesimen)) {
        $response['message'] = 'ID Spesimen Pemeriksaan tidak valid.';
        echo json_encode($response);
        exit;
    }
    // Proses Hapus Data Dari Database
    $Conn->begin_transaction();
    try {

        $QryDelete = $Conn->prepare("DELETE FROM laboratorium_spesimen WHERE id_laboratorium_spesimen = ?");
        if (!$QryDelete) {
            throw new Exception($Conn->error);
        }
        $QryDelete->bind_param("i", $id_laboratorium_spesimen);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data.');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Hapus Data Spesimen Berhasil.';

    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    // Response JSON
    echo json_encode($response);
?>
