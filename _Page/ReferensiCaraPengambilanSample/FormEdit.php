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
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_metode_sample_edit">Nama Metode Spesimen</label>
                    <input type="text" name="nama_metode_sample" id="nama_metode_sample_edit" class="form-control" value="'.$nama_metode_sample.'" required>
                    <small class="text text-grayish">
                        <small>Nama Metode Spesimen dalam bahasa Indonesia yang mudah dipahami</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="display_metode_sample_edit"><i>Display</i></label>
                    <input type="text" name="display_metode_sample" id="display_metode_sample_edit" class="form-control" value="'.$display_metode_sample.'" required>
                    <small class="text text-grayish">
                        <small>Nama Metode Spesimen sesuai standar standar</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="code_metode_sample_edit"><i>Code</i></label>
                    <input type="text" name="code_metode_sample" id="code_metode_sample_edit" class="form-control" value="'.$code_metode_sample.'" required>
                    <small class="text text-grayish">
                        <small>Kode Metode Spesimen sesuai standar yang digunakan</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="system_metode_sample_edit"><i>System</i></label>
                    <input type="url" name="system_metode_sample" id="system_metode_sample_edit" class="form-control" list="list_system_edit" value="'.$system_metode_sample.'" placeholder="https://" required>
                    <datalist id="list_system"></datalist>
                    <small class="text text-grayish">
                        <small>System standar referensi yang digunakan</small>
                    </small>
                </div>
            </div>
        ';
    }
?>