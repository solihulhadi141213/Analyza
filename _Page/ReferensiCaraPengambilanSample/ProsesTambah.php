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

    // Buat Variabel
    $nama_metode_sample      = $_POST['nama_metode_sample'];
    $display_metode_sample   = $_POST['display_metode_sample'];
    $code_metode_sample      = $_POST['code_metode_sample'];
    $system_metode_sample    = $_POST['system_metode_sample'];

    // Validasi Duplikat Data code_metode_sample
    $validasi_duplikat_data = GetDetailData($Conn, 'referensi_metode_sample', 'code_metode_sample', $code_metode_sample, 'id_referensi_metode_sample');
    if(!empty($validasi_duplikat_data)){
        echo json_encode(['status'  => 'error','message' => 'Kode Metode Spesimen Yang Anda Gunakan Sudah Terdaftar']);
        exit;
    }

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_metode_sample (
            nama_metode_sample,
            display_metode_sample,
            code_metode_sample,
            system_metode_sample
        ) VALUES (?,?,?,?)
    ");

    $query->bind_param(
        "ssss",
        $nama_metode_sample,
        $display_metode_sample,
        $code_metode_sample,
        $system_metode_sample
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Metode Spesimen berhasil disimpan'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan data'
        ]);
    }

    $query->close();
    $Conn->close();
?>