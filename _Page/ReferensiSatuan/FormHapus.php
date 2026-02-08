<?php
    // Koneksi Session Dan Function
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi id_referensi_satuan
    if (empty($_POST['id_referensi_satuan'])) {
        echo '
            <div class="alert alert-danger">
                <small>ID Satuan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel 'id_referensi_satuan' dan sanitazi
    $id_referensi_satuan = validateAndSanitizeInput($_POST['id_referensi_satuan']);

    // Query Database 'referensi_sediaan'
    $Qry = $Conn->prepare("SELECT * FROM referensi_satuan WHERE id_referensi_satuan = ?");
    $Qry->bind_param("i", $id_referensi_satuan);

    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan saat membuka data!<br>
                Keterangan : ' . htmlspecialchars($Conn->error) . '</small>
            </div>
        ';
        exit;
    }

    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    if (!$Data) {
        echo '
            <div class="alert alert-warning">
                <small>Data Sediaan tidak ditemukan.</small>
            </div>
        ';
        exit;
    }

    $id_referensi_satuan = $Data['id_referensi_satuan'];
    $nama_satuan         = $Data['nama_satuan'];
    $unit_satuan         = $Data['unit_satuan'];
    $code_satuan         = $Data['code_satuan'];
    $system_satuan       = $Data['system_satuan'];

    echo '
        <input type="hidden" name="id_referensi_satuan" value="'.$id_referensi_satuan.'">
        <div class="row mb-3">
            <div class="col-4"><small><i>Nama Satuan</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$nama_satuan.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small><i>Unit Satuan</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$unit_satuan.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small><i>Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$code_satuan.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small><i>System</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-grayish">'.$system_satuan.'</small></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
               <div class="alert alert-warning">
                    Apakah Anda Yakin Akan Menghapus Data Referensi Satuan Tersebut?
               </div>
            </div>
        </div>
    ';
?>
