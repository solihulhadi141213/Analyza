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

    //id_laboratorium_spesimen wajib terisi
    if(empty($_POST['id_laboratorium_spesimen'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Spesimen Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_spesimen' dan sanitasi
    $id_laboratorium_spesimen = validateAndSanitizeInput($_POST['id_laboratorium_spesimen']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_spesimen WHERE id_laboratorium_spesimen = ?");
    $Qry->bind_param("i", $id_laboratorium_spesimen);
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
    $id_speciment      = $Data['id_speciment'] ?? '';
    $datetime_spesimen = $Data['datetime_spesimen'];
    $nama_spesimen     = $Data['nama_spesimen'];
    
   

    // Menampilkan FORM
    echo '
        <input type="hidden" name="id_laboratorium_spesimen" value="'.$id_laboratorium_spesimen.'">
    ';
    
    // Informasi Umum
    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>A. Waktu Pengambilan Spesimen</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tanggal_spesimen_edit"><small>Tanggal Pengambilan</small></label>
            </div>
            <div class="col-md-8">
                <input type="date" name="tanggal_spesimen" id="tanggal_spesimen_edit" class="form-control" value="'.date('Y-m-d', strtotime($Data['datetime_spesimen'])).'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="jam_spesimen_edit"><small>Pukul/Jam Pengambilan</small></label>
            </div>
            <div class="col-md-8">
                <input type="time" name="jam_spesimen" id="jam_spesimen_edit" class="form-control" value="'.date('H:i', strtotime($Data['datetime_spesimen'])).'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>B. Petugas Pengambil Spesimen</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="collector_name_edit"><small>Nama Petugas</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="collector_name" id="collector_name_edit" class="form-control" value="'.$Data['collector_name'].'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="collector_ihs_edit"><small>ID IHS Petugas</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="collector_ihs" id="collector_ihs_edit" class="form-control" value="'.$Data['collector_ihs'].'" required>
            </div>
        </div>
    ';
    $id_referensi_jenis_spesimen = GetDetailData($Conn, 'referensi_jenis_spesimen', 'code_spesimen', $Data['code_spesimen'], 'id_referensi_jenis_spesimen');
    $id_referensi_metode_sample  = GetDetailData($Conn, 'referensi_metode_sample', 'code_metode_sample', $Data['code_metode_sample'], 'id_referensi_metode_sample');
    $id_referensi_body_site      = GetDetailData($Conn, 'referensi_body_site', 'body_site_code', $Data['bodysite_code'], 'id_referensi_body_site');
    $id_referensi_container      = GetDetailData($Conn, 'referensi_container', 'code_container', $Data['code_container'], 'id_referensi_container');
    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>C. Informasi Spesimen</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_jenis_spesimen_edit"><small>Nama/Jenis Spesimen</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_jenis_spesimen" id="id_referensi_jenis_spesimen_edit" class="form-control" required>
                    <option value="">Pilih</option>
                    <option selected value="'.$id_referensi_jenis_spesimen.'">'.$Data['nama_spesimen'].'</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_metode_sample_edit"><small>Metode Pengambilan</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_metode_sample" id="id_referensi_metode_sample_edit" class="form-control" required>
                    <option value="">Pilih</option>
                    <option selected value="'.$id_referensi_metode_sample.'">'.$Data['nama_metode_sample'].'</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_body_site_edit"><small>Lokasi Tubuh (<i>Body Site</i>)</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_body_site" id="id_referensi_body_site_edit" class="form-control" required>
                    <option value="">Pilih</option>
                    <option selected value="'.$id_referensi_body_site.'">'.$Data['bodysite_nama'].'</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_container_edit"><small>Nama/Jenis Kemasan (<i>Container</i>)</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_container" id="id_referensi_container_edit" class="form-control" required>
                    <option value="">Pilih</option>
                    <option selected value="'.$id_referensi_container.'">'.$Data['nama_container'].'</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="quantity_value_edit"><small>Jumlah / Volume Spesimen</small></label>
            </div>
            <div class="col-md-8">
                <div class="">
                    <div class="input-group mb-3">
                      <input type="number" min="0" step="0.01" name="quantity_value" id="quantity_value_edit" class="form-control" value="'.$Data['quantity_value'].'" required>
                      <span class="input-group-text" id="quantity_unit_edit">'.$Data['quantity_unit'].'</span>
                    </div>
                </div>
            </div>
        </div>
    ';
?>