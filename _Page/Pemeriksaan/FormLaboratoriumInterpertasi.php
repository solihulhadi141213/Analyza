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

    //Data Yang Wajib Diisi (Mandatory)
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Rincian Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['gender'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Informasi Gender pasien Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['result_type'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Tipe Hasil Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['result_interpertation_type'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Metode Interpertasi Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    $id_laboratorium            = $_POST['id_laboratorium'];
    $id_laboratorium_rincian    = $_POST['id_laboratorium_rincian'];
    $gender                     = $_POST['gender'];
    $result_type                = $_POST['result_type'];
    $result_interpertation_type = $_POST['result_interpertation_type'];

    // Buat Variabel Dengan Value Tidak Wajib
    if(empty($_POST['id_referensi_usia'])){
        $id_referensi_usia = "";
    }else{
        $id_referensi_usia = $_POST['id_referensi_usia'];
    }
    if(empty($_POST['usia'])){
        $usia = 0;
    }else{
        $usia = $_POST['usia'];
    }
    if(empty($_POST['satuan_usia'])){
        $satuan_usia = "";
    }else{
        $satuan_usia = $_POST['satuan_usia'];
    }
    if(empty($_POST['hasil_pemeriksaan'])){
        $hasil_pemeriksaan = "";
    }else{
        $hasil_pemeriksaan = $_POST['hasil_pemeriksaan'];
    }

    // Inisialisasi hasil untuk simpan pada database
?>
    <input type="hidden" name="id_laboratorium_rincian" value="<?php echo $id_laboratorium_rincian; ?>">
    <div class="row mb-2">
        <div class="col-md-12">
            <label for="hasil"><small>Hasil Pemeriksaan</small></label>
        </div>
        <div class="col-md-12">
            <input type="text" name="hasil" id="hasil" class="form-control" value="<?php echo $hasil_pemeriksaan; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="hasil_interpertasi"><small>Interpertasi</small></label>
        </div>
        <div class="col-md-12">
            <input type="text" name="hasil_interpertasi" id="hasil_interpertasi" class="form-control">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="hasil_conclusion"><small>Kesimpulan</small></label>
        </div>
        <div class="col-md-12">
            <input type="text" name="hasil_conclusion" id="hasil_conclusion" class="form-control">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="hasil_keterangan"><small>Keterangan Lain</small></label>
        </div>
        <div class="col-md-12">
            <textarea name="hasil_keterangan" id="hasil_keterangan" class="form-control"></textarea>
        </div>
    </div>
    
    
    
    
