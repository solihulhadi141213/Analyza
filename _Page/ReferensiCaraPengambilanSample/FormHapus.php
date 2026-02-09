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

    //id_referensi_metode_sample wajib terisi
    if(empty($_POST['id_referensi_metode_sample'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Kontainer Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_metode_sample' dan sanitasi
    $id_referensi_metode_sample = validateAndSanitizeInput($_POST['id_referensi_metode_sample']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_metode_sample WHERE id_referensi_metode_sample = ?");
    $Qry->bind_param("i", $id_referensi_metode_sample);
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
        $nama_metode_sample         = $Data['nama_metode_sample'];
        $display_metode_sample      = $Data['display_metode_sample'];
        $code_metode_sample         = $Data['code_metode_sample'];
        $system_metode_sample       = $Data['system_metode_sample'];
        
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_metode_sample" value="'.$id_referensi_metode_sample.'">
            <div class="row mb-2">
                <div class="col-4"><small>Nama Metode</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_metode_sample.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long"><i>'.$display_metode_sample.'</i></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$code_metode_sample.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$system_metode_sample.'</small>
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
    }
?>