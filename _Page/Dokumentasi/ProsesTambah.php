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
    $dokumentasi_title       = trim($_POST['dokumentasi_title'] ?? '');
    $dokumentasi_category    = trim($_POST['dokumentasi_category'] ?? '');
    $dokumentasi_description = trim($_POST['dokumentasi_description'] ?? '');
    $dokumentasi_datetime    = date("Y-m-d H:i:s");

    // Validasi
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

    // Simpan ke database
    $publish = 0;
    $stmt = $Conn->prepare("
        INSERT INTO dokumentasi 
        (
            dokumentasi_title,
            dokumentasi_category,
            dokumentasi_description,
            dokumentasi_datetime,
            dokumentasi_author, 
            publish
        ) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssi",
        $dokumentasi_title,
        $dokumentasi_category,
        $dokumentasi_description,
        $dokumentasi_datetime,
        $dokumentasi_author,
        $publish
    );

    if($stmt->execute()){

        $response['status']  = "success";
        $response['message'] = "Dokumentasi berhasil ditambahkan";
        $response['payload'] = [
            "insert_id" => $stmt->insert_id
        ];

    } else {

        $response['message'] = "Gagal menyimpan data";

    }

    $stmt->close();
    echo json_encode($response);
?>