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
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$system_spesimen.'</small>
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