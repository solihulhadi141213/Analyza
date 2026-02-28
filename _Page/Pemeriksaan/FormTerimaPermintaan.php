<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/FungsiAkses.php";

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

?>

<input type="hidden" name="id_laboratorium" value="<?php echo $id_laboratorium; ?>">
<div class="row mb-2">
    <div class="col-4"><small>Nama Petugas</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7"><small class="text text-grayish"><?php echo $access_name; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>IHS Petugas</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7"><small class="text text-grayish"><?php echo $access_ihs; ?></small></div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-12">
        <label for="status">Status Pemeriksaan</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">Pilih</option>
            <option value="Diterima">Diterima</option>
            <option value="Ditolak">Ditolak</option>
            <option value="Dibatalkan">Dibatalkan</option>
        </select>
    </div>
</div>
<div class="row mb-3" id="wrap_datetime_diterima" style="display: none;">
    <div class="col-12">
        <label for="datetime_diterima">Tanggal / Waktu</label>
    </div>
    <div class="col-6">
        <input type="date" name="tanggal_diterima" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="col-6">
        <input type="time" name="jam_diterima" class="form-control" value="<?php echo date('H:i'); ?>">
    </div>
</div>
<div class="row mb-3" id="wrap_alasan_penolakan" style="display: none;">
    <div class="col-12">
        <label for="alasan">Alasan Penolakan/Pembatalan</label>
        <textarea class="form-control" name="alasan" id="alasan"></textarea>
    </div>
</div>
<div class="row mb-3" id="wrap_dokter_penerima" style="display: none;">
    <div class="col-md-12">
        <label for="nama_dokter_penerima"><small>Dokter Penerima</small></label>
        <select name="nama_dokter_penerima" id="nama_dokter_penerima" class="form-control">
            <option value=""></option>
        </select>
    </div>
</div>
<input type="hidden" name="ihs_dokter_penerima" id="ihs_dokter_penerima">
<input type="hidden" name="kode_dokter_penerima" id="kode_dokter_penerima">
