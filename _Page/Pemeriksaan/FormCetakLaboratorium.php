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
    $datetime_diminta     = $Data['datetime_diminta'] ?? '';
    $datetime_diterima    = $Data['datetime_diterima'] ?? '';
    $datetime_spesimen    = $Data['datetime_spesimen'] ?? '';
    $datetime_hasil       = $Data['datetime_hasil'] ?? '';
    $keterangan            = $Data['keterangan'] ?? '-';

    $label_puasa = ((string)$puasa === '1') ? 'Puasa' : 'Tidak Puasa';
    $tanggal_lahir_label     = !empty($tanggal_lahir) ? date('d/m/Y', strtotime($tanggal_lahir)) : '-';
    $datetime_diminta_label  = formatDateTimeStrict($datetime_diminta);
    $datetime_diterima_label = formatDateTimeStrict($datetime_diterima);
    $datetime_spesimen_label = formatDateTimeStrict($datetime_spesimen);
    $datetime_hasil_label    = formatDateTimeStrict($datetime_hasil);

    // priority
    if($priority=="routine"){
        $label_priority = '<span class="badge badge-success">Biasa</span>';
    }else{
        if($priority=="urgent"){
            $label_priority = '<span class="badge badge-warning">Segera</span>';
        }else{
            $label_priority = '<span class="badge badge-danger">Darurat</span>';
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

    // Hitung Jumlah Rincian/Spesimen
    $jumlah_rincian  = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium'"));
    $jumlah_spesimen = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_spesimen FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium'"));
    // Ambil default pengaturan cetak dari file JSON
    $settingPath = __DIR__ . '/setting_cetak_hasil.json';
    $defaultWidth = 210;
    $defaultHeight = 330;
    $defaultMarginTop = 0.1;
    $defaultMarginBottom = 0.1;
    $defaultMarginLeft = 0.1;
    $defaultMarginRight = 0.1;
    $defaultShowHeader = true;
    $defaultShowSignature = false;

    if (file_exists($settingPath)) {
        $json = file_get_contents($settingPath);
        $setting = json_decode($json, true);
        if (is_array($setting)) {
            $selectedSetting = null;
            $isList = array_keys($setting) === range(0, count($setting) - 1);

            if ($isList) {
                foreach ($setting as $item) {
                    if (is_array($item) && (($item['id_access'] ?? '') === (string) $SessionIdAccess)) {
                        $selectedSetting = $item;
                        break;
                    }
                }
                if ($selectedSetting === null) {
                    foreach ($setting as $item) {
                        if (is_array($item) && (($item['id_access'] ?? '') === 'DEFAULT')) {
                            $selectedSetting = $item;
                            break;
                        }
                    }
                }
            } else {
                // Kompatibilitas format lama: object tunggal tanpa list.
                $selectedSetting = $setting;
            }

            if (is_array($selectedSetting)) {
                $printSetting = $selectedSetting['print_setting'] ?? $selectedSetting;
                if (is_array($printSetting)) {
                    $defaultWidth = $printSetting['width'] ?? $defaultWidth;
                    $defaultHeight = $printSetting['height'] ?? $defaultHeight;
                    $defaultMarginTop = $printSetting['margin_top'] ?? ($printSetting['margin-top'] ?? $defaultMarginTop);
                    $defaultMarginBottom = $printSetting['margin_bottom'] ?? ($printSetting['margin-bottom'] ?? $defaultMarginBottom);
                    $defaultMarginLeft = $printSetting['margin_left'] ?? ($printSetting['margin-left'] ?? $defaultMarginLeft);
                    $defaultMarginRight = $printSetting['margin_right'] ?? ($printSetting['margin-right'] ?? $defaultMarginRight);
                }
                if (array_key_exists('show_header', $selectedSetting)) {
                    $defaultShowHeader = (bool) $selectedSetting['show_header'];
                }
                if (array_key_exists('show_signature', $selectedSetting)) {
                    $defaultShowSignature = (bool) $selectedSetting['show_signature'];
                }
            }
        }
    }


    // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';

    echo '<div class="row">';
    
    // Kolom 1
    echo '
        <div class="container border-1 border-info rounded">
            <div class="row mb-2">
                <div class="col-4"><small>ID Laboratorium</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$id_laboratorium.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>No.RM / Reg</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$id_pasien.' / '.$id_kunjungan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Pasien</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jenis Kelamin</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$gender.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Usia Saat Pelayanan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$usia.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Tujuan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$tujuan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Pembayaran</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$pembayaran.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Faskes Pengirim</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$fakses.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Unit/Instalasi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$unit.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Priority</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$label_priority.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Status Pemeriksaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$status.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Dokter Pengirim</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_dokter_pengirim.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Dokter Penerima</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_dokter_penerima.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jumlah Rincian</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$jumlah_rincian.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jumlah Spesimen</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$jumlah_spesimen.'</small>
                </div>
            </div>
        </div>
    ';
?>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="height_hasil"><small>Lebar / Tinggi</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="height_hasil" name="height" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultHeight); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="width_hasil"><small>Panjang</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="width_hasil" name="width" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultWidth); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="margin_top_hasil"><small>Margin Atas</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_top_hasil" name="margin_top" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginTop); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="margin_bottom_hasil"><small>Margin Bawah</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_bottom_hasil" name="margin_bottom" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginBottom); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="margin_left_hasil"><small>Margin Kiri</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_left_hasil" name="margin_left" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginLeft); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
    <div class="col-md-6">
        <label for="margin_right_hasil"><small>Margin Kanan</small></label>
        <div class="input-group">
            <input type="number" min="0" step="0.01" class="form-control" id="margin_right_hasil" name="margin_right" placeholder="0.00" value="<?php echo htmlspecialchars((string) $defaultMarginRight); ?>">
            <div class="input-group-text">mm</div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="input-group">
            <input class="form-check-input mt-0" <?php echo $defaultShowHeader ? 'checked' : ''; ?> name="show_header" id="show_header" type="checkbox" value="true">
            <label for="show_header"><small>Tampilkan Header (Kop Surat)</small></label>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="input-group">
            <input class="form-check-input mt-0" <?php echo $defaultShowSignature ? 'checked' : ''; ?> name="show_signature" id="show_signature" type="checkbox" value="true">
            <label for="show_signature"><small>Tampilkan Tanda Tangan</small></label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
       <label for="format_hasil"><small>Format hasil</small></label>
        <select name="format_hasil" id="format_hasil" class="form-control">
            <option value="HTML">HTML</option>
            <option value="PDF">PDF</option>
        </select>
    </div>
</div>
