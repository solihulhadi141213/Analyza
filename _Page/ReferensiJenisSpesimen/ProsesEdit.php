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
    if(empty($_POST['id_referensi_jenis_spesimen'])){
        echo json_encode(['status' => 'error', 'message' => 'ID Jenis Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['nama_spesimen'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['display_spesimen'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Display Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['code_spesimen'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Kode Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['system_spesimen'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi System Spesimen Tidak Boleh Kosong!']);
        exit;
    }

    // Variabel Input
    $id_referensi_jenis_spesimen = $_POST['id_referensi_jenis_spesimen'];
    $nama_spesimen               = $_POST['nama_spesimen'];
    $display_spesimen            = $_POST['display_spesimen'];
    $code_spesimen               = $_POST['code_spesimen'];
    $system_spesimen             = $_POST['system_spesimen'];


    // ======================================================
    // Validasi Duplikat Code (kecuali data sendiri)
    // ======================================================
    $kode_lama = GetDetailData($Conn, 'referensi_jenis_spesimen', 'id_referensi_jenis_spesimen', $id_referensi_jenis_spesimen, 'code_spesimen');

    if ($kode_lama != $code_spesimen) {
        $duplikat = GetDetailData($Conn, 'referensi_jenis_spesimen', 'code_spesimen', $code_spesimen, 'id_referensi_jenis_spesimen');

        if (!empty($duplikat)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Kode Jenis Spesimen Yang Anda Gunakan Sudah Terdaftar'
            ]);
            exit;
        }
    }

    // ======================================================
    // PROSES UPDATE DATA
    // ======================================================
    $query = $Conn->prepare("
        UPDATE referensi_jenis_spesimen 
        SET 
            nama_spesimen = ?,
            display_spesimen = ?,
            code_spesimen = ?,
            system_spesimen = ?
        WHERE id_referensi_jenis_spesimen = ?
    ");

    $query->bind_param(
        "ssssi",
        $nama_spesimen,
        $display_spesimen,
        $code_spesimen,
        $system_spesimen,
        $id_referensi_jenis_spesimen
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Spesimen berhasil diperbarui'
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