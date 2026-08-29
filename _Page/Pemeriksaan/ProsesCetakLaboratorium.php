<?php
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
    ob_clean();
     //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
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

    if(empty($_POST['height'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Ukuran Lebar Dokumen Tidak Boleh Kosong</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['width'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Ukuran Panjang Dokumen Tidak Boleh Kosong</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['margin_top'])){
        $margin_top = 0;
    }else{
        $margin_top = (float) validateAndSanitizeInput($_POST['margin_top']);
    }
    if(empty($_POST['margin_bottom'])){
        $margin_bottom = 0;
    }else{
        $margin_bottom = (float) validateAndSanitizeInput($_POST['margin_bottom']);
    }
    if(empty($_POST['margin_left'])){
        $margin_left = 0;
    }else{
        $margin_left = (float) validateAndSanitizeInput($_POST['margin_left']);
    }
    if(empty($_POST['margin_right'])){
        $margin_right = 0;
    }else{
        $margin_right = (float) validateAndSanitizeInput($_POST['margin_right']);
    }
    if(empty($_POST['show_header'])){
        $show_header = false;
    }else{
        $show_header = true;
    }
    if(empty($_POST['show_signature'])){
        $show_signature = false;
    }else{
        $show_signature = true;
    }

    if(empty($_POST['format_hasil'])){
        $format_hasil = "HTML";
    }else{
        $format_hasil = strtoupper(validateAndSanitizeInput($_POST['format_hasil']));
    }
    if($format_hasil !== "PDF"){
        $format_hasil = "HTML";
    }


    //Buat variabel 'id_laboratorium' dan sanitasi
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);
    $height          = (float) validateAndSanitizeInput($_POST['height']);
    $width           = (float) validateAndSanitizeInput($_POST['width']);
    $height          = max(0, $height);
    $width           = max(0, $width);
    $margin_top      = max(0, (float) $margin_top);
    $margin_bottom   = max(0, (float) $margin_bottom);
    $margin_left     = max(0, (float) $margin_left);
    $margin_right    = max(0, (float) $margin_right);

    //Buka Detail laboratorium Dengan Prepared Statment
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
    $id_pasien            = $Data['id_pasien'] ?? '';
    $id_kunjungan         = $Data['id_kunjungan'] ?? '';
    $nama                 = $Data['nama'] ?? '';
    $gender               = $Data['gender'] ?? '';
    $tanggal_lahir        = $Data['tanggal_lahir'] ?? '';
    $tujuan               = $Data['tujuan'] ?? '';
    $pembayaran           = $Data['pembayaran'] ?? '';
    $fakses               = $Data['fakses'] ?? '';
    $unit                 = $Data['unit'] ?? '';
    $priority             = $Data['priority'] ?? '';
    $puasa                = $Data['puasa'] ?? '0';
    $status               = $Data['status'] ?? '';
    $nama_dokter_pengirim = $Data['nama_dokter_pengirim'] ?? '-';
    $nama_dokter_penerima = $Data['nama_dokter_penerima'] ?? '-';
    $nama_petugas         = $Data['nama_petugas'] ?? '-';
    $datetime_diminta     = $Data['datetime_diminta'] ?? '';
    $datetime_diterima    = $Data['datetime_diterima'] ?? '';
    $datetime_spesimen    = $Data['datetime_spesimen'] ?? '';
    $datetime_hasil       = $Data['datetime_hasil'] ?? '';
    $keterangan           = $Data['keterangan'] ?? '-';

    $label_puasa = ((string)$puasa === '1') ? 'Puasa' : 'Tidak Puasa';
    $tanggal_lahir_label     = !empty($tanggal_lahir) ? date('d/m/Y', strtotime($tanggal_lahir)) : '-';
    $datetime_diminta_label  = formatDateTimeStrict($datetime_diminta);
    $datetime_diterima_label = formatDateTimeStrict($datetime_diterima);
    $datetime_spesimen_label = formatDateTimeStrict($datetime_spesimen);
    $datetime_hasil_label    = formatDateTimeStrict($datetime_hasil);

    // Mencari Base64 Tanda Tangan

    // priority
    if($priority=="routine"){
        $label_priority = 'BIASA';
    }else{
        if($priority=="urgent"){
            $label_priority = 'SEGERA';
        }else{
            $label_priority = 'DARURAT';
        }
    }

    // Usia pada saat permintaan dibuat (tanggal_lahir -> datetime_diminta)
    // Aturan:
    // - < 1 bulan  => satuan Hari
    // - < 1 tahun  => satuan Bulan
    // - >= 1 tahun => satuan Tahun
    // - Dibulatkan ke atas bila sisa > setengah satuan
    if (empty($tanggal_lahir) || empty($datetime_diminta)) {
        $usia = "-";
    } else {
        try {
            $tgl_lahir = new DateTime($tanggal_lahir);
            $tgl_diminta = new DateTime($datetime_diminta);

            if ($tgl_diminta < $tgl_lahir) {
                $usia = "-";
            } else {
                $selisih = $tgl_lahir->diff($tgl_diminta);

                if ($selisih->y >= 1) {
                    $tahun = (int) $selisih->y;
                    $lebih_setengah_tahun = (
                        $selisih->m > 6 ||
                        ($selisih->m == 6 && ($selisih->d > 0 || $selisih->h > 0 || $selisih->i > 0 || $selisih->s > 0))
                    );
                    if ($lebih_setengah_tahun) {
                        $tahun++;
                    }
                    $usia = $tahun . ' Tahun';
                } elseif ($selisih->m >= 1) {
                    $bulan = (int) $selisih->m;
                    $acuan_bulan = clone $tgl_lahir;
                    $acuan_bulan->add(new DateInterval('P' . $bulan . 'M'));
                    $hari_dalam_bulan = (int) $acuan_bulan->format('t');
                    $sisa_hari = $selisih->d + ($selisih->h / 24) + ($selisih->i / 1440) + ($selisih->s / 86400);

                    if ($sisa_hari > ($hari_dalam_bulan / 2)) {
                        $bulan++;
                    }

                    $usia = $bulan . ' Bulan';
                } else {
                    $hari = (int) $selisih->days;
                    $sisa_hari = ($selisih->h / 24) + ($selisih->i / 1440) + ($selisih->s / 86400);

                    if ($sisa_hari > 0.5) {
                        $hari++;
                    }

                    $usia = $hari . ' Hari';
                }
            }
        } catch (Exception $e) {
            $usia = "-";
        }
    }

    // Generate QR Code inline (base64) agar tidak perlu simpan file sementara
    $url_viewer = "$app_base_url/result_viewer.php?id=$id_laboratorium";
    $qrCodeBase64 = '';
    $phpQrLibPath = "../../assets/vendor/phpqrcode/qrlib.php";
    if (file_exists($phpQrLibPath)) {
        require_once $phpQrLibPath;
        if (class_exists('QRcode')) {
            ob_start();
            QRcode::png($url_viewer, null, QR_ECLEVEL_M, 4, 2);
            $qrCodeBase64 = base64_encode(ob_get_clean());
        }
    }

    if (!empty($qrCodeBase64)) {
        $tampilan_qr_code = '<img src="data:image/png;base64,'.$qrCodeBase64.'" alt="QR Code '.$url_viewer.'" style="width:80px; height:auto;">';
    } else {
        $tampilan_qr_code = '<div style="font-size:9px;">QR Code gagal dibuat</div>';
    }

    // Simpan setting cetak berdasarkan id_access pengguna (fallback baca ke DEFAULT di form)
    $settingPath = __DIR__ . '/setting_cetak_hasil.json';
    $settingEntry = array(
        'id_access' => (string) $SessionIdAccess,
        'print_setting' => array(
            'margin_top' => $margin_top,
            'margin_bottom' => $margin_bottom,
            'margin_left' => $margin_left,
            'margin_right' => $margin_right,
            'width' => $width,
            'height' => $height
        ),
        'show_header' => (bool) $show_header,
        'show_signature' => (bool) $show_signature
    );

    $settingList = array();
    if (file_exists($settingPath)) {
        $settingJson = file_get_contents($settingPath);
        $settingDecoded = json_decode($settingJson, true);
        if (is_array($settingDecoded)) {
            $isList = array_keys($settingDecoded) === range(0, count($settingDecoded) - 1);
            if ($isList) {
                $settingList = $settingDecoded;
            } else {
                $settingList[] = $settingDecoded;
            }
        }
    }

    $isFoundAccessSetting = false;
    foreach ($settingList as $index => $item) {
        if (is_array($item) && (($item['id_access'] ?? '') === (string) $SessionIdAccess)) {
            $settingList[$index] = $settingEntry;
            $isFoundAccessSetting = true;
            break;
        }
    }
    if ($isFoundAccessSetting === false) {
        $settingList[] = $settingEntry;
    }
    file_put_contents($settingPath, json_encode($settingList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    ob_start();
?>
<html>
    <head>
        <title>Hasil-Lab-<?php echo $id_laboratorium; ?></title>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
            }
            table.kop_surat {
                width : 100%;
            }
            table.kop_surat tr td {
                padding : 0px;
                margin : 0px;
            }
            b {
                padding : 0px;
                margin : 0px;
                margin-bottom : 0px;
                padding-bottom : 0px;
            }
            .pembatas_garis {
                border-bottom: 3px double black;
                width : 100%;
                margin-top : 5px;
                margin-bottom : 5px;
            }
            table.informasi_pemeriksaan tr td {
                font-size: 10pt;
            }
            table.tabel_spesimen {
                width : 100%;
                border-top : 1px solid black;
                border-right : 1px solid black;
            }
            table.tabel_spesimen tr td {
                border-bottom : 1px solid black;
                border-left : 1px solid black;
                padding : 3px;
            }
            table.tabel_hasil {
                width : 100%;
                border-top : 1px solid black;
                border-right : 1px solid black;
            }
            table.tabel_hasil tr td {
                border-bottom : 1px solid black;
                border-left : 1px solid black;
                padding : 3px;
            }
            table.tabel_diagnostik {
                width : 100%;
                border-top : 1px solid black;
                border-right : 1px solid black;
            }
            table.tabel_diagnostik tr td {
                border-bottom : 1px solid black;
                border-left : 1px solid black;
                padding : 3px;
            }
        </style>
    </head>
    <body>
        <!-- KOP SURAT -->
        <?php
            if($show_header==true){
                echo '
                    <table class="kop_surat">
                        <tr>
                            <td align="left">
                                <img src="../../assets/img/'.$app_logo.'" width="80px">
                            </td>
                            <td align="left">
                                <b>'.$company_name.'</b><br>
                                '.$company_address.'<br>
                                <small>Email : '.$company_address.' | Kontak : '.$company_contact.'</small><br>
                            </td>
                            <td align="right" valign="middle">
                                <b>LEMBAR HASIL PEMERIKSAAN LABORATORIUM</b><br>
                                PRIORITAS : '.$label_priority.' | STATUS : '.$status.' 
                            </td>
                        </tr>
                    </table>
                ';
            }
        ?>
        <table class="pembatas_garis">
           <tr>
                <td></td>
           </tr>
        </table>

        <!-- Informasi Pemeriksaan -->
        <table width="100%" class="informasi_pemeriksaan">
            <tr>
                <td width="33%" valign="top">
                    <table>
                        <tr>
                            <td><b>No.RM / Reg</b></td>
                            <td>:</td>
                            <td><?php echo "RM.$id_pasien / REG.$id_pasien"; ?></td>
                        </tr>
                        <tr>
                            <td><b>Nama Pasien</b></td>
                            <td>:</td>
                            <td><?php echo $nama; ?></td>
                        </tr>
                        <tr>
                            <td><b>Jenis Kelamin</b></td>
                            <td>:</td>
                            <td><?php echo $gender; ?></td>
                        </tr>
                        <tr>
                            <td><b>Tanggal Lahir</b></td>
                            <td>:</td>
                            <td><?php echo $tanggal_lahir_label; ?></td>
                        </tr>
                        <tr>
                            <td><b>Usia Saat Pemeriksaan</b></td>
                            <td>:</td>
                            <td><?php echo $usia; ?></td>
                        </tr>
                    </table>
                </td>
                <td width="33%" valign="top">
                    <table>
                        <tr>
                            <td><b>Permintaan</b></td>
                            <td>:</td>
                            <td><?php echo "$datetime_diminta_label"; ?></td>
                        </tr>
                        <tr>
                            <td><b>Kunjungan</b></td>
                            <td>:</td>
                            <td><?php echo $tujuan; ?></td>
                        </tr>
                        <tr>
                            <td><b>Metode Pembayaran</b></td>
                            <td>:</td>
                            <td><?php echo $pembayaran; ?></td>
                        </tr>
                        <tr>
                            <td><b>Faskes Pengirim</b></td>
                            <td>:</td>
                            <td><?php echo $fakses; ?></td>
                        </tr>
                        <tr>
                            <td><b>Unit / Instalasi</b></td>
                            <td>:</td>
                            <td><?php echo $unit; ?></td>
                        </tr>
                    </table>
                </td>
                <td valign="top">
                    <table>
                        <tr>
                            <td><b>Keluar Hasil</b></td>
                            <td>:</td>
                            <td><?php echo $datetime_hasil_label; ?></td>
                        </tr>
                        <tr>
                            <td><b>Petugas Laboratorium</b></td>
                            <td>:</td>
                            <td><?php echo "$nama_petugas"; ?></td>
                        </tr>
                        <tr>
                            <td><b>Dokter Pengirim</b></td>
                            <td>:</td>
                            <td><?php echo $nama_dokter_pengirim; ?></td>
                        </tr>
                        <tr>
                            <td><b>Dokter Penerima</b></td>
                            <td>:</td>
                            <td><?php echo $nama_dokter_penerima; ?></td>
                        </tr>
                        <tr>
                            <td><b>Keterangan</b></td>
                            <td>:</td>
                            <td><?php echo $keterangan; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="pembatas_garis">
            <tr>
                    <td></td>
            </tr>
        </table>

        <!-- informasi spesimen -->
        <p>
            <b>A. Informasi Spesimen</b>
            <table class="tabel_spesimen" cellspacing="0">
                <tr>
                    <td align="center"><b>No</b></td>
                    <td align="center"><b>Kode</b></td>
                    <td align="center"><b>Spesimen</b></td>
                    <td align="center"><b>Waktu Pengambilan</b></td>
                    <td align="center"><b>Metode</b></td>
                    <td align="center"><b><i>Body Site</i></b></td>
                    <td align="center"><b><i>Quantity</i></b></td>
                    <td align="center"><b><i>Container</i></b></td>
                    <td align="center"><b>Petugas</b></td>
                </tr>
                <?php
                    // Menampilkan Spesimen
                    $jumlah_spesimen = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_spesimen FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium'"));
                    if(empty($jumlah_spesimen)){
                        echo '
                            <tr>
                                <td colspan="8" align="center">
                                    <small>NO DATA</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        $no3 = 1;
                        $query3 = mysqli_query($Conn, "SELECT * FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium' ORDER BY nama_spesimen ASC");
                        while ($data3 = mysqli_fetch_array($query3)) {
                            $id_laboratorium_spesimen = $data3['id_laboratorium_spesimen'];
                            $id_speciment             = $data3['id_speciment'];
                            $datetime_spesimen        = $data3['datetime_spesimen'];
                            $nama_spesimen            = $data3['nama_spesimen'] ?? '-';
                            $display_spesimen         = $data3['display_spesimen'] ?? '-';
                            $nama_metode_sample       = $data3['nama_metode_sample'] ?? '-';
                            $bodysite_nama            = $data3['bodysite_nama'] ?? '-';
                            $bodysite_nama            = $data3['bodysite_nama'] ?? '-';
                            $quantity_value           = $data3['quantity_value'] ?? '0';
                            $quantity_unit            = $data3['quantity_unit'] ?? '';
                            $nama_container           = $data3['nama_container'] ?? '-';
                            $collector_name           = $data3['collector_name'] ?? '-';
                            echo '
                                <tr>
                                    <td align="center">'.$no3.'</td>
                                    <td align="left">LAB-SPC-'.$id_laboratorium_spesimen.'</td>
                                    <td align="left">'.$nama_spesimen.'</td>
                                    <td align="left">'.date('d/m/Y H:i', strtotime($datetime_spesimen)).'</td>
                                    <td align="left">'.$nama_metode_sample.'</td>
                                    <td align="left">'.$bodysite_nama.'</td>
                                    <td align="left">'.$quantity_value.' '.$quantity_unit.'</td>
                                    <td align="left">'.$nama_container.'</td>
                                    <td align="left">'.$collector_name.'</td>
                                </tr>
                            ';
                            $no3++;
                        }
                    }
                ?>
            </table>
        </p>
        <p>
            <b>B. Hasil Pemeriksaan</b>
            <table class="tabel_hasil" cellspacing="0">
                <tr>
                    <td align="center"><b>No</b></td>
                    <td align="center" colspan="2"><b>Parameter Pemeriksaan</b></td>
                    <td align="center"><b>Spesimen</b></td>
                    <td align="center"><b>Hasil</b></td>
                    <td align="center"><b>Satuan</b></td>
                    <td align="center"><b>Nilai Rujukan / Normal</b></td>
                    <td align="center"><b><i>Interpertasi</i></b></td>
                    <td align="center"><b><i>Kesimpulan</i></b></td>
                </tr>
                <?php
                    // Menampilkan Spesimen
                    $jumlah_rincian = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium'"));
                    if(empty($jumlah_rincian)){
                        echo '
                            <tr>
                                <td colspan="9" class="text-center">
                                    <small class="text-danger">Tidak Ada Data Rincian Pemeriksaan</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        // Menampilkan 'category_pemeriksaan' secara DISTINCT
                        $no = 1;
                        $query = mysqli_query($Conn, "SELECT DISTINCT category_pemeriksaan FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium' ORDER BY category_pemeriksaan ASC");
                        while ($data = mysqli_fetch_array($query)) {
                            $category_pemeriksaan = $data['category_pemeriksaan'];
                            echo '
                                <tr>
                                    <td align="center"><b>'.$no.'</b></td>
                                    <td colspan="8"><b>'.$category_pemeriksaan.'</b></td>
                                </tr>
                            ';

                            // Menampilkan 'laboratorium_rincian' berdasarkan category_pemeriksaan
                            $no2 = 1;
                            $query2 = mysqli_query($Conn, "SELECT * FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium' AND category_pemeriksaan='$category_pemeriksaan' ORDER BY nama_pemeriksaan ASC");
                            while ($data2 = mysqli_fetch_array($query2)) {
                                $id_laboratorium_rincian  = $data2['id_laboratorium_rincian'];
                                $id_referensi_pemeriksaan = $data2['id_referensi_pemeriksaan'];
                                $nama_pemeriksaan         = $data2['nama_pemeriksaan'];
                                $interpertasi             = $data2['interpertasi'] ?? '-';
                                $conclusion             = $data2['conclusion'] ?? '-';
                                $keterangan               = $data2['keterangan'] ?? '-';

                                // Jika Sudah Punya Spesimen
                                $label_spesimen_pemeriksaan = "-";
                                if(!empty($data2['id_laboratorium_spesimen'])){
                                    $id_laboratorium_spesimen   = $data2['id_laboratorium_spesimen'];
                                    $label_spesimen_pemeriksaan = 'LAB-SPC-'.$id_laboratorium_spesimen.'';
                                }

                                // Inisialiasi Tombol
                                if(empty($data2['hasil'])){
                                    $label_hasil ='-';
                                }else{
                                    $label_hasil = $data2['hasil'] ?? '-';
                                }

                                // Satuan
                                if(empty($data2['id_referensi_pemeriksaan'])){
                                    $unit_satuan = '-';
                                }else{
                                    $id_referensi_pemeriksaan = $data2['id_referensi_pemeriksaan'];
                                    $unit_satuan =  GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'unit_display');
                                }
                                // Mencari Nilai Normal
                                $nilai_normal_pemeriksaan = [];
                                $result_interpertation_type = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'result_interpertation_type');
                                if($result_interpertation_type=="Range"){
                                    $QryRange = mysqli_query($Conn, "SELECT * FROM referensi_range WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan' AND normal_value=1");
                                    while ($dataRange = mysqli_fetch_array($QryRange)) {
                                        
                                        $jenis_kelamin = $dataRange['jenis_kelamin'];
                                        $nilai_min     = $dataRange['nilai_min'];
                                        $nilai_max     = $dataRange['nilai_max'];
                                        $operator      = $dataRange['operator'];

                                        // trim
                                        $nilai_min = rtrim(rtrim(number_format($dataRange['nilai_min'], 2, ',', '.'), '0'), ',');
                                        $nilai_max = rtrim(rtrim(number_format($dataRange['nilai_max'], 2, ',', '.'), '0'), ',');

                                        // Jika usia tidak kosong
                                        if(!empty($dataRange['umur_kategori'])){
                                            $umur_kategori  = $dataRange['umur_kategori'];
                                            if(empty($dataRange['umur_max'])){
                                                $umur_min  = $dataRange['umur_min'];
                                                $umur_unit = $dataRange['umur_unit'];
                                                $notasi_usia = "≥ $umur_min $umur_unit";
                                            }else{
                                                $umur_min  = $dataRange['umur_min'];
                                                $umur_max  = $dataRange['umur_max'];
                                                $umur_unit = $dataRange['umur_unit'];
                                                $notasi_usia = "$umur_min - $umur_max $umur_unit";
                                            }

                                            $label_usia = "($notasi_usia / $umur_kategori)";
                                        }else{
                                            $label_usia = "";
                                        }

                                        // Jika jenis Kelamin Tidak All
                                        if($dataRange['jenis_kelamin']!=="All"){
                                            if($dataRange['jenis_kelamin']=="Laki-laki"){
                                                $label_jenis_kelamin = "(L)";
                                            }
                                            if($dataRange['jenis_kelamin']=="Perempuan"){
                                                $label_jenis_kelamin = "(P)";
                                            }
                                        }else{
                                            $label_jenis_kelamin = "";
                                        }

                                        // Jika Operator More
                                        if($operator=="More"){
                                            $label_nilai = "≥ $nilai_min";
                                        }else{
                                            $label_nilai = "$nilai_min - $nilai_max";
                                        }

                                        $nilai_normal_pemeriksaan[] = '-> '.$label_nilai.' '.$unit_satuan.' '.$label_usia.' '.$label_jenis_kelamin;
                                    }
                                }
                                if(empty($nilai_normal_pemeriksaan)){ $nilai_normal_pemeriksaan = "-"; }else{ $nilai_normal_pemeriksaan = implode("<br>", $nilai_normal_pemeriksaan); }

                                
                                // Tampilkan Hasil
                                echo '
                                    <tr>
                                        <td></td>
                                        <td align="center">'.$no.'.'.$no2.'</td>
                                        <td>'.$nama_pemeriksaan.'</td>
                                        <td align="center">'.$label_spesimen_pemeriksaan.'</td>
                                        <td align="center">'.$label_hasil.'</td>
                                        <td align="center">'.$unit_satuan.'</td>
                                        <td><small>'.$nilai_normal_pemeriksaan.'</small></td>
                                        <td align="center">'.$interpertasi.'</td>
                                        <td align="center">'.$conclusion.'</td>
                                    </tr>
                                ';

                                $no2++;
                            }

                            $no++;
                        }
                    }
                ?>
            </table>
        </p>
        <p>
           <b> C. Kesimpulan & Diagnosis</b>
           <?php
                // Default value
                $id_laboratorium_diagnostic = "-";
                $conclusion                 = "-";
                $clinical                   = "-";
                $icd_10_code                = "-";
                $icd_10_display             = "-";
                $icd_10_system              = "-";

                // Query
                $QryDiagnostic = $Conn->prepare("SELECT * FROM laboratorium_diagnostic WHERE id_laboratorium = ?");
                $QryDiagnostic->bind_param("s", $id_laboratorium);

                if (!$QryDiagnostic->execute()) {
                    echo '
                        <div class="alert alert-danger text-center">
                            <small>Terjadi kesalahan pada saat membuka data Diagnostic!<br>
                            Keterangan : '.$Conn->error.'</small>
                        </div>
                    ';
                    exit;
                }

                $ResultDiagnostic = $QryDiagnostic->get_result();
                $DataDiagnostic   = $ResultDiagnostic->fetch_assoc();
                $QryDiagnostic->close();

                if (!empty($DataDiagnostic)) {
                    $id_laboratorium_diagnostic = $DataDiagnostic['id_laboratorium_diagnostic'];
                    $conclusion                 = $DataDiagnostic['conclusion'];
                    $clinical                   = $DataDiagnostic['clinical'];
                    $icd_10_code                = $DataDiagnostic['icd_10_code'];
                    $icd_10_display             = $DataDiagnostic['icd_10_display'];
                    $icd_10_system              = $DataDiagnostic['icd_10_system'];
                }
            ?>
            <table class="tabel_diagnostik">
                <tr>
                    <td width="33%">
                        <b>Klinis pasien :</b>
                        <p>
                            <small><?php echo $clinical; ?></small>
                        </p>
                    </td>
                    <td width="33%">
                        <b>Kesimpulan / <i>Conclusion</i> :</b>
                        <p>
                            <small><?php echo $conclusion; ?></small>
                        </p>
                    </td>
                    <td width="33%">
                        <b>Diagnosis</i> :</b>
                        <p>
                            <small><?php echo "$icd_10_code - $icd_10_display"; ?></small>
                        </p>
                    </td>
                </tr>
            </table>
        </p>
        <?php
            // Kode IHS Dokter dan Petugas
            $ihs_dokter_pengirim = $Data['ihs_dokter_pengirim'];
            $ihs_dokter_penerima = $Data['ihs_dokter_penerima'];
            $ihs_petugas         = $Data['ihs_petugas'];

            // Pencarian Tanda Tangan
            $signature_pengirim = GetDetailData($Conn, 'referensi_signature', 'ihs', $ihs_dokter_pengirim, 'base_64_ttd');
            $signature_penerima = GetDetailData($Conn, 'referensi_signature', 'ihs', $ihs_dokter_penerima, 'base_64_ttd');
            $signature_petugas  = GetDetailData($Conn, 'referensi_signature', 'ihs', $ihs_petugas, 'base_64_ttd');
        ?>
        <table width="100%" class="signature">
            <tr>
                <td width="10%" align="center">
                    <?php
                        echo $tampilan_qr_code;
                    ?>
                </td>
                <td width="30%" align="center">
                    Petugas Laboratorium<br>
                    <?php
                        if($show_signature!==true){
                            echo "<br><br><br><br>";
                        }else{
                            if(!empty($Data['ihs_petugas'])){
                                if(empty($signature_petugas)){
                                    echo "<br><br><br><br>";
                                }else{
                                    echo '<img src="'.$signature_petugas.'" class="border border-1 border-primary" width="100%">';
                                }
                            }else{
                                echo "<br><br><br><br>";
                            }
                        }
                        echo "($nama_petugas)";
                    ?>
                </td>
                <td width="30%" align="center">
                    Dokter Pengirim<br>
                    <?php
                        if($show_signature!==true){
                            echo "<br><br><br><br>";
                        }else{
                            if(!empty($Data['ihs_dokter_pengirim'])){
                                if(empty($signature_pengirim)){
                                    echo "<br><br><br><br>";
                                }else{
                                    echo '<img src="'.$signature_pengirim.'" class="border border-1 border-primary" width="100%">';
                                }
                            }else{
                                echo "<br><br><br><br>";
                            }
                        }
                        echo "($nama_dokter_pengirim)";
                    ?>
                </td>
                <td width="30%" align="center">
                    Dokter Penerima<br>
                    <?php
                        if($show_signature!==true){
                            echo "<br><br><br><br>";
                        }else{
                            if(!empty($Data['ihs_dokter_penerima'])){
                                if(empty($signature_penerima)){
                                    echo "<br><br><br><br>";
                                }else{
                                    echo '<img src="'.$signature_penerima.'" class="border border-1 border-primary" width="100%">';
                                }
                            }else{
                                echo "<br><br><br><br>";
                            }
                        }
                        echo "($nama_dokter_penerima)";
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php
    $htmlHasil = ob_get_clean();

    if ($format_hasil === "PDF") {
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

        try {
            $mpdf = new \Mpdf\Mpdf(array(
                'mode' => 'utf-8',
                'format' => array($width, $height),
                'margin_top' => $margin_top,
                'margin_bottom' => $margin_bottom,
                'margin_left' => $margin_left,
                'margin_right' => $margin_right
            ));
            $mpdf->WriteHTML($htmlHasil);
            $fileName = 'Hasil-Lab-' . preg_replace('/[^A-Za-z0-9\-]/', '_', (string) $id_laboratorium) . '.pdf';
            $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE);
            exit;
        } catch (\Throwable $e) {
            echo 'Gagal membuat PDF: ' . $e->getMessage();
            exit;
        }
    }

    echo $htmlHasil;
    exit;
?>
