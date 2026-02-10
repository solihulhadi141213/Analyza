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
    if(empty($_POST['nama_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['category_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Kategori Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['code_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Kode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['display_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Display Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['system_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi System FHIR Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['result_type'])){
        echo json_encode(['status'  => 'error','message' => 'Tipe Hasil Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['result_interpertation_type'])){
        echo json_encode(['status'  => 'error','message' => 'Tipe Interpertasi Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $nama_pemeriksaan           = $_POST['nama_pemeriksaan'];
    $category_pemeriksaan       = $_POST['category_pemeriksaan'];
    $code_pemeriksaan           = $_POST['code_pemeriksaan'];
    $display_pemeriksaan        = $_POST['display_pemeriksaan'];
    $system_pemeriksaan         = $_POST['system_pemeriksaan'];
    $result_type                = $_POST['result_type'];
    $result_interpertation_type = $_POST['result_interpertation_type'];

    // Allow Age & Sex
    if(empty($_POST['allow_age'])){
        $allow_age = false;
    }else{
        $allow_age = true;
    }

    if(empty($_POST['allow_sex'])){
        $allow_sex = false;
    }else{
        $allow_sex = true;
    }

    // Satuan (Jika id_referensi_satuan ditangkap)
    if(empty($_POST['id_referensi_satuan'])){
        $id_referensi_satuan = "";
        $unit                = "";
        $unit_display        = "";
        $unit_code           = "";
        $unit_system         = "";
    }else{
        $id_referensi_satuan = $_POST['id_referensi_satuan'];
        $unit                = GetDetailData($Conn, 'referensi_satuan', 'id_referensi_satuan', $id_referensi_satuan, 'nama_satuan');
        $unit_display        = GetDetailData($Conn, 'referensi_satuan', 'id_referensi_satuan', $id_referensi_satuan, 'unit_satuan');
        $unit_code           = GetDetailData($Conn, 'referensi_satuan', 'id_referensi_satuan', $id_referensi_satuan, 'code_satuan');
        $unit_system         = GetDetailData($Conn, 'referensi_satuan', 'id_referensi_satuan', $id_referensi_satuan, 'system_satuan');
    }

    // Validasi Nilai 'result_type'
    $enum_result_type = ['Numeric','Decimal','Coded','Text','Boolean'];
    if (!in_array($result_type, $enum_result_type)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Tipe hasil tidak valid'
        ]);
        exit;
    }

    // Validasi Nilai 'interpertation_type'
    $enum_interpertation_type = ['Range','Category','None'];
    if (!in_array($result_interpertation_type, $enum_interpertation_type)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Tipe Interpertasi tidak valid'
        ]);
        exit;
    }

    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_pemeriksaan (
            nama_pemeriksaan,
            category_pemeriksaan,
            code_pemeriksaan,
            display_pemeriksaan,
            system_pemeriksaan,
            unit,
            unit_display,
            unit_code,
            unit_system,
            result_type,
            result_interpertation_type,
            allow_age,
            allow_sex
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $query->bind_param(
        "sssssssssssss",
        $nama_pemeriksaan,
        $category_pemeriksaan,
        $code_pemeriksaan,
        $display_pemeriksaan,
        $system_pemeriksaan,
        $unit,
        $unit_display,
        $unit_code,
        $unit_system,
        $result_type,
        $result_interpertation_type,
        $allow_age,
        $allow_sex
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi pemeriksaan berhasil disimpan'
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