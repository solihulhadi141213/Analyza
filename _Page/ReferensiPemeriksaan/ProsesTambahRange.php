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
    if(empty($_POST['id_referensi_pemeriksaan'])){
        echo json_encode(['status'  => 'error','message' => 'ID Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['operator'])){
        echo json_encode(['status'  => 'error','message' => 'Operator Nilai Tidak Boleh Kosong!']);
        exit;
    }

    if(empty($_POST['label_hasil'])){
        echo json_encode(['status'  => 'error','message' => 'Label Hasil Pemeriksaan Tidak Boleh Kosong2!']);
        exit;
    }

    // Buat Variabel
    $id_referensi_pemeriksaan = $_POST['id_referensi_pemeriksaan'];
    $operator                 = $_POST['operator'];
    $label                    = $_POST['label_hasil'];

    // Variabel Tidak Wajib
    if(empty($_POST['nilai_min'])){
        $nilai_min = 0;
    }else{
        $nilai_min = $_POST['nilai_min'];
    }
    if(empty($_POST['nilai_max'])){
        $nilai_max = 0;
    }else{
        $nilai_max = $_POST['nilai_max'];
    }
    if(empty($_POST['umur_kategori'])){
        $umur_kategori = "";
    }else{
        $umur_kategori = $_POST['umur_kategori'];
    }
    if(empty($_POST['umur_min'])){
        $umur_min = 0;
    }else{
        $umur_min = $_POST['umur_min'];
    }
    if(empty($_POST['umur_max'])){
        $umur_max = 0;
    }else{
        $umur_max = $_POST['umur_max'];
    }
    if(empty($_POST['umur_unit'])){
        $umur_unit = "";
    }else{
        $umur_unit = $_POST['umur_unit'];
    }
    if(empty($_POST['jenis_kelamin'])){
        $jenis_kelamin = "All";
    }else{
        $jenis_kelamin = $_POST['jenis_kelamin'];
    }
    if(empty($_POST['fhir_display'])){
        $fhir_display = "";
    }else{
        $fhir_display = $_POST['fhir_display'];
    }
    if(empty($_POST['fhir_code'])){
        $fhir_code = "";
    }else{
        $fhir_code = $_POST['fhir_code'];
    }
    if(empty($_POST['fhir_system'])){
        $fhir_system = "";
    }else{
        $fhir_system = $_POST['fhir_system'];
    }
    if(empty($_POST['conclusion'])){
        $conclusion = "";
    }else{
        $conclusion = $_POST['conclusion'];
    }

    // Validasi Nilai 'umur_unit'
    $enum_umur_unit = ['Hari','Bulan','Tahun'];
    if (!in_array($umur_unit, $enum_umur_unit)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Tipe satuan usia tidak valid'
        ]);
        exit;
    }

    // Validasi Nilai 'jenis_kelamin'
    $enum_jenis_kelamin = ['Laki-laki','Perempuan','All'];
    if (!in_array($jenis_kelamin, $enum_jenis_kelamin)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Jenis Kelamin tidak valid'
        ]);
        exit;
    }

    // Validasi Nilai 'operator'
    $enum_operator = ['<','>','between','<=','>=','-'];
    if (!in_array($operator, $enum_operator)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Jenis operator tidak valid'
        ]);
        exit;
    }

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
    $Qry->bind_param("i", $id_referensi_pemeriksaan);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo json_encode([
            'status'  => 'error',
            'message' => "Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'"
        ]);
        exit;
    }

    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Buat Variabel
    $allow_age = $Data['allow_age'];
    $allow_sex = $Data['allow_sex'];

    if($allow_age==1){
        if(empty($umur_unit)){
            echo json_encode([
                'status'  => 'error',
                'message' => "Satuan / Unit usia wajib diisi"
            ]);
            exit;
        }
    }

    if($allow_sex==1){
        if(empty($jenis_kelamin)){
            echo json_encode([
                'status'  => 'error',
                'message' => "Jenis kelamin wajib diisi"
            ]);
            exit;
        }
    }
        
    // Simpan Data Ke Database
    $query = $Conn->prepare("
        INSERT INTO referensi_range (
            id_referensi_pemeriksaan,
            umur_kategori,
            umur_min,
            umur_max,
            umur_unit,
            jenis_kelamin,
            nilai_min,
            nilai_max,
            operator,
            label,
            fhir_display,
            fhir_code,
            fhir_system,
            conclusion
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $query->bind_param(
        "isssssssssssss",
        $id_referensi_pemeriksaan,
        $umur_kategori,
        $umur_min,
        $umur_max,
        $umur_unit,
        $jenis_kelamin,
        $nilai_min,
        $nilai_max,
        $operator,
        $label,
        $fhir_display,
        $fhir_code,
        $fhir_system,
        $conclusion
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi range pemeriksaan berhasil disimpan'
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