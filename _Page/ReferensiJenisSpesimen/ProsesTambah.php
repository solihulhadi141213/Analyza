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

    // Buat Variabel
    $nama_spesimen    = $_POST['nama_spesimen'];
    $display_spesimen = $_POST['display_spesimen'];
    $code_spesimen    = $_POST['code_spesimen'];
    $system_spesimen  = $_POST['system_spesimen'];

    // Validasi Duplikat Data code_metode_sample
    $validasi_duplikat_data = GetDetailData($Conn, 'referensi_jenis_spesimen ', 'code_spesimen', $code_spesimen, 'id_referensi_jenis_spesimen ');
    if(!empty($validasi_duplikat_data)){
        echo json_encode(['status'  => 'error','message' => 'Kode Spesimen Yang Anda Gunakan Sudah Terdaftar']);
        exit;
    }

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_jenis_spesimen (
            nama_spesimen,
            display_spesimen,
            code_spesimen,
            system_spesimen
        ) VALUES (?,?,?,?)
    ");

    $query->bind_param(
        "ssss",
        $nama_spesimen,
        $display_spesimen,
        $code_spesimen,
        $system_spesimen
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Spesimen berhasil disimpan'
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