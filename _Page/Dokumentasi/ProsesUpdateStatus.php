<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    $response = [
        "status"  => "error",
        "message" => "Terjadi kesalahan"
    ];

    // Validasi Session Login
    if(empty($SessionIdAccess)){
        $response = [
            "status"  => "error",
            "message" => "Sesi Login Sudah Berakhir! Silahkan Login Ulang!"
        ];
        exit;
    }

    // Panggil 'FungsiAkses'
    include "../../_Config/FungsiAkses.php";

    // Ambil & Bersihkan Input
    $id_dokumentasi = trim($_POST['id_dokumentasi'] ?? '');
    $publish        = trim($_POST['publish'] ?? 0);

    // Validasi
    if($id_dokumentasi == ''){
        $response['message'] = "ID dokumentasi tidak boleh kosong";
        echo json_encode($response);
        exit;
    }

    // Update ke database
    $stmt = $Conn->prepare("
        UPDATE dokumentasi SET
            publish = ?
        WHERE id_dokumentasi = ?
    ");

    $stmt->bind_param(
        "ii",
        $publish,
        $id_dokumentasi
    );

    if($stmt->execute()){

        $response['id']  = $id_dokumentasi;
        $response['status']  = "success";
        $response['message'] = "Dokumentasi berhasil diperbarui";

    } else {
        $response['id']  = $id_dokumentasi;
        $response['message'] = "Gagal menyimpan data";

    }

    $stmt->close();
    echo json_encode($response);
?>
