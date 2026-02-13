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

    //id_referensi_body_site wajib terisi
    if(empty($_POST['id_referensi_body_site'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Lokasi Tubuh (Body Site) Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_body_site' dan sanitasi
    $id_referensi_body_site = validateAndSanitizeInput($_POST['id_referensi_body_site']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_body_site WHERE id_referensi_body_site = ?");
    $Qry->bind_param("i", $id_referensi_body_site);
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
        $body_site_nama         = $Data['body_site_nama'];
        $body_site_display      = $Data['body_site_display'];
        $body_site_code         = $Data['body_site_code'];
        $body_site_system       = $Data['body_site_system'];
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_body_site" value="'.$id_referensi_body_site.'">
            <div class="row mb-3">
                <div class="col-4"><small>Nama Lokasi Tubuh</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$body_site_nama.'</small></div>
            </div>
            <div class="row mb-3">
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish"><i>'.$body_site_display.'</i></small></div>
            </div>
            <div class="row mb-3">
                <div class="col-4"><small><i>Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish">'.$body_site_code.'</small></div>
            </div>
            <div class="row mb-3">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text text-grayish"><i>'.$body_site_system.'</i></small></div>
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