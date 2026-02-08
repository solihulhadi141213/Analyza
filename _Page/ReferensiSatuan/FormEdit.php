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
            <div class="col-md-12">
                <label for="nama_satuan_edit"><i>Nama Satuan / Unit</i></label>
                <input type="text" name="nama_satuan" id="nama_satuan_edit" class="form-control" value="'.$nama_satuan.'">
                <small class="text text-grayish">
                    <small>Nama satuan dalam bahasa Indonesia yang mudah dipahami</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="unit_satuan_edit"><i>Unit / Satuan</i></label>
                <input type="text" name="unit_satuan" id="unit_satuan_edit" class="form-control" value="'.$unit_satuan.'">
                <small class="text text-grayish">
                    <small>Nama unit/satuan dalam simbol matematis standar</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="code_satuan_edit"><i>Code</i></label>
                <input type="text" name="code_satuan" id="code_satuan_edit" class="form-control" value="'.$code_satuan.'">
                <small class="text text-grayish">
                    <small>Kode satuan/unit sesuai standar yang digunakan</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="system_satuan_edit"><i>System</i></label>
                <input type="url" name="system_satuan" id="system_satuan_edit" class="form-control" list="list_system_edit" placeholder="https://" value="'.$system_satuan.'">
                <datalist id="list_system_edit"></datalist>
                <small class="text text-grayish">
                    <small>System standar referensi yang digunakan</small>
                </small>
            </div>
        </div>
    ';
?>
