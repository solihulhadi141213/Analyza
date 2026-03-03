<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    //id_dokumentasi wajib terisi
    if(empty($_POST['id_dokumentasi'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_dokumentasi' dan sanitasi
    $id_dokumentasi = validateAndSanitizeInput($_POST['id_dokumentasi']);

    //Buka Detail 'dokumentasi' Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM dokumentasi WHERE id_dokumentasi = ?");
    $Qry->bind_param("i", $id_dokumentasi);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }

    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $dokumentasi_title       = $Data['dokumentasi_title'];
    $dokumentasi_category    = $Data['dokumentasi_category'];
    $dokumentasi_description = $Data['dokumentasi_description'];
    $dokumentasi_datetime    = $Data['dokumentasi_datetime'];
    $dokumentasi_author      = $Data['dokumentasi_author'];
    $publish                 = $Data['publish'];

    // Menampilkan form
    echo '<input type="hidden" id="id_dokumentasi" value="'.$id_dokumentasi.'">';
    // Menampilkan Data
    echo '
        <div class="row mb-2">
            <div class="col-12 mb-2 border-1 border-bottom">
                <h3>'.$dokumentasi_title.'</h3>
                <small><i><i class="bi bi-tag"></i> '.$dokumentasi_category.'</i></small>
            </div>
        </div>
        <div class="row mb-2 border-1 border-bottom">
            <div class="col-12 mb-3">
                <i>'.$dokumentasi_description.'</i>
            </div>
        </div>
    ';

    // Menampilkan Datetime dan Author
    echo '
        <div class="row mt-3">
            <div class="col-12 mt-2 border-1 border-top">
                <small>
                    <i><i class="bi bi-calendar"></i> '.date('d/m/Y H:i', strtotime($dokumentasi_datetime)).'</i><br>
                    Write By : '.$dokumentasi_author.'
                </small>
            </div>
        </div>
    ';
?>
