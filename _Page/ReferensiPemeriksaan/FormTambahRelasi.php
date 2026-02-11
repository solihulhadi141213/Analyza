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

    //id_referensi_pemeriksaan wajib terisi
    if(empty($_POST['id_referensi_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_pemeriksaan' dan sanitasi
    $id_referensi_pemeriksaan = validateAndSanitizeInput($_POST['id_referensi_pemeriksaan']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
    $Qry->bind_param("i", $id_referensi_pemeriksaan);
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

    // Buat Variabel
    $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'];
    $result_type              = $Data['result_type'];
    $allow_age                = $Data['allow_age'];
    $allow_sex                = $Data['allow_sex'];
    
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
    ';
    

    echo '
        <div class="row mb-2">
            <div class="col-md-12">
                <small> <b>1. Metode Pemeriksaan</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <label for="id_referensi_metode_pemeriksaan">
                    <small>Nama Metode</small>
                </label>
                <select name="id_referensi_metode_pemeriksaan" id="id_referensi_metode_pemeriksaan" class="form-control" required></select>
                <input type="hidden" name="nama_metode_pemeriksaan" id="nama_metode_pemeriksaan">
            </div>
            <div class="col-md-6 mb-2">
                <label for="display_metode_pemeriksaan">
                    <small><i>Display</i></small>
                </label>
                <input type="text" name="display_metode_pemeriksaan" id="display_metode_pemeriksaan" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
               <label for="code_metode_pemeriksaan">
                    <small><i>Code</i></small>
                </label>
                <input type="text" name="code_metode_pemeriksaan" id="code_metode_pemeriksaan" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="system_metode_pemeriksaan">
                    <small><i>System</i></small>
                </label>
                <input type="url" name="system_metode_pemeriksaan" id="system_metode_pemeriksaan" class="form-control" placeholder="https://" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-md-12">
                <small> <b>2. Jenis Spesimen</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <label for="id_referensi_jenis_spesimen">
                    <small>Jenis Spesimen</small>
                </label>
                <select name="id_referensi_jenis_spesimen" id="id_referensi_jenis_spesimen" class="form-control" required></select>
                <input type="hidden" name="nama_spesimen" id="nama_spesimen">
            </div>
            <div class="col-md-6 mb-2">
                <label for="display_spesimen">
                    <small><i>Display</i></small>
                </label>
                <input type="text" name="display_spesimen" id="display_spesimen" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="code_spesimen">
                    <small><i>Code</i></small>
                </label>
                <input type="text" name="code_spesimen" id="code_spesimen" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="system_spesimen">
                    <small><i>System</i></small>
                </label>
                <input type="url" name="system_spesimen" id="system_spesimen" class="form-control" placeholder="https://" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-md-12">
                <small> <b>3. Pengambilan Spesimen</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <label for="id_referensi_metode_sample">
                    <small>Nama Metode</small>
                </label>
                <select name="id_referensi_metode_sample" id="id_referensi_metode_sample" class="form-control" required></select>
                <input type="hidden" name="nama_metode_sample" id="nama_metode_sample">
            </div>
            <div class="col-md-6 mb-2">
                <label for="display_metode_sample">
                    <small><i>Display</i></small>
                </label>
                <input type="text" name="display_metode_sample" id="display_metode_sample" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="code_metode_sample">
                    <small><i>Code</i></small>
                </label>
                <input type="text" name="code_metode_sample" id="code_metode_sample" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="system_metode_sample">
                    <small><i>System</i></small>
                </label>
                <input type="url" name="system_metode_sample" id="system_metode_sample" class="form-control" placeholder="https://" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-md-12">
                <small> <b>4. Kontainer</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <label for="id_referensi_container">
                    <small>Nama / Jenis Kontainer</small>
                </label>
                <select name="id_referensi_container" id="id_referensi_container" class="form-control" required></select>
                <input type="hidden" name="nama_container" id="nama_container">
            </div>
            <div class="col-md-6 mb-2">
                <label for="display_container">
                    <small><i>Display</i></small>
                </label>
                <input type="text" name="display_container" id="display_container" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="code_container">
                    <small><i>Code</i></small>
                </label>
                <input type="text" name="code_container" id="code_container" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="system_container">
                    <small><i>System</i></small>
                </label>
                <input type="url" name="system_container" id="system_container" class="form-control" placeholder="https://" required>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-md-12">
                <small> <b>5. Kapasitas</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <label for="kapasitas_container">
                    <small><i>Kapasitas</i></small>
                </label>
                <input type="number" min="0" step="0.01" name="kapasitas_container" id="kapasitas_container" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="unit_container">
                    <small>Unit / Satuan</small>
                </label>
                <select name="unit_container" id="unit_container" class="form-control" required></select>
            </div>
            <div class="col-md-6 mb-2">
                <label for="code_unit_container">
                    <small>Code Unit</small>
                </label>
                <input type="text" name="code_unit_container" id="code_unit_container" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label for="system_unit_container">
                    <small><i>System</i></small>
                </label>
                <input type="url" name="system_unit_container" id="system_unit_container" class="form-control" placeholder="https://" required>
            </div>
        </div>
    ';

?>