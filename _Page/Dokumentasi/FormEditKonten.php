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
    //id_dokumentasi_content wajib terisi
    if(empty($_POST['id_dokumentasi_content'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Konten Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    
    //Buat variabel 'id_dokumentasi' dan sanitasi
    $id_dokumentasi         = validateAndSanitizeInput($_POST['id_dokumentasi']);
    $id_dokumentasi_content = validateAndSanitizeInput($_POST['id_dokumentasi_content']);

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

    // Buat Form Hidden
    echo '
        <input type="hidden" name="id_dokumentasi" value="'.$id_dokumentasi.'">
        <input type="hidden" name="id_dokumentasi_content" value="'.$id_dokumentasi_content.'">
        <input type="hidden" name="type_content" id="type_content_edit" value="'.$type_content.'">
    ';

    // Menampilkan Form Berdasarkan Tipe
    if($type_content=="text"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label><small>Isi Konten</small></label>
                    <div id="editor_quill_edit" style="height:200px;">'.$value_content.'</div>
                    <input type="hidden" name="value_content" id="value_content_edit" value="'.$value_content.'">
                </div>
            </div>
        ';
    }
    if($type_content=="list"){
        echo '
            <div class="row mb-3">
                <div class="col-10"><label for="value_content_edit"><small>List Konten</small></label></div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-floating btn-md btn-outline-secondary add_multiple_form_edit"><i class="bi bi-plus"></i></button>
                </div>
            </div>
            <div id="list_container_edit">
                <div class="row mb-2 list_item_row_edit">
                    <div class="col-10">
                        <input type="text" name="value_content_edit[]" class="form-control">
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-floating btn-md btn-outline-danger delete_multiple_form_edit">
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
                    <label for="value_content_file_edit"><small>Upload Gambar</small></label>
                    <input type="file" name="value_content" id="value_content_file_edit" class="form-control">
                    <small class="text text-grayish">Maksimal 5 Mb (Tipe File : JPEG, PNG, GIF, WEBP, BMP)</small>
                </div>
            </div>
        ';
    }
    if($type_content=="video"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content_file_edit"><small>Upload Video</small></label>
                    <input type="file" name="value_content" id="value_content_file_edit" class="form-control">
                    <small class="text text-grayish">Maksimal 50 Mb (Tipe File : MP4, WEBM, OGG, MOV, AVI dan MKV)</small>
                </div>
            </div>
        ';
    }
    if($type_content=="image_link"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content_edit"><small>URL Gambar</small></label>
                    <input type="url" name="value_content_edit" id="value_content_edit" class="form-control" value="'.$value_content.'">
                </div>
            </div>
        ';
    }
    if($type_content=="video_link"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <label for="value_content_edit"><small>URL Video</small></label>
                    <input type="url" name="value_content" id="value_content_edit" class="form-control" value="'.$value_content.'">
                </div>
            </div>
        ';
    }
?>