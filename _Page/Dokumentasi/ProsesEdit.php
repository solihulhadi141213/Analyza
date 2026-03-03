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
    $id_dokumentasi          = trim($_POST['id_dokumentasi'] ?? '');
    $dokumentasi_title       = trim($_POST['dokumentasi_title'] ?? '');
    $dokumentasi_category    = trim($_POST['dokumentasi_category'] ?? '');
    $dokumentasi_description = trim($_POST['dokumentasi_description'] ?? '');
    $dokumentasi_datetime    = date("Y-m-d H:i:s");

    // Validasi
    if($id_dokumentasi == ''){
        $response['message'] = "ID dokumentasi tidak boleh kosong";
        echo json_encode($response);
        exit;
    }
    if($dokumentasi_title == ''){
        $response['message'] = "Judul dokumentasi tidak boleh kosong";
        echo json_encode($response);
        exit;
    }

    if($dokumentasi_category == ''){
        $response['message'] = "Kategori harus dipilih atau diisi";
        echo json_encode($response);
        exit;
    }

    if(strlen($dokumentasi_description) > 1000){
        $response['message'] = "Deskripsi maksimal 1000 karakter";
        echo json_encode($response);
        exit;
    }

    // Jika ada session author
    $dokumentasi_author = $access_name;

    // Update ke database
    $stmt = $Conn->prepare("
        UPDATE dokumentasi SET
            dokumentasi_title = ?,
            dokumentasi_category = ?,
            dokumentasi_description = ?,
            dokumentasi_datetime = ?,
            dokumentasi_author = ?
        WHERE id_dokumentasi = ?
    ");

    $stmt->bind_param(
        "sssssi",
        $dokumentasi_title,
        $dokumentasi_category,
        $dokumentasi_description,
        $dokumentasi_datetime,
        $dokumentasi_author,
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
