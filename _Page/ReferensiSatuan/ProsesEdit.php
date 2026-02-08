<?php
    // Koneksi, Global Function, Session Dan Setting General
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Header JSON
    header('Content-Type: application/json');

    // Validasi Session
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir. Silakan login ulang.'
        ]);
        exit;
    }

    // Validasi Input Wajib
    $requiredFields = [
        'id_referensi_satuan' => 'ID Satuan Tidak Boleh Kosong.',
        'nama_satuan'         => 'Nama Satuan Tidak Boleh Kosong.',
        'unit_satuan'         => 'Unit Satuan Tidak Boleh Kosong.',
        'code_satuan'         => 'Kode Satuan Tidak Boleh Kosong.',
        'system_satuan'       => 'Kode Sistem Satuan Tidak Boleh Kosong.'
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

    // Sanitasi Input
    $id_referensi_satuan = validateAndSanitizeInput($_POST['id_referensi_satuan']);
    $nama_satuan         = validateAndSanitizeInput($_POST['nama_satuan']);
    $unit_satuan         = validateAndSanitizeInput($_POST['unit_satuan']);
    $code_satuan         = validateAndSanitizeInput($_POST['code_satuan']);
    $system_satuan       = validateAndSanitizeInput($_POST['system_satuan']);

    // Ambil Kode Lama
    $code_lama = GetDetailData($Conn,'referensi_satuan','id_referensi_satuan',$id_referensi_satuan,'code_satuan');

    // Validasi Duplikat Kode
    if ($code_lama !== $code_satuan) {
        $validasi_duplikat = GetDetailData($Conn,'referensi_satuan','code_satuan',$code_satuan,'id_referensi_satuan');
    } else {
        $validasi_duplikat = "";
    }

    if (!empty($validasi_duplikat)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Kode Satuan sudah terdaftar.'
        ]);
        exit;
    }

    // ==============================
    // PROSES UPDATE DATA
    // ==============================
    $sql = "UPDATE referensi_satuan SET
                nama_satuan = ?,
                unit_satuan = ?,
                code_satuan = ?,
                system_satuan = ?
            WHERE id_referensi_satuan = ?";

    $stmt = $Conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query.'
        ]);
        exit;
    }

    $stmt->bind_param(
        "ssssi",
        $nama_satuan,
        $unit_satuan,
        $code_satuan,
        $system_satuan,
        $id_referensi_satuan
    );

    if (!$stmt->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbaharui referensi satuan.'
        ]);
        exit;
    }

    $stmt->close();

    // RESPONSE SUKSES
    echo json_encode([
        'status'  => 'success',
        'message' => 'Referensi satuan Berhasil Diperbaharui'
    ]);
    exit;
?>
