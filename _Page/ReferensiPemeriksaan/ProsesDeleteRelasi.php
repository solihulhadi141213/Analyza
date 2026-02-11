<?php
    /* Header JSON */
    header('Content-Type: application/json');

    // koneksi dan session
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

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

    // Validasi Data Wajib
    $id_referensi_pemeriksaan_relasi = validateAndSanitizeInput($_POST['id_referensi_pemeriksaan_relasi'] ?? '');
    if (empty($id_referensi_pemeriksaan_relasi)) {
        $response['message'] = 'ID Relasi tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Validasi Keberadaan Data
    $QryCheck = $Conn->prepare("SELECT id_referensi_pemeriksaan_relasi FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan_relasi = ?");
    if (!$QryCheck) {
        $response['message'] = $Conn->error;
        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_referensi_pemeriksaan_relasi);
    $QryCheck->execute();
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Relasi tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // Proses Hapus
    $Conn->begin_transaction();
    try {
        $QryDelete = $Conn->prepare("DELETE FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan_relasi = ?");
        if (!$QryDelete) {
            throw new Exception($Conn->error);
        }

        $QryDelete->bind_param("i", $id_referensi_pemeriksaan_relasi);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data.');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Hapus Data Relasi Berhasil.';
    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
?>
