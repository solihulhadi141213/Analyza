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

    //id_referensi_jenis_spesimen wajib terisi
    if(empty($_POST['id_referensi_jenis_spesimen'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Spesimen Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_jenis_spesimen' dan sanitasi
    $id_referensi_jenis_spesimen = validateAndSanitizeInput($_POST['id_referensi_jenis_spesimen']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_jenis_spesimen WHERE id_referensi_jenis_spesimen = ?");
    $Qry->bind_param("i", $id_referensi_jenis_spesimen);
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
        $id_referensi_jenis_spesimen = $Data['id_referensi_jenis_spesimen'];
        $nama_spesimen               = $Data['nama_spesimen'];
        $display_spesimen            = $Data['display_spesimen'];
        $code_spesimen               = $Data['code_spesimen'];
        $system_spesimen             = $Data['system_spesimen'];
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_jenis_spesimen" value="'.$id_referensi_jenis_spesimen.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_spesimen_edit">Nama Spesimen</label>
                    <input type="text" name="nama_spesimen" id="nama_spesimen_edit" class="form-control" value="'.$nama_spesimen.'" required>
                    <small class="text text-grayish">
                        <small>Nama Spesimen dalam bahasa Indonesia yang mudah dipahami</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="display_spesimen_edit"><i>Display</i></label>
                    <input type="text" name="display_spesimen" id="display_spesimen_edit" class="form-control" value="'.$display_spesimen.'" required>
                    <small class="text text-grayish">
                        <small>Nama Spesimen sesuai standar standar</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="code_spesimen_edit"><i>Code</i></label>
                    <input type="text" name="code_spesimen" id="code_spesimen_edit" class="form-control" value="'.$code_spesimen.'" required>
                    <small class="text text-grayish">
                        <small>Kode Spesimen sesuai standar yang digunakan</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="system_spesimen_edit"><i>System</i></label>
                    <input type="url" name="system_spesimen" id="system_spesimen_edit" class="form-control" value="'.$system_spesimen.'" list="list_system_edit" placeholder="https://" required>
                    <datalist id="list_system_edit"></datalist>
                    <small class="text text-grayish">
                        <small>System standar referensi yang digunakan</small>
                    </small>
                </div>
            </div>
        ';
    }
?>