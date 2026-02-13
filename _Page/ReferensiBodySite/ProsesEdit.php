<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi ID Wajib
    if (empty($_POST['id_referensi_body_site'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Referensi Lokasi Tubuh (Body Site) Tidak Boleh Kosong!'
        ]);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if (empty($_POST['body_site_nama'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Nama Lokasi Tubuh (Body Site) Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST['body_site_display'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Referensi Display Lokasi Tubuh (Body Site) Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST['body_site_code'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Referensi Kode Lokasi Tubuh (Body Site) Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST['body_site_system'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Referensi System Lokasi Tubuh (Body Site) Tidak Boleh Kosong!'
        ]);
        exit;
    }

    // Variabel Input
    $id_referensi_body_site = validateAndSanitizeInput($_POST['id_referensi_body_site']);
    $body_site_nama = $_POST['body_site_nama'];
    $body_site_display = $_POST['body_site_display'];
    $body_site_code = $_POST['body_site_code'];
    $body_site_system = $_POST['body_site_system'];

    // Validasi Duplikat Code (kecuali data sendiri)
    $kode_lama = GetDetailData(
        $Conn,
        'referensi_body_site',
        'id_referensi_body_site',
        $id_referensi_body_site,
        'body_site_code'
    );

    if ($kode_lama != $body_site_code) {
        $duplikat = GetDetailData(
            $Conn,
            'referensi_body_site',
            'body_site_code',
            $body_site_code,
            'id_referensi_body_site'
        );

        if (!empty($duplikat)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Kode Lokasi Tubuh (Body Site) Yang Anda Gunakan Sudah Terdaftar'
            ]);
            exit;
        }
    }

    // Proses Update Data
    $query = $Conn->prepare("
        UPDATE referensi_body_site
        SET
            body_site_nama = ?,
            body_site_display = ?,
            body_site_code = ?,
            body_site_system = ?
        WHERE id_referensi_body_site = ?
    ");

    if (!$query) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query update'
        ]);
        exit;
    }

    $query->bind_param(
        "ssssi",
        $body_site_nama,
        $body_site_display,
        $body_site_code,
        $body_site_system,
        $id_referensi_body_site
    );

    // Eksekusi
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Lokasi Tubuh (Body Site) berhasil diperbarui'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data'
        ]);
    }

    $query->close();
    $Conn->close();
?>
