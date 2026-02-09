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
    if(empty($_POST['nama_metode_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['display_metode_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Display Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['code_metode_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Kode Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['system_metode_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi System Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $nama_metode_pemeriksaan    = $_POST['nama_metode_pemeriksaan'];
    $display_metode_pemeriksaan = $_POST['display_metode_pemeriksaan'];
    $code_metode_pemeriksaan    = $_POST['code_metode_pemeriksaan'];
    $system_metode_pemeriksaan  = $_POST['system_metode_pemeriksaan'];

    // Validasi Duplikat Data code_metode_sample
    $validasi_duplikat_data = GetDetailData($Conn,'referensi_metode_pemeriksaan','code_metode_pemeriksaan',$code_metode_pemeriksaan,'id_referensi_metode_pemeriksaan');
    if(!empty($validasi_duplikat_data)){
        echo json_encode(['status'  => 'error','message' => 'Kode Metode Pemeriksaan Yang Anda Gunakan Sudah Terdaftar']);
        exit;
    }

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_metode_pemeriksaan (
            nama_metode_pemeriksaan,
            display_metode_pemeriksaan,
            code_metode_pemeriksaan,
            system_metode_pemeriksaan
        ) VALUES (?,?,?,?)
    ");

    $query->bind_param(
        "ssss",
        $nama_metode_pemeriksaan,
        $display_metode_pemeriksaan,
        $code_metode_pemeriksaan,
        $system_metode_pemeriksaan
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Metode Pemeriksaan berhasil disimpan'
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
