<?php
     /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    // Tanggal Jam Sekarang
    $now_ms = date('YmdHis');

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if(empty($_POST['id_laboratorium'])){
        echo json_encode(['status'  => 'error','message' => 'ID Laboratorium Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['conclusion'])){
        echo json_encode(['status'  => 'error','message' => 'Kesimpulan (Conclusion) Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['clinical'])){
        echo json_encode(['status'  => 'error','message' => 'Kondisi Klinis (Clinical) Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['icd_10_code'])){
        echo json_encode(['status'  => 'error','message' => 'Kode ICD10 Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['icd_10_display'])){
        echo json_encode(['status'  => 'error','message' => 'Display ICD10 Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['icd_10_system'])){
        echo json_encode(['status'  => 'error','message' => 'System ICD10 Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['pernyataan_petugas'])){
        echo json_encode(['status'  => 'error','message' => 'Persetujuan Petugas Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel dari Data Yang Wajib
    $id_laboratorium    = $_POST['id_laboratorium'];
    $conclusion         = $_POST['conclusion'];
    $clinical           = $_POST['clinical'];
    $icd_10_code        = $_POST['icd_10_code'];
    $icd_10_display     = $_POST['icd_10_display'];
    $icd_10_system      = $_POST['icd_10_system'];
    $pernyataan_petugas = $_POST['pernyataan_petugas'];

    
    if(empty($_POST['id_laboratorium_diagnostic'])){
        $id_laboratorium_diagnostic = generateUUIDv4();
       
        // Simpan Ke Database laboratorium_diagnostic (Insert)
        $query = $Conn->prepare("
            INSERT INTO laboratorium_diagnostic (
                id_laboratorium_diagnostic,
                id_laboratorium,
                conclusion,
                clinical,
                icd_10_code,
                icd_10_display,
                icd_10_system
            ) VALUES (?,?,?,?,?,?,?)
        ");
        $query->bind_param(
            "sssssss",
            $id_laboratorium_diagnostic,
            $id_laboratorium,
            $conclusion,
            $clinical,
            $icd_10_code,
            $icd_10_display,
            $icd_10_system
        );
        if (!$query->execute()) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data diagnostic report'
            ]);
            exit;
        }
        $query->close();
        $status = "success";
        $message = "Diagnostic Report berhasil disimpan pada database";
    }else{
        $id_laboratorium_diagnostic = $_POST['id_laboratorium_diagnostic'];
        // Simpan Ke Database laboratorium_diagnostic (UPDATE)
        $StatmentUpdate = $Conn->prepare("UPDATE laboratorium_diagnostic SET 
            conclusion = ?,
            clinical = ?,
            icd_10_code = ?,
            icd_10_display = ?,
            icd_10_system = ?
            WHERE id_laboratorium_diagnostic = ?
        ");
        $StatmentUpdate->bind_param("ssssss", $conclusion, $clinical, $icd_10_code, $icd_10_display, $icd_10_system, $id_laboratorium_diagnostic);
        if (!$StatmentUpdate->execute()) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada saat UPDATE data ke tabel laboratorium_diagnostic'
            ]);
            exit;
        }
        $StatmentUpdate->close();
        $status = "success";
        $message = "Diagnostic Report berhasil Di UPDATE pada database";
    }
    
    // Update Status Selesai
    $status = "Selesai";
    $QryUpdateLab = $Conn->prepare("UPDATE laboratorium SET status = ? WHERE id_laboratorium = ?");
    if (!$QryUpdateLab) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal Mempersiapkan Query Update Ke Tabel Laboratorium'
        ]);
        exit;
    }
    $QryUpdateLab->bind_param(
        "si",
        $status,
        $id_laboratorium
    );

    // Eksekusi
    if (!$QryUpdateLab->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal Update Tabel Laboratorium: ' . $QryUpdateLab->error
        ]);
        exit;
    }
    $QryUpdateLab->close();

    echo json_encode([
        'status'  => 'success',
        'message' => $message
    ]);
    
    
   
?>
