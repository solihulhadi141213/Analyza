<?php
    // ======================================================
    // KONEKSI, SESSION, GLOBAL FUNCTION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    date_default_timezone_set("Asia/Jakarta");
    header('Content-Type: application/json');

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI INPUT WAJIB
    // ======================================================
    $requiredFields = [
        'nama_satuan'   => 'Nama Satuan / Unit Tidak Boleh Kosong',
        'unit_satuan'   => 'Unit Satuan Tidak Boleh Kosong.',
        'code_satuan'   => 'Kode Satuan Tidak Boleh Kosong.',
        'system_satuan' => 'Kode Sistem Satuan Tidak Boleh Kosong.'
    ];

    foreach ($requiredFields as $field => $message) {
        if (empty($_POST[$field])) {
            echo json_encode([
                'status'  => 'error',
                'message' => $message
            ]);
            exit;
        }
    }

    // ======================================================
    // SANITASI INPUT
    // ======================================================
    $nama_satuan   = validateAndSanitizeInput($_POST['nama_satuan']);
    $unit_satuan   = validateAndSanitizeInput($_POST['unit_satuan']);
    $code_satuan   = validateAndSanitizeInput($_POST['code_satuan']);
    $system_satuan = validateAndSanitizeInput($_POST['system_satuan']);

    // ======================================================
    // VALIDASI DUPLIKAT CODE
    // ======================================================
    $validasi_duplikat = GetDetailData($Conn,'referensi_satuan','code_satuan',$code_satuan,'id_referensi_satuan');

    if (!empty($validasi_duplikat)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Kode satuan sudah terdaftar.'
        ]);
        exit;
    }

    // ======================================================
    // INSERT DATA
    // ======================================================
    $stmt = $Conn->prepare("INSERT INTO referensi_satuan (nama_satuan, unit_satuan, code_satuan, system_satuan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param(
        "ssss",
        $nama_satuan,
        $unit_satuan,
        $code_satuan,
        $system_satuan
    );
    $stmt->execute();
    

    if(!$stmt){
        echo json_encode([
            'status' => 'error',
            'message' => 'Prepare failed: '.$Conn->error
        ]);
        exit;
    }
    $stmt->close();
    // ======================================================
    // RESPONSE SUKSES
    // ======================================================
    echo json_encode([
        'status'  => 'success',
        'message' => 'Referensi Satuan Berhasil Disimpan'
    ]);
    exit;
