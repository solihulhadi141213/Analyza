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

    //id_laboratorium wajib terisi
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium' dan sanitasi
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry->bind_param("s", $id_laboratorium);
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

    if (empty($Data)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $datetime_diminta  = $Data['datetime_diminta'];
    $datetime_diterima = $Data['datetime_diterima'];
    $datetime_spesimen = $Data['datetime_spesimen'];
    $datetime_hasil    = $Data['datetime_hasil'];

    // Routing Tanggal Dan Jam
    if(!empty($datetime_diminta)){
        $tanggal_diminta = date('Y-m-d', strtotime($datetime_diminta));
        $jam_diminta     = date('H:i', strtotime($datetime_diminta));
    }else{
        $tanggal_diminta = "";
        $jam_diminta     = "";
    }
    if(!empty($datetime_diterima)){
        $tanggal_diterima = date('Y-m-d', strtotime($datetime_diterima));
        $jam_diterima     = date('H:i', strtotime($datetime_diterima));
    }else{
        $tanggal_diterima = "";
        $jam_diterima     = "";
    }
    if(!empty($datetime_spesimen)){
        $tanggal_spesimen = date('Y-m-d', strtotime($datetime_spesimen));
        $jam_spesimen     = date('H:i', strtotime($datetime_spesimen));
    }else{
        $tanggal_spesimen = "";
        $jam_spesimen     = "";
    }
    if(!empty($datetime_hasil)){
        $tanggal_hasil = date('Y-m-d', strtotime($datetime_hasil));
        $jam_hasil     = date('H:i', strtotime($datetime_hasil));
    }else{
        $tanggal_hasil = "";
        $jam_hasil     = "";
    }
  
    // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';

    echo '
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <label for="tanggal_diminta"><small>Tanggal/Jam Diminta</small></label>
            </div>
            <div class="col-md-5 col-6 mb-2">
                <input type="date" name="tanggal_diminta" id="tanggal_diminta" class="form-control" value="'.$tanggal_diminta.'">
            </div>
            <div class="col-md-3 col-6 mb-2">
                <input type="time" name="jam_diminta" id="jam_diminta" class="form-control" value="'.$jam_diminta.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <label for="tanggal_diterima"><small>Tanggal/Jam Diterima</small></label>
            </div>
            <div class="col-md-5 col-6 mb-2">
                <input type="date" name="tanggal_diterima" id="tanggal_diterima" class="form-control" value="'.$tanggal_diterima.'">
            </div>
            <div class="col-md-3 col-6 mb-2">
                <input type="time" name="jam_diterima" id="jam_diterima" class="form-control" value="'.$jam_diterima.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <label for="tanggal_spesimen"><small>Tanggal/Jam Spesimen</small></label>
            </div>
            <div class="col-md-5 col-6 mb-2">
                <input type="date" name="tanggal_spesimen" id="tanggal_spesimen" class="form-control" value="'.$tanggal_spesimen.'">
            </div>
            <div class="col-md-3 col-6 mb-2">
                <input type="time" name="jam_spesimen" id="jam_spesimen" class="form-control" value="'.$jam_spesimen.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <label for="tanggal_hasil"><small>Tanggal/Jam Hasil</small></label>
            </div>
            <div class="col-md-5 col-6 mb-2">
                <input type="date" name="tanggal_hasil" id="tanggal_hasil" class="form-control" value="'.$tanggal_hasil.'">
            </div>
            <div class="col-md-3 col-6 mb-2">
                <input type="time" name="jam_hasil" id="jam_hasil" class="form-control" value="'.$jam_hasil.'">
            </div>
        </div>
    ';
?>
