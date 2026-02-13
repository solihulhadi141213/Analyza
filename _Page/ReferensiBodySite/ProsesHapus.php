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
        $response['message'] = 'Sesi akses telah berakhir. Silakan login ulang.';
        echo json_encode($response);
        exit;
    }

    // Validasi ID Wajib
    $id_referensi_body_site = validateAndSanitizeInput($_POST['id_referensi_body_site'] ?? '');
    if (empty($id_referensi_body_site)) {
        $response['message'] = 'ID Referensi Lokasi Tubuh (Body Site) tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Validasi Keberadaan Data
    $QryCheck = $Conn->prepare("SELECT id_referensi_body_site FROM referensi_body_site WHERE id_referensi_body_site = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_referensi_body_site);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Referensi Lokasi Tubuh (Body Site) tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // Proses Hapus Data
    $Conn->begin_transaction();

    try {
        $QryDelete = $Conn->prepare("DELETE FROM referensi_body_site WHERE id_referensi_body_site = ?");
        if (!$QryDelete) {
            throw new Exception($Conn->error);
        }

        $QryDelete->bind_param("i", $id_referensi_body_site);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data Referensi Lokasi Tubuh (Body Site).');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status'] = 'success';
        $response['message'] = 'Data Referensi Lokasi Tubuh (Body Site) berhasil dihapus.';
    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
?>
