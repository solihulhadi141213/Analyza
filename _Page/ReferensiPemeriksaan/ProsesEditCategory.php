<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    // Helper untuk bind_param dinamis
    function refValues($arr){
        $refs = [];
        foreach($arr as $key => $value){
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi Data Wajib
    if (empty($_POST['id_referensi_category'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Category Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST['label_hasil'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Label Hasil Pemeriksaan Tidak Boleh Kosong!'
        ]);
        exit;
    }

    // Buat Variabel
    $id_referensi_category = (int) $_POST['id_referensi_category'];
    $label                 = trim($_POST['label_hasil']);

    // Variabel Tidak Wajib
    $id_referensi_pemeriksaan_post = isset($_POST['id_referensi_pemeriksaan']) ? (int) $_POST['id_referensi_pemeriksaan'] : 0;
    $nilai_hasil                  = isset($_POST['nilai_hasil']) ? trim($_POST['nilai_hasil']) : "";
    $umur_kategori                = isset($_POST['umur_kategori']) ? trim($_POST['umur_kategori']) : "";
    $umur_min                     = isset($_POST['umur_min']) ? trim($_POST['umur_min']) : "0";
    $umur_max                     = isset($_POST['umur_max']) ? trim($_POST['umur_max']) : "0";
    $umur_unit                    = isset($_POST['umur_unit']) ? trim($_POST['umur_unit']) : "";
    $jenis_kelamin                = isset($_POST['jenis_kelamin']) ? trim($_POST['jenis_kelamin']) : "All";
    $fhir_display                 = isset($_POST['fhir_display']) ? trim($_POST['fhir_display']) : "";
    $fhir_code                    = isset($_POST['fhir_code']) ? trim($_POST['fhir_code']) : "";
    $fhir_system                  = isset($_POST['fhir_system']) ? trim($_POST['fhir_system']) : "";
    $conclusion                   = isset($_POST['conclusion']) ? trim($_POST['conclusion']) : "";

    // Validasi Enum
    if(!empty($umur_unit)){
        $enum_umur_unit = ['Hari', 'Bulan', 'Tahun'];
        if(!in_array($umur_unit, $enum_umur_unit, true)){
            echo json_encode([
                'status'  => 'error',
                'message' => 'Tipe satuan usia tidak valid'
            ]);
            exit;
        }
    }

    $enum_jenis_kelamin = ['Laki-laki', 'Perempuan', 'All'];
    if(!in_array($jenis_kelamin, $enum_jenis_kelamin, true)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Jenis Kelamin tidak valid'
        ]);
        exit;
    }

    // Pastikan data category ada
    $Chk = $Conn->prepare("SELECT id_referensi_pemeriksaan FROM referensi_category WHERE id_referensi_category = ?");
    if(!$Chk){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query validasi category: '.$Conn->error
        ]);
        exit;
    }
    $Chk->bind_param("i", $id_referensi_category);
    if(!$Chk->execute()){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mengeksekusi query validasi category: '.$Conn->error
        ]);
        $Chk->close();
        exit;
    }

    $ResChk = $Chk->get_result();
    $DataChk = $ResChk->fetch_assoc();
    $Chk->close();

    if(empty($DataChk)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Data category tidak ditemukan'
        ]);
        exit;
    }

    // Gunakan id pemeriksaan existing agar aman dari manipulasi payload
    $id_referensi_pemeriksaan = (int) $DataChk['id_referensi_pemeriksaan'];
    if($id_referensi_pemeriksaan <= 0 && $id_referensi_pemeriksaan_post > 0){
        $id_referensi_pemeriksaan = $id_referensi_pemeriksaan_post;
    }

    // Baca setting allow_age / allow_sex
    $allow_age = 0;
    $allow_sex = 0;
    if($id_referensi_pemeriksaan > 0){
        $Qry = $Conn->prepare("SELECT allow_age, allow_sex FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
        if($Qry){
            $Qry->bind_param("i", $id_referensi_pemeriksaan);
            if($Qry->execute()){
                $Result = $Qry->get_result();
                $Data = $Result->fetch_assoc();
                if(!empty($Data)){
                    $allow_age = (int) $Data['allow_age'];
                    $allow_sex = (int) $Data['allow_sex'];
                }
            }
            $Qry->close();
        }
    }

    if($allow_age === 1 && empty($umur_unit)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Satuan / Unit usia wajib diisi'
        ]);
        exit;
    }

    if($allow_sex === 1 && empty($jenis_kelamin)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Jenis kelamin wajib diisi'
        ]);
        exit;
    }

    // Cek kolom yang tersedia agar kompatibel dengan struktur tabel aktual
    $ShowCols = $Conn->query("SHOW COLUMNS FROM referensi_category");
    if(!$ShowCols){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal membaca struktur tabel referensi_category: '.$Conn->error
        ]);
        exit;
    }

    $available_columns = [];
    while($row = $ShowCols->fetch_assoc()){
        $available_columns[] = $row['Field'];
    }

    $set_parts = [];
    $types = "";
    $params = [];

    // Kolom inti category
    if(in_array('nilai_hasil', $available_columns, true)){
        $set_parts[] = "nilai_hasil = ?";
        $types .= "s";
        $params[] = $nilai_hasil;
    }
    if(in_array('label', $available_columns, true)){
        $set_parts[] = "label = ?";
        $types .= "s";
        $params[] = $label;
    }
    if(in_array('fhir_display', $available_columns, true)){
        $set_parts[] = "fhir_display = ?";
        $types .= "s";
        $params[] = $fhir_display;
    }
    if(in_array('fhir_code', $available_columns, true)){
        $set_parts[] = "fhir_code = ?";
        $types .= "s";
        $params[] = $fhir_code;
    }
    if(in_array('fhir_system', $available_columns, true)){
        $set_parts[] = "fhir_system = ?";
        $types .= "s";
        $params[] = $fhir_system;
    }

    // Kolom tambahan jika memang tersedia di tabel
    if(in_array('umur_kategori', $available_columns, true)){
        $set_parts[] = "umur_kategori = ?";
        $types .= "s";
        $params[] = $umur_kategori;
    }
    if(in_array('umur_min', $available_columns, true)){
        $set_parts[] = "umur_min = ?";
        $types .= "s";
        $params[] = $umur_min;
    }
    if(in_array('umur_max', $available_columns, true)){
        $set_parts[] = "umur_max = ?";
        $types .= "s";
        $params[] = $umur_max;
    }
    if(in_array('umur_unit', $available_columns, true)){
        $set_parts[] = "umur_unit = ?";
        $types .= "s";
        $params[] = $umur_unit;
    }
    if(in_array('jenis_kelamin', $available_columns, true)){
        $set_parts[] = "jenis_kelamin = ?";
        $types .= "s";
        $params[] = $jenis_kelamin;
    }
    if(empty($set_parts)){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Tidak ada kolom yang dapat diperbarui pada tabel referensi_category'
        ]);
        exit;
    }

    $sql = "UPDATE referensi_category SET ".implode(", ", $set_parts)." WHERE id_referensi_category = ?";
    $types .= "i";
    $params[] = $id_referensi_category;

    $query = $Conn->prepare($sql);
    if(!$query){
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query update category: '.$Conn->error
        ]);
        exit;
    }

    $bind_values = array_merge([$types], $params);
    call_user_func_array([$query, 'bind_param'], refValues($bind_values));

    // Eksekusi
    if($query->execute()){
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi category berhasil diperbarui'
        ]);
    }else{
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data'
        ]);
    }

    $query->close();
    $Conn->close();
?>
