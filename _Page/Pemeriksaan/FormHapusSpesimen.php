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
                <small>ID Spesimen Tidak Boleh Kosong!</small>
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
                <small>Data spesimen tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium   = $Data['id_laboratorium'];
    if(empty($Data['id_speciment'])){
        $id_speciment      = '-';
    }else{
        $id_speciment      = $Data['id_speciment'];
    }
    
    $datetime_spesimen     = $Data['datetime_spesimen'];
    $nama_spesimen         = $Data['nama_spesimen'];
    $display_spesimen      = $Data['display_spesimen'];
    $code_spesimen         = $Data['code_spesimen'];
    $system_spesimen       = $Data['system_spesimen'];
    $nama_metode_sample    = $Data['nama_metode_sample'];
    $display_metode_sample = $Data['display_metode_sample'];
    $code_metode_sample    = $Data['code_metode_sample'];
    $system_metode_sample  = $Data['system_metode_sample'];
    $bodysite_nama         = $Data['bodysite_nama'];
    $bodysite_display      = $Data['bodysite_display'];
    $bodysite_code         = $Data['bodysite_code'];
    $bodysite_system       = $Data['bodysite_system'];
    $nama_container        = $Data['nama_container'];
    $display_container     = $Data['display_container'];
    $code_container        = $Data['code_container'];
    $system_container      = $Data['system_container'];
    $quantity_value        = $Data['quantity_value'];
    $quantity_unit         = $Data['quantity_unit'];
    $quantity_code         = $Data['quantity_code'];
    $quantity_system       = $Data['quantity_system'];
    $collector_name        = $Data['collector_name'];
    $collector_ihs         = $Data['collector_ihs'];

    // Tampilkan Data
    echo '
        <input type="hidden" name="id_laboratorium_spesimen" value="'.$id_laboratorium_spesimen.'">
        <div class="row mb-2">
            <div class="col-4"><small>Waktu Pengambilan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.date('d/m/Y H:i T', strtotime($datetime_spesimen)).'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Spesimen</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nama_spesimen.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Display</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long"><i>'.$display_spesimen.'</i></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$code_spesimen.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>System</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$system_spesimen.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Petugas</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$collector_name.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>ID Practitioner</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$collector_ihs.'</small>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-danger text-center">
                    <small>Apakah anda yakin ingin menghapus data ini?</small>
                </div>
            </div>
        </div>

    ';
?>