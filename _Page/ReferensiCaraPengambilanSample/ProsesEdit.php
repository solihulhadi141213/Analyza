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

    // Validasi ID Wajib
    if(empty($_POST['id_referensi_metode_sample'])){
        echo json_encode(['status' => 'error', 'message' => 'ID Metode Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if(empty($_POST['nama_metode_sample'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Metode Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['display_metode_sample'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Display Metode Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['code_metode_sample'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Kode Metode Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['system_metode_sample'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi System Metode Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    // Variabel Input
    $id_referensi_metode_sample = $_POST['id_referensi_metode_sample'];
    $nama_metode_sample         = $_POST['nama_metode_sample'];
    $display_metode_sample      = $_POST['display_metode_sample'];
    $code_metode_sample         = $_POST['code_metode_sample'];
    $system_metode_sample       = $_POST['system_metode_sample'];
    // ======================================================
    // Validasi Duplikat Code (kecuali data sendiri)
    // ======================================================
    $kode_lama = GetDetailData($Conn, 'referensi_metode_sample', 'id_referensi_metode_sample', $id_referensi_metode_sample, 'code_metode_sample');

    if ($kode_lama != $code_metode_sample) {
        $duplikat = GetDetailData($Conn, 'referensi_metode_sample', 'code_metode_sample', $code_metode_sample, 'id_referensi_metode_sample');

        if (!empty($duplikat)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Kode Metode Spesimen Yang Anda Gunakan Sudah Terdaftar'
            ]);
            exit;
        }
    }

    // ======================================================
    // PROSES UPDATE DATA
    // ======================================================
    $query = $Conn->prepare("
        UPDATE referensi_metode_sample 
        SET 
            nama_metode_sample = ?,
            display_metode_sample = ?,
            code_metode_sample = ?,
            system_metode_sample = ?
        WHERE id_referensi_metode_sample = ?
    ");

    $query->bind_param(
        "ssssi",
        $nama_metode_sample,
        $display_metode_sample,
        $code_metode_sample,
        $system_metode_sample,
        $id_referensi_metode_sample
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Metode Spesimen berhasil diperbarui'
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