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
                <div class="col-md-12">
                    <label for="body_site_nama_edit">Nama Lokasi Tubuh</label>
                    <input type="text" name="body_site_nama" id="body_site_nama_edit" class="form-control" value="'.$body_site_nama.'" required>
                    <small class="text text-grayish">
                        <small>Nama Lokasi Tubuh (Body Site) dalam bahasa Indonesia yang mudah dipahami</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="body_site_display_edit"><i>Display</i></label>
                    <input type="text" name="body_site_display" id="body_site_display_edit" class="form-control" value="'.$body_site_display.'" required>
                    <small class="text text-grayish">
                        <small>Nama Lokasi Tubuh (Body Site) sesuai standar standar</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="body_site_code_edit"><i>Code</i></label>
                    <input type="text" name="body_site_code" id="body_site_code_edit" class="form-control" value="'.$body_site_code.'" required>
                    <small class="text text-grayish">
                        <small>Kode Lokasi Tubuh (Body Site) sesuai standar yang digunakan</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="body_site_system_edit"><i>System</i></label>
                    <input type="url" name="body_site_system" id="body_site_system_edit" class="form-control" list="list_system_edit" value="'.$body_site_system.'" placeholder="https://" required>
                    <datalist id="list_system"></datalist>
                    <small class="text text-grayish">
                        <small>System standar referensi yang digunakan</small>
                    </small>
                </div>
            </div>
        ';
    }
?>