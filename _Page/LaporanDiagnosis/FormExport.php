<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Tangkap Data
    if(empty($_POST['periode'])){
        $periode_value = "";
        $periode = "-";
    }else{
        $periode_value = $_POST['periode'];
        $periode = $_POST['periode'];
    }
    if(empty($_POST['tahun'])){
        $tahun_value = "";
        $tahun = "-";
    }else{
        $tahun_value = $_POST['tahun'];
        $tahun = $_POST['tahun'];
    }
    if(empty($_POST['bulan'])){
        $bulan = "-";
        $bulan_value = "";
        $nama_bulan = "-";
    }else{
        $bulan = $_POST['bulan'];
        $bulan_value = $_POST['bulan'];
        $nama_bulan = getNamaBulanSingkatZeroPadding($bulan_value);
    }
    
    echo '
        <input type="hidden" name="periode" value="'.$periode_value.'">
        <input type="hidden" name="tahun" value="'.$tahun_value.'">
        <input type="hidden" name="bulan" value="'.$bulan_value.'">
    ';
    echo '
        <div class="row mb-3">
            <div class="col-4"><small>Periode</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$periode.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Tahun</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$tahun.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small>Bulan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$nama_bulan.'</small></div>
        </div>
    ';
    echo '
        <div class="row mb-3">
            <div class="col-12">
                <label for="format_data">
                    <small>Format Data</small>
                </label>
                <select name="format_data" id="format_data" class="form-control">
                    <option value="">Pilih Format</option>
                    <option value="HTML">HTML</option>
                    <option value="Excel">Excel</option>
                    <option value="PDF">PDF</option>
                </select>
            </div>
        </div>
    ';
?>
