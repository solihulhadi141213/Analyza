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
    if(empty($_POST['nama_container'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Kontainer Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['display_container'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Display Kontainer Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['code_container'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Kode Kontainer Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['system_container'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi System Kontainer Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['kapasitas_container'])){
        echo json_encode(['status'  => 'error','message' => 'Informasi Kapasitas Kontainer Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['unit_container'])){
        echo json_encode(['status'  => 'error','message' => 'Satuan Unit Kontainer Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $nama_container      = $_POST['nama_container'];
    $display_container   = $_POST['display_container'];
    $code_container      = $_POST['code_container'];
    $system_container    = $_POST['system_container'];
    $kapasitas_container = $_POST['kapasitas_container'];
    $unit_container      = $_POST['unit_container'];

    // Validasi Duplikat Data code_container
    $validasi_duplikat_data = GetDetailData($Conn, 'referensi_container', 'code_container', $code_container, 'id_referensi_container');
    if(!empty($validasi_duplikat_data)){
        echo json_encode(['status'  => 'error','message' => 'Kode Kontainer Yang Anda Gunakan Sudah Terdaftar']);
        exit;
    }

    // Buka Detail Unit Satuan
    $Qry = $Conn->prepare("SELECT * FROM referensi_satuan WHERE id_referensi_satuan = ?");
    $Qry->bind_param("i", $unit_container);

    if (!$Qry->execute()) {
        echo json_encode(['status'  => 'error','message' => 'Unit satuan yang anda pilih tidak terdaftar!']);
        exit;
    }

    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    if (!$Data) {
        echo json_encode(['status'  => 'error','message' => 'Unit satuan yang anda pilih tidak terdaftar!']);
        exit;
    }
    $unit_satuan         = $Data['unit_satuan'];
    $code_satuan         = $Data['code_satuan'];
    $system_satuan       = $Data['system_satuan'];

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_container (
            nama_container,
            display_container,
            code_container,
            system_container,
            kapasitas_container,
            unit_container,
            code_unit_container,
            system_unit_container
        ) VALUES (?,?,?,?,?,?,?,?)
    ");

    $query->bind_param(
        "ssssssss",
        $nama_container,
        $display_container,
        $code_container,
        $system_container,
        $kapasitas_container,
        $unit_satuan,
        $code_satuan,
        $system_satuan
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi kontainer berhasil disimpan'
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