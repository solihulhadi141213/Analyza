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

    // Validasi ID Wajib (Primary Key integer)
    $id_referensi_usia_raw = $_POST['id_referensi_usia'] ?? '';
    $id_referensi_usia_raw = validateAndSanitizeInput($id_referensi_usia_raw);

    if ($id_referensi_usia_raw === '' || !is_numeric($id_referensi_usia_raw) || (int)$id_referensi_usia_raw <= 0) {
        $response['message'] = 'ID Klasifikasi Usia tidak valid.';
        echo json_encode($response);
        exit;
    }
    $id_referensi_usia = (int)$id_referensi_usia_raw;

    // Cek data referensi usia
    $QryCheck = $Conn->prepare("SELECT id_referensi_usia FROM referensi_usia WHERE id_referensi_usia = ?");
    if (!$QryCheck) {
        $response['message'] = 'Gagal mempersiapkan query validasi data.';
        echo json_encode($response);
        exit;
    }
    $QryCheck->bind_param("i", $id_referensi_usia);
    if (!$QryCheck->execute()) {
        $response['message'] = 'Terjadi kesalahan saat memvalidasi data.';
        $QryCheck->close();
        echo json_encode($response);
        exit;
    }
    $Result = $QryCheck->get_result();
    $Data   = $Result->fetch_assoc();
    $QryCheck->close();

    if (!$Data) {
        $response['message'] = 'Data Klasifikasi Usia tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    // Proses hapus data
    $Conn->begin_transaction();
    try {
        $QryDelete = $Conn->prepare("DELETE FROM referensi_usia WHERE id_referensi_usia = ?");
        if (!$QryDelete) {
            throw new Exception('Gagal mempersiapkan query hapus data.');
        }

        $QryDelete->bind_param("i", $id_referensi_usia);
        if (!$QryDelete->execute()) {
            throw new Exception('Gagal menghapus data klasifikasi usia.');
        }

        $QryDelete->close();
        $Conn->commit();

        $response['status']  = 'success';
        $response['message'] = 'Hapus Data Klasifikasi Usia Berhasil.';
    } catch (Exception $e) {
        $Conn->rollback();
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
?>
