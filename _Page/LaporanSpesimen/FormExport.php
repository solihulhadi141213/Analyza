<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    //Validasi Akses
    if (empty($SessionIdAccess)) {
        echo '
           <div class="alert alert-danger">
            <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
           </div>
        ';
        exit;
    }
    if (empty($_POST["periode"])) {
        echo '
           <div class="alert alert-danger">
            <small>Periode Data Tidak Boleh Kosong!</small>
           </div>
        ';
        exit;
    }
    if (empty($_POST["tahun"])) {
        echo '
           <div class="alert alert-danger">
            <small>Periode Tahun Tidak Boleh Kosong!</small>
           </div>
        ';
        exit;
    }
    $periode = validateAndSanitizeInput($_POST["periode"]);
    $tahun   = validateAndSanitizeInput($_POST["tahun"]);
    $bulan   = "";
    $nama_bulan   = "-";
    $keyword = "$tahun";
    if($periode=="Bulan"){
        if (empty($_POST["bulan"])) {
             echo '
                <div class="alert alert-danger">
                    <small>Periode Bulan Tidak Boleh Kosong!</small>
                </div>
            ';
            exit;
        }
        $bulan = validateAndSanitizeInput($_POST["bulan"]);
        $keyword = "$tahun-$bulan";
        $nama_bulan   = getNamaBulanSingkatZeroPadding($bulan);
    }
    echo '
        <input type="hidden" name="periode" value="'.$periode.'">
        <input type="hidden" name="tahun" value="'.$tahun.'">
        <input type="hidden" name="bulan" value="'.$bulan.'">
    ';

    // Menghitung Jumlah Data
    $jumlah_data = mysqli_num_rows(mysqli_query($Conn, "SELECT DISTINCT nama_spesimen, display_spesimen, code_spesimen, system_spesimen FROM laboratorium_spesimen WHERE datetime_spesimen LIKE '%$keyword%'"));
    
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>Periode</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$periode.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Tahun</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$tahun.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Bulan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$nama_bulan.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Jumlah Data</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$jumlah_data.'</small></div>
        </div>
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <label for="format_file"><small>Format File</small></label>
                <select name="format_file" id="format_file" class="form-control">
                    <option value="HTML">HTML</option>
                    <option value="PDF">PDF</option>
                    <option value="Excel">Excel</option>
                </select>
            </div>
        </div>
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <b>PENTING!</b><br>
                    <small>Semakin banyak data maka sistem akan membutuhkan waktu lebih lama untuk memproses data</small>
                </div>
            </div>
        </div>
    ';
?>