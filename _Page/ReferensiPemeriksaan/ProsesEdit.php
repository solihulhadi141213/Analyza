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

    // List kolom wajib (Mandatory) untuk divalidasi dalam loop agar lebih rapi
    $required_fields = [
        'id_referensi_pemeriksaan'   => 'ID Pemeriksaan',
        'nama_pemeriksaan'           => 'Nama Pemeriksaan',
        'category_pemeriksaan'       => 'Kategori Pemeriksaan',
        'code_pemeriksaan'           => 'Referensi Kode Pemeriksaan',
        'display_pemeriksaan'        => 'Referensi Display Pemeriksaan',
        'system_pemeriksaan'         => 'Referensi System FHIR Pemeriksaan',
        'result_type'                => 'Tipe Hasil Pemeriksaan',
        'result_interpertation_type' => 'Interpertasi Pemeriksaan'
    ];

    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            echo json_encode(['status' => 'error', 'message' => "$label Tidak Boleh Kosong!"]);
            exit;
        }
    }

    // Buat Variabel dari POST
    $id_referensi_pemeriksaan   = $_POST['id_referensi_pemeriksaan'];
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
        echo json_encode(['status' => 'error', 'message' => 'Tipe hasil tidak valid']);
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

    // --- MULAI PREPARED STATEMENT ---

    // 1. Siapkan Query dengan Placeholder (?)
    $sql = "UPDATE referensi_pemeriksaan SET 
                nama_pemeriksaan           = ?,
                category_pemeriksaan       = ?,
                code_pemeriksaan           = ?,
                display_pemeriksaan        = ?,
                system_pemeriksaan         = ?,
                unit                       = ?,
                unit_display               = ?,
                unit_code                  = ?,
                unit_system                = ?,
                result_type                = ?,
                result_interpertation_type = ?,
                allow_age                  = ?,
                allow_sex                  = ?
            WHERE id_referensi_pemeriksaan = ?";

    // 2. Inisialisasi statement
    $stmt = mysqli_prepare($Conn, $sql);

    if ($stmt) {
        /* 3. Bind Parameter 
           "sssssss" berarti ada 7 parameter bertipe string (s). 
           Sesuaikan jika 'id_referensi_pemeriksaan' adalah integer (i).
        */
        mysqli_stmt_bind_param($stmt, "sssssssssssiii", 
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
            $allow_sex, 
            $id_referensi_pemeriksaan
        );

        // 4. Eksekusi
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Data referensi pemeriksaan berhasil disimpan'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . mysqli_stmt_error($stmt)
            ]);
        }

        // 5. Tutup statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query (System Error)'
        ]);
    }

    // Tutup koneksi (opsional, tergantung struktur aplikasi Anda)
    mysqli_close($Conn);
?>