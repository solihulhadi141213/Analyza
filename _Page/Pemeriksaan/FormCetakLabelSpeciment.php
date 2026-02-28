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

    //id_laboratorium_spesimen wajib terisi
    if(empty($_POST['id_laboratorium_spesimen'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Spesimen Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_spesimen' dan sanitasi
    $id_laboratorium_spesimen = validateAndSanitizeInput($_POST['id_laboratorium_spesimen']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_spesimen WHERE id_laboratorium_spesimen = ?");
    $Qry->bind_param("i", $id_laboratorium_spesimen);
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
                <small>Data spesimen tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium   = $Data['id_laboratorium'];
    if(empty($Data['id_speciment'])){
        $id_speciment      = '-';
    }else{
        $id_speciment      = $Data['id_speciment'];
    }
    
    $datetime_spesimen     = $Data['datetime_spesimen'];
    $nama_spesimen         = $Data['nama_spesimen'];
    $display_spesimen      = $Data['display_spesimen'];
    $code_spesimen         = $Data['code_spesimen'];
    $system_spesimen       = $Data['system_spesimen'];
    $nama_metode_sample    = $Data['nama_metode_sample'];
    $display_metode_sample = $Data['display_metode_sample'];
    $code_metode_sample    = $Data['code_metode_sample'];
    $system_metode_sample  = $Data['system_metode_sample'];
    $bodysite_nama         = $Data['bodysite_nama'];
    $bodysite_display      = $Data['bodysite_display'];
    $bodysite_code         = $Data['bodysite_code'];
    $bodysite_system       = $Data['bodysite_system'];
    $nama_container        = $Data['nama_container'];
    $display_container     = $Data['display_container'];
    $code_container        = $Data['code_container'];
    $system_container      = $Data['system_container'];
    $quantity_value        = $Data['quantity_value'];
    $quantity_unit         = $Data['quantity_unit'];
    $quantity_code         = $Data['quantity_code'];
    $quantity_system       = $Data['quantity_system'];
    $collector_name        = $Data['collector_name'];
    $collector_ihs         = $Data['collector_ihs'];

    // Buka Nama pasien / RM dll
    $Qry2 = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry2->bind_param("s", $id_laboratorium);
    if (!$Qry2->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }

    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    if (empty($Data2)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_pasien    = $Data2['id_pasien'] ?? '';
    $id_kunjungan = $Data2['id_kunjungan'] ?? '';
    $ihs_pasien   = $Data2['ihs_pasien'] ?? '';
    $id_encounter = $Data2['id_encounter'] ?? '';
    $nama         = $Data2['nama'] ?? '';
    $gender       = $Data2['gender'] ?? '';

    // Nilai yang diencode pada QR: utamakan code_spesimen, fallback ke kode internal
    $kode_spesimen = "LAB-SPC-$id_laboratorium_spesimen";

    // Generate QR Code inline (base64) agar tidak perlu simpan file sementara
    $qrCodeBase64 = '';
    $phpQrLibPath = "../../assets/vendor/phpqrcode/qrlib.php";
    if (file_exists($phpQrLibPath)) {
        require_once $phpQrLibPath;
        if (class_exists('QRcode')) {
            ob_start();
            QRcode::png($kode_spesimen, null, QR_ECLEVEL_M, 4, 2);
            $qrCodeBase64 = base64_encode(ob_get_clean());
        }
    }

    if (!empty($qrCodeBase64)) {
        $tampilan_qr_code = '
            <img src="data:image/png;base64,'.$qrCodeBase64.'" alt="QR Code '.$kode_spesimen.'" class="img-fluid" width="80%">
        ';
    }else{
        $tampilan_qr_code = '<small>Library PHPQRCode tidak ditemukan atau gagal memproses QR Code.</small>';
    }

    // Ambil default pengaturan cetak dari file JSON
    $settingPath = __DIR__ . '/setting_cetak_label.json';
    $defaultWidth = 50;
    $defaultHeight = 150;
    $defaultMarginTop = 0.1;
    $defaultMarginBottom = 0.1;
    $defaultMarginLeft = 0.1;
    $defaultMarginRight = 0.1;

    if (file_exists($settingPath)) {
        $json = file_get_contents($settingPath);
        $setting = json_decode($json, true);
        if (is_array($setting)) {
            $defaultWidth = $setting['width'] ?? $defaultWidth;
            $defaultHeight = $setting['height'] ?? $defaultHeight;
            $defaultMarginTop = $setting['margin_top'] ?? ($setting['margin-top'] ?? $defaultMarginTop);
            $defaultMarginBottom = $setting['margin_bottom'] ?? ($setting['margin-bottom'] ?? $defaultMarginBottom);
            $defaultMarginLeft = $setting['margin_left'] ?? ($setting['margin-left'] ?? $defaultMarginLeft);
            $defaultMarginRight = $setting['margin_right'] ?? ($setting['margin-right'] ?? $defaultMarginRight);
        }
    }

    // Tampilkan Data
    echo '
        <input type="hidden" name="id_laboratorium_spesimen" value="'.$id_laboratorium_spesimen.'">
        <div class="row mb-3">
            <div class="col-md-3 mb-2 text-center">
                '.$tampilan_qr_code.'
            </div>
             <div class="col-md-9 mb-2">
                <div class="row mb-2">
                    <div class="col-3"><small>Kode</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-8">
                        <small class="text text-grayish text-long">'.$kode_spesimen.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-3"><small>No.RM</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-8">
                        <small class="text text-grayish text-long">'.$id_pasien.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-3"><small>Nama</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-8">
                        <small class="text text-grayish text-long">'.$nama.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-3"><small>Datetime</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-8">
                        <small class="text text-grayish text-long">'.$datetime_spesimen.'</small>
                    </div>
                </div>
            </div>
        </div>
    ';
?>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="height"><small>Lebar / Tinggi</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="height" name="height" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultHeight); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="width"><small>Panjang</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="width" name="width" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultWidth); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="margin_top"><small>Margin Atas</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_top" name="margin_top" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginTop); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="margin_bottom"><small>Margin Bawah</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_bottom" name="margin_bottom" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginBottom); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="margin_left"><small>Margin Kiri</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_left" name="margin_left" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginLeft); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="margin_right"><small>Margin Kanan</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_right" name="margin_right" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginRight); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="input-group">
            <input class="form-check-input mt-0" checked name="set_as_default" id="set_as_default" type="checkbox" value="1" aria-label="Checkbox for following text input">
            <label for="set_as_default"><small>Tetapkan Sebagai Default</small></label>
        </div>
    </div>
</div>
