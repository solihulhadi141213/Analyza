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
            <script>
                $("#tombol_hapus_pemeriksaan").prop("disabled", true);
            </script>
        ';
        exit;
    }

    //id_laboratorium wajib terisi
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
            <script>
                $("#tombol_hapus_pemeriksaan").prop("disabled", true);
            </script>
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
            <script>
                $("#tombol_hapus_pemeriksaan").prop("disabled", true);
            </script>
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
            <script>
                $("#tombol_hapus_pemeriksaan").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Buat Variabel
    $id_pasien            = $Data['id_pasien'] ?? '';
    $id_kunjungan         = $Data['id_kunjungan'] ?? '';
    $ihs_pasien           = $Data['ihs_pasien'] ?? '';
    $id_encounter         = $Data['id_encounter'] ?? '';
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
    $kode_dokter_pengirim = $Data['kode_dokter_pengirim'] ?? '-';
    $ihs_dokter_pengirim  = $Data['ihs_dokter_pengirim'] ?? '-';
    $nama_dokter_pengirim = $Data['nama_dokter_pengirim'] ?? '-';
    $nama_dokter_penerima = $Data['nama_dokter_penerima'] ?? '-';
    $kode_dokter_penerima = $Data['kode_dokter_penerima'] ?? '-';
    $ihs_dokter_penerima  = $Data['ihs_dokter_penerima'] ?? '-';
    $datetime_diminta     = $Data['datetime_diminta'] ?? '';
    $datetime_diterima    = $Data['datetime_diterima'] ?? '';
    $datetime_spesimen    = $Data['datetime_spesimen'] ?? '';
    $datetime_hasil       = $Data['datetime_hasil'] ?? '';
    $diagnosis            = $Data['diagnosis'];
    $keterangan            = $Data['keterangan'] ?? '-';
    $label_puasa = ((string)$puasa === '1') ? 'Puasa' : 'Tidak Puasa';
    $tanggal_lahir_label     = !empty($tanggal_lahir) ? date('d/m/Y', strtotime($tanggal_lahir)) : '-';
    $datetime_diminta_label  = formatDateTimeStrict($datetime_diminta);
    $datetime_diterima_label = formatDateTimeStrict($datetime_diterima);
    $datetime_spesimen_label = formatDateTimeStrict($datetime_spesimen);
    $datetime_hasil_label    = formatDateTimeStrict($datetime_hasil);

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
    // Validasi Status
    if($Data['status']=="Selesai"){
         echo '
            <div class="alert alert-danger text-center">
                <small>Data Tidak Bisa Dihapus Karena Pemeriksaan Sudah Dilaksanakan!</small>
            </div>
        ';
        exit;
    }
    echo '
        <input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">
        <div class="row mb-2">
            <div class="col-4"><small>No.RM</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$id_pasien.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>ID IHS</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$ihs_pasien.'</small>
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
            <div class="col-4"><small>Tanggal Lahir</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$tanggal_lahir_label.'</small>
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
            <div class="col-12">
                <div class="alert alert-danger text-center">
                    <small>
                        <b>PENTING!</b><br>
                        <small>Menghapus data pemeriksaan akan menghapus semua rincian dan informasi pendukung lainnya.</small><br>
                        <p>Apakah Anda Yakin Akan Menghapus Data Pemeriksaan Tersebut?</p>
                    </small>
                </div>
            </div>
        </div>
        <script>
            $("#tombol_hapus_pemeriksaan").prop("disabled", false);
        </script>
    ';
?>