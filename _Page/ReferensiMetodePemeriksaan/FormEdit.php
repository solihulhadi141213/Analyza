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
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_metode_pemeriksaan_edit">Nama Metode Pemeriksaan</label>
                    <input type="text" name="nama_metode_pemeriksaan" id="nama_metode_pemeriksaan_edit" class="form-control" value="'.htmlspecialchars($nama_metode_pemeriksaan, ENT_QUOTES).'" required>
                    <small class="text text-grayish">
                        <small>Nama Metode Pemeriksaan dalam bahasa Indonesia yang mudah dipahami</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="display_metode_pemeriksaan_edit"><i>Display</i></label>
                    <input type="text" name="display_metode_pemeriksaan" id="display_metode_pemeriksaan_edit" class="form-control" value="'.htmlspecialchars($display_metode_pemeriksaan, ENT_QUOTES).'" required>
                    <small class="text text-grayish">
                        <small>Nama Metode Pemeriksaan sesuai standar standar</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="code_metode_pemeriksaan_edit"><i>Code</i></label>
                    <input type="text" name="code_metode_pemeriksaan" id="code_metode_pemeriksaan_edit" class="form-control" value="'.htmlspecialchars($code_metode_pemeriksaan, ENT_QUOTES).'" required>
                    <small class="text text-grayish">
                        <small>Kode Metode Pemeriksaan sesuai standar yang digunakan</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="system_metode_pemeriksaan_edit"><i>System</i></label>
                    <input type="url" name="system_metode_pemeriksaan" id="system_metode_pemeriksaan_edit" class="form-control" list="list_system" placeholder="https://" value="'.htmlspecialchars($system_metode_pemeriksaan, ENT_QUOTES).'" required>
                    <datalist id="list_system"></datalist>
                    <small class="text text-grayish">
                        <small>System standar referensi yang digunakan</small>
                    </small>
                </div>
            </div>
        ';
    }
?>
