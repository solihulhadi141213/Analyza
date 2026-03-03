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
    //order wajib terisi
    if(empty($_POST['order'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Order Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    //type_content wajib terisi
    if(empty($_POST['type_content'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Tipe Konten Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_dokumentasi' dan sanitasi
    $id_dokumentasi = validateAndSanitizeInput($_POST['id_dokumentasi']);
    $order          = validateAndSanitizeInput($_POST['order']);
    $type_content   = validateAndSanitizeInput($_POST['type_content']);

    // Variabel Tidak Wajib
    if(empty($_POST['order_by'])){
       $order_by = "";
    }else{
        $order_by = $_POST['order_by'];
    }

    // Buat Form Hidden
    echo '
        <input type="hidden" name="id_dokumentasi" value="'.$id_dokumentasi.'">
        <input type="hidden" name="order" value="'.$order.'">
        <input type="hidden" name="type_content" value="'.$type_content.'">
        <input type="hidden" name="order_by" value="'.$order_by.'">
    ';

    // Menampilkan Form Berdasarkan Tipe
    if($type_content=="text"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label><small>Isi Konten</small></label>
                    <div id="editor_quill" style="height:200px;"></div>
                    <input type="hidden" name="value_content" id="value_content">
                </div>
            </div>
        ';
    }
    if($type_content=="list"){
        echo '
            <div class="row mb-3">
                <div class="col-10"><label for="value_content"><small>List Konten</small></label></div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-floating btn-md btn-outline-secondary add_multiple_form"><i class="bi bi-plus"></i></button>
                </div>
            </div>
            <div id="list_container">
                <div class="row mb-2 list_item_row">
                    <div class="col-10">
                        <input type="text" name="value_content[]" class="form-control">
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-floating btn-md btn-outline-danger delete_multiple_form">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        ';
    }
    if($type_content=="image"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content"><small>Upload Gambar</small></label>
                    <input type="file" name="value_content" id="value_content" class="form-control">
                    <small class="text text-grayish">Maksimal 5 Mb (Tipe File : JPEG, PNG, GIF, WEBP, BMP)</small>
                </div>
            </div>
        ';
    }
    if($type_content=="video"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content"><small>Upload Video</small></label>
                    <input type="file" name="value_content" id="value_content" class="form-control">
                    <small class="text text-grayish">Maksimal 50 Mb (Tipe File : MP4, WEBM, OGG, MOV, AVI dan MKV)</small>
                </div>
            </div>
        ';
    }
    if($type_content=="image_link"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content"><small>URL Gambar</small></label>
                    <input type="url" name="value_content" id="value_content" class="form-control">
                </div>
            </div>
        ';
    }
    if($type_content=="video_link"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content"><small>URL Video</small></label>
                    <input type="url" name="value_content" id="value_content" class="form-control">
                </div>
            </div>
        ';
    }
?>