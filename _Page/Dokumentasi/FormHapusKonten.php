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

    //id_dokumentasi_content wajib terisi
    if(empty($_POST['id_dokumentasi_content'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Konten Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    if(empty($_POST['id_dokumentasi'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Dokumentasi Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_dokumentasi_content' dan sanitasi
    $id_dokumentasi_content = validateAndSanitizeInput($_POST['id_dokumentasi_content']);
    $id_dokumentasi = validateAndSanitizeInput($_POST['id_dokumentasi']);

    //Buka Detail 'dokumentasi' Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM dokumentasi_content WHERE id_dokumentasi_content = ?");
    $Qry->bind_param("i", $id_dokumentasi_content);
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
                <small>Data konten dokumentasi tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $type_content  = $Data['type_content'];
    $order_content = $Data['order_content'];
    $value_content = $Data['value_content'];

    // Menampilkan form
    echo '<input type="hidden" name="id_dokumentasi_content" value="'.$id_dokumentasi_content.'">';
    echo '<input type="hidden" name="id_dokumentasi" value="'.$id_dokumentasi.'">';
    // Menampilkan Data
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>Type Konten</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$type_content.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Order Konten</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$order_content.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Value</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$value_content.'</small></div>
        </div>
    ';

    // Menampilkan Datetime dan Author
    echo '
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-danger text-center">
                    <small>Apakah anda yakin ingin menghapus data ini?</small>
                </div>
            </div>
        </div>
    ';
?>
