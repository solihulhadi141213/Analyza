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

    //id_referensi_container wajib terisi
    if(empty($_POST['id_referensi_container'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Kontainer Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_container' dan sanitasi
    $id_referensi_container = validateAndSanitizeInput($_POST['id_referensi_container']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_container WHERE id_referensi_container = ?");
    $Qry->bind_param("i", $id_referensi_container);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        // Buat Variabel
        $id_referensi_container = $Data['id_referensi_container'];
        $nama_container         = $Data['nama_container'];
        $display_container      = $Data['display_container'];
        $code_container         = $Data['code_container'];
        $system_container       = $Data['system_container'];
        $kapasitas_container    = $Data['kapasitas_container'];
        $unit_container         = $Data['unit_container'];
        $code_unit_container    = $Data['code_unit_container'];
        $system_unit_container  = $Data['system_unit_container'];
        
       
        //Tampilkan Data
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Nama Kontainer</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_container.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long"><i>'.$display_container.'</i></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$code_container.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$system_container.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kapasitas</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$kapasitas_container.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Unit</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$unit_container.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Code Unit</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$code_unit_container.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$system_unit_container.'</small>
                </div>
            </div>
        ';
    }
?>