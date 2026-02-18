<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
    //Zona Waktu Pakai UTC
    date_default_timezone_set('UTC');
    $datetime_now = new DateTime();

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    //id_laboratorium_rincian wajib terisi
    if(empty($_POST['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Rincian Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_rincian' dan sanitasi
    $id_laboratorium_rincian = validateAndSanitizeInput($_POST['id_laboratorium_rincian']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
    $Qry->bind_param("i", $id_laboratorium_rincian);
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
                <small>Data Rincian Pemeriksaan Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium          = $Data['id_laboratorium'];
    $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'];
    $nama_pemeriksaan         = $Data['nama_pemeriksaan'];
    $category_pemeriksaan     = $Data['category_pemeriksaan'];
    
    // Buka Referensi Pemeriksaan
    $Qry = $Conn->prepare("SELECT * FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
    $Qry->bind_param("i", $id_referensi_pemeriksaan);
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

    // Buat Variabel
    $code_pemeriksaan           = $Data['code_pemeriksaan'];
    $display_pemeriksaan        = $Data['display_pemeriksaan'];
    $system_pemeriksaan         = $Data['system_pemeriksaan'];
    

    echo '
        <input type="hidden" name="id_laboratorium_rincian" value="'.$id_laboratorium_rincian.'">
    ';
?>
<div class="row mb-2">
    <div class="col-4"><small><i>ID Laboratorium</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $id_laboratorium; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Kategori Pemeriksaan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $category_pemeriksaan; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Nama Pemeriksaan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $nama_pemeriksaan; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Display</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $display_pemeriksaan; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Code</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $code_pemeriksaan; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>System</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $system_pemeriksaan; ?></small></div>
</div>
 <div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>Apakah anda yakin ingin menghapus data ini?</small>
        </div>
    </div>
</div>
