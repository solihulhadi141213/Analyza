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
    echo '<input type="hidden" name="id_dokumentasi" value="'.$id_dokumentasi.'">';
    // Menampilkan Data
    echo '
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="dokumentasi_title_edit">Judul Dokumentasi</label>
                <input type="text" name="dokumentasi_title" id="dokumentasi_title_edit" class="form-control" value="'.$dokumentasi_title.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="dokumentasi_category_edit">Kategori</label>
                <select name="dokumentasi_category" id="dokumentasi_category_edit" class="form-control">
                    <option value="">Pilih</option>
                    <option selected value="'.$dokumentasi_category.'">'.$dokumentasi_category.'</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="dokumentasi_description_edit">Deskripsi</label>
                <textarea name="dokumentasi_description" id="dokumentasi_description_edit" class="form-control">'.$dokumentasi_description.'</textarea>
                <small class="text text-grayish" id="jumlah_karakter_deskripsi_edit">0 / 1000</small>
            </div>
        </div>
    ';
?>
