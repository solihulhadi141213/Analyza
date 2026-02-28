<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Settinggeneral.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang.';
        exit;
    }

    // Ambil parameter request (dukung POST/GET)
    $request = $_POST;
    if (empty($request)) {
        $request = $_GET;
    }

    //id_laboratorium_spesimen wajib terisi
    if(empty($request['id_laboratorium_spesimen'])){
        echo 'ID Spesimen Tidak Boleh Kosong!';
        exit;
    }

    //Buat variabel 'id_laboratorium_spesimen' dan sanitasi
    $id_laboratorium_spesimen = validateAndSanitizeInput($request['id_laboratorium_spesimen']);

    // Ambil pengaturan cetak dari form
    $height = isset($request['height']) ? (float) validateAndSanitizeInput($request['height']) : 150;
    $width = isset($request['width']) ? (float) validateAndSanitizeInput($request['width']) : 50;
    $margin_top = isset($request['margin_top']) ? (float) validateAndSanitizeInput($request['margin_top']) : 0.1;
    $margin_bottom = isset($request['margin_bottom']) ? (float) validateAndSanitizeInput($request['margin_bottom']) : 0.1;
    $margin_left = isset($request['margin_left']) ? (float) validateAndSanitizeInput($request['margin_left']) : 0.1;
    $margin_right = isset($request['margin_right']) ? (float) validateAndSanitizeInput($request['margin_right']) : 0.1;

    // Normalisasi nilai minimum
    $height = max(0, $height);
    $width = max(0, $width);
    $margin_top = max(0, $margin_top);
    $margin_bottom = max(0, $margin_bottom);
    $margin_left = max(0, $margin_left);
    $margin_right = max(0, $margin_right);

    // Simpan sebagai default bila dicentang
    if (!empty($request['set_as_default']) && $request['set_as_default'] == '1') {
        $settingPath = __DIR__ . '/setting_cetak_label.json';
        $settingData = array(
            'width' => $width,
            'height' => $height,
            'margin-top' => $margin_top,
            'margin-bottom' => $margin_bottom,
            'margin-left' => $margin_left,
            'margin-right' => $margin_right
        );

        file_put_contents($settingPath, json_encode($settingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    // Load mPDF
    $autoloadPath = "../../vendor/autoload.php";
    if (!file_exists($autoloadPath)) {
        echo 'Library mPDF tidak ditemukan (vendor/autoload.php).';
        exit;
    }
    require_once $autoloadPath;

    if (!class_exists('\Mpdf\Mpdf')) {
        echo 'Class mPDF tidak tersedia.';
        exit;
    }

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_spesimen WHERE id_laboratorium_spesimen = ?");
    $Qry->bind_param("i", $id_laboratorium_spesimen);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data)) {
        echo 'Data spesimen tidak ditemukan!';
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
        echo 'Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'';
        exit;
    }

    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    if (empty($Data2)) {
        echo 'Data pemeriksaan laboratorium tidak ditemukan!';
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
        $tampilan_qr_code = '<img src="data:image/png;base64,'.$qrCodeBase64.'" alt="QR Code '.$kode_spesimen.'" style="width:100%; height:auto;">';
    } else {
        $tampilan_qr_code = '<div style="font-size:9px;">QR Code gagal dibuat</div>';
    }

    $html = '
    <html>
        <head>
            <style>
                body { font-family: arial; font-size: 12pt; }
                .wrap { width: 100%; }
                .row { width: 100%; }
                .col-qr { width: 30%; vertical-align: top; }
                .col-text { width: 66%; vertical-align: top; padding-left: 2mm; padding-top: 4mm }
                .kode { font-size: 14pt; }
                .nama { font-size: 12pt; }
                .dt { font-size: 10pt; }
                .header_label { font-size: 14pt; border-bottom : 1px solid black; margin-bottom : 4px; }
            </style>
        </head>
        <body>
            <table class="wrap" cellpadding="0" cellspacing="0" border="0">
                <tr class="row">
                    <td class="col-qr">'.$tampilan_qr_code.'</td>
                    <td class="col-text" align="right">
                        <div class="header_label"><b>'.$company_name.'</b></div><br>
                        <div class="kode">'.htmlspecialchars($kode_spesimen).' / RM.'.htmlspecialchars((string)$id_pasien).'</div>
                        <div class="dt">'.htmlspecialchars((string)$datetime_spesimen).'</div><br>
                        <div class="nama">'.htmlspecialchars((string)$nama).'</div>
                    </td>
                </tr>
            </table>
        </body>
    </html>';

    try {
        $mpdf = new \Mpdf\Mpdf(array(
            'mode' => 'utf-8',
            'format' => array($height, $width),
            'margin_top' => $margin_top,
            'margin_bottom' => $margin_bottom,
            'margin_left' => $margin_left,
            'margin_right' => $margin_right
        ));

        $mpdf->WriteHTML($html);
        $fileName = 'Label-Spesimen-' . preg_replace('/[^A-Za-z0-9\-]/', '_', $kode_spesimen) . '.pdf';
        $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE);
        exit;
    } catch (\Throwable $e) {
        echo 'Gagal membuat PDF: ' . $e->getMessage();
        exit;
    }
