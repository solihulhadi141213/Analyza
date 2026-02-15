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

    //id_referensi_usia wajib terisi
    if(empty($_POST['id_referensi_usia'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_usia' dan sanitasi
    $id_referensi_usia = validateAndSanitizeInput($_POST['id_referensi_usia']);
    
    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_usia WHERE id_referensi_usia = ?");
    $Qry->bind_param("i", $id_referensi_usia);
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

    // Buat Variabel Data
    $umur_kategori = $Data['umur_kategori'];
    $umur_min      = $Data['umur_min'];
    $umur_max      = $Data['umur_max'];
    $umur_unit     = $Data['umur_unit'];

    echo '<input type="hidden" name="id_referensi_usia" value="'.$id_referensi_usia.'">';
    echo '
        <div class="row mb-3">
            <div class="col-4"><small>Klasifikasi Usia</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$umur_kategori.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Usia Min</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$umur_min.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Usia Max</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$umur_max.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Unit / Satuan Usia</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$umur_unit.'</small></div>
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