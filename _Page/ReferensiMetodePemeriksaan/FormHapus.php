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

    //id_referensi_metode_pemeriksaan wajib terisi
    if(empty($_POST['id_referensi_metode_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Metode Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_metode_pemeriksaan' dan sanitasi
    $id_referensi_metode_pemeriksaan = validateAndSanitizeInput($_POST['id_referensi_metode_pemeriksaan']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_metode_pemeriksaan WHERE id_referensi_metode_pemeriksaan = ?");
    $Qry->bind_param("i", $id_referensi_metode_pemeriksaan);
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
        $id_referensi_metode_pemeriksaan = $Data['id_referensi_metode_pemeriksaan'];
        $nama_metode_pemeriksaan         = $Data['nama_metode_pemeriksaan'];
        $display_metode_pemeriksaan      = $Data['display_metode_pemeriksaan'];
        $code_metode_pemeriksaan         = $Data['code_metode_pemeriksaan'];
        $system_metode_pemeriksaan       = $Data['system_metode_pemeriksaan'];
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_metode_pemeriksaan" value="'.$id_referensi_metode_pemeriksaan.'">
            <div class="row mb-2">
                <div class="col-4"><small>Metode Pemeriksaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_metode_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long"><i>'.$display_metode_pemeriksaan.'</i></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$code_metode_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$system_metode_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <small>Apakah anda yakin ingin menghapus data ini?</small>
                    </div>
                </div>
        ';
    }
?>
