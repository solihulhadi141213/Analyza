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

    //id_referensi_container wajib terisi
    if(empty($_POST['id_referensi_container'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Kontainer Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_container' dan sanitasi
    $id_referensi_container = validateAndSanitizeInput($_POST['id_referensi_container']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_container WHERE id_referensi_container = ?");
    $Qry->bind_param("i", $id_referensi_container);
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
        $id_referensi_container = $Data['id_referensi_container'];
        $nama_container         = $Data['nama_container'];
        $display_container      = $Data['display_container'];
        $code_container         = $Data['code_container'];
        $system_container       = $Data['system_container'];
        $kapasitas_container    = $Data['kapasitas_container'];
        $unit_container         = $Data['unit_container'];
        $code_unit_container    = $Data['code_unit_container'];
        $system_unit_container  = $Data['system_unit_container'];
        
        // Buka id Unit satuan
        $id_referensi_satuan = GetDetailData($Conn, 'referensi_satuan', 'code_satuan', $code_unit_container, 'id_referensi_satuan');
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_container" value="'.$id_referensi_container.'">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nama_container_edit">Nama Kemasan (Container)</label>
                    <input type="text" name="nama_container" id="nama_container_edit" class="form-control" value="'.$nama_container.'">
                    <small class="text text-grayish">
                        <small>Nama kemasan sample spesimen dalam bahasa Indonesia yang mudah dipahami</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="display_container_edit"><i>Display</i></label>
                    <input type="text" name="display_container" id="display_container_edit" class="form-control" value="'.$display_container.'" required>
                    <small class="text text-grayish">
                        <small>Nama kemasan sample spesimen sesuai standar standar</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="code_container_edit"><i>Code</i></label>
                    <input type="text" name="code_container" id="code_container_edit" class="form-control" value="'.$code_container.'" required>
                    <small class="text text-grayish">
                        <small>Kode kemasan sample spesimen sesuai standar yang digunakan</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="system_container_edit"><i>System</i></label>
                    <input type="url" name="system_container" id="system_container_edit" class="form-control" list="list_system_edit" value="'.$system_container.'" placeholder="https://" required>
                    <datalist id="list_system_edit"></datalist>
                    <small class="text text-grayish">
                        <small>System standar referensi yang digunakan</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="kapasitas_container_edit">Kapasitas</label>
                    <input type="number" min="0" step="0.01" name="kapasitas_container" id="kapasitas_container_edit" class="form-control" value="'.$kapasitas_container.'" required>
                    <small class="text text-grayish">
                        <small>Kapasitas total kemasan (container)</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="unit_container_edit">Unit Kapasitas</label>
                    <select name="unit_container" id="unit_container_edit" class="form-control" required>
                        <option value="'.$id_referensi_satuan.'">'.$unit_container.' ('.$code_unit_container.')</option>
                    </select>
                </div>
            </div>
        ';
    }
?>