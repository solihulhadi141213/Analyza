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

    //id_laboratorium wajib terisi
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium' dan sanitasi
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry->bind_param("s", $id_laboratorium);
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
    $kode_dokter_pengirim = $Data['kode_dokter_pengirim'];
    $ihs_dokter_pengirim  = $Data['ihs_dokter_pengirim'];
    $nama_dokter_pengirim = $Data['nama_dokter_pengirim'];
    $nama_dokter_penerima = $Data['nama_dokter_penerima'];
    $kode_dokter_penerima = $Data['kode_dokter_penerima'];
    $ihs_dokter_penerima  = $Data['ihs_dokter_penerima'];
    


    // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';

    echo '
        <div class="row mb-3">
            <div class="col-md-12">
                <small><b>A. Dokter Pengirim</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_dokter_pengirim_edit"><small>Cari Dokter</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_dokter_pengirim" id="id_dokter_pengirim_edit" class="form-control bg-info">
                    <option value="">Cari & Pilih</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="kode_dokter_pengirim_edit"><small>Kode Dokter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="kode_dokter_pengirim" id="kode_dokter_pengirim_edit" class="form-control" value="'.$kode_dokter_pengirim.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="ihs_dokter_pengirim_edit"><small>IHS Dokter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="ihs_dokter_pengirim" id="ihs_dokter_pengirim_edit" class="form-control" value="'.$ihs_dokter_pengirim.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_dokter_pengirim_edit"><small>IHS Dokter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="nama_dokter_pengirim" id="nama_dokter_pengirim_edit" class="form-control" value="'.$nama_dokter_pengirim.'">
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-md-12">
                <small><b>B. Dokter Penerima</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_dokter_penerima_edit"><small>Cari Dokter</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_dokter_penerima" id="id_dokter_penerima_edit" class="form-control bg-info">
                    <option value="">Cari & Pilih</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="kode_dokter_penerima_edit"><small>Kode Dokter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="kode_dokter_penerima" id="kode_dokter_penerima_edit" class="form-control" value="'.$kode_dokter_penerima.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="ihs_dokter_penerima_edit"><small>IHS Dokter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="ihs_dokter_penerima" id="ihs_dokter_penerima_edit" class="form-control" value="'.$ihs_dokter_penerima.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_dokter_penerima_edit"><small>IHS Dokter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="nama_dokter_penerima" id="nama_dokter_penerima_edit" class="form-control" value="'.$nama_dokter_penerima.'">
            </div>
        </div>
    ';
?>
