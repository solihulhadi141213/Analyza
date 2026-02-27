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

    // Ekstract Diagnosis
    $DiagnosisArry = json_decode($diagnosis, true);
    $diagnosis_code    = $DiagnosisArry['code'] ?? '-';
    $diagnosis_display = $DiagnosisArry['display'] ?? '-';
    $diagnosis_system  = $DiagnosisArry['system'] ?? '-';

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

    //Buka Procedure
    $QryProcedure = $Conn->prepare("SELECT * FROM laboratorium_procedure WHERE id_laboratorium = ?");
    $QryProcedure->bind_param("s", $id_laboratorium);
    if (!$QryProcedure->execute()) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data procedure!<br>Keterangan : '.$Conn->error.'</small>
            </div>
        ';
    }
    $ResultProcedure = $QryProcedure->get_result();
    $DataProcedure = $ResultProcedure->fetch_assoc();
    $QryProcedure->close();
    if (empty($DataProcedure)) {
        $id_procedure          = "-";
        $procedure_description = "-";
        $procedure_display     = "-";
        $procedure_code        = "-";
        $procedure_system      = "-";
    }else{
        $id_procedure          = $DataProcedure['id_procedure'] ?? '-';
        $procedure_description = $DataProcedure['procedure_description'] ?? '-';
        $procedure_display     = $DataProcedure['procedure_display'] ?? '-';
        $procedure_code        = $DataProcedure['procedure_code'] ?? '-';
        $procedure_system      = $DataProcedure['procedure_system'] ?? '-';
    }

    // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';

    echo '<div class="row">';
    
    // Kolom 1
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>A. Informasi Pasien</b></small>
                </div>
            </div>
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
        </div>
    ';

    // Kolom Ke 2
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>B. Informasi Kunjungan</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ID Kunjungan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$id_kunjungan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ID Encounter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$id_encounter.'</small>
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
        </div>
    ';

    // Kolom Ke 3
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>C. Informasi Permintaan</b></small>
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
                <div class="col-4"><small>Status Puasa</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$label_puasa.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Status Pemeriksaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$status.'</small>
                </div>
            </div>
        </div>
    ';

    // Kolom Ke 4
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>D. Dokter Pengirim</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Dokter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_dokter_pengirim.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Dokter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$kode_dokter_pengirim.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>IHS Dokter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$ihs_dokter_pengirim.'</small>
                </div>
            </div>
        </div>
    ';
    // Kolom Ke 4
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>E. Dokter Penerima</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nama Dokter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_dokter_penerima.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kode Dokter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$kode_dokter_penerima.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>IHS Dokter</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$ihs_dokter_penerima.'</small>
                </div>
            </div>
        </div>
    ';


    // Kolom Ke 5
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>F. Diagnosis <i>(Reson Code)</i></b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$diagnosis_display.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$diagnosis_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$diagnosis_system.'</small>
                </div>
            </div>
        </div>
    ';

    // Kolom Ke 6
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>G. Tanggl/Jam <i>(Log Service)</i></b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Diminta</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_diminta_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Diterima</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_diterima_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Spesimen</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_spesimen_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Hasil</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_hasil_label.'</small>
                </div>
            </div>
        </div>
    ';

    // Kolom Ke 6
    echo '
        <div class="col-md-4 mb-3">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b>H. Status Puasa <i>(Procedure)</i></b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>ID procedure</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$id_procedure.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Description</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$procedure_description.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$procedure_display.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Code</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$procedure_code.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$procedure_system.'</small>
                </div>
            </div>
        </div>
    ';
    echo '
        <div class="col-md-4 mb-3">
            <div class="row">
                <div class="col-12 mb-2">
                    <small><b>I. Catatan / Keterangan Lain</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Keterangan </small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$keterangan.'</small>
                </div>
            </div>
        </div>
    ';

    // Jika Ditemukan Adanya Keterangan
    if(!empty($Data['keterangan'])){
        
    }

    echo '</div>';
    echo '
        <div class="row">
            <div class="col-12">
                <small><b>J. Rincian Pemeriksaan</b></small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr class="table-dark">
                        <th class="text-center"><b>No</b></th>
                        <th class="text-center"><b>Pemeriksaan</b></th>
                        <th class="text-center"><b>Kategori</b></th>
                        <th class="text-center"><b>Satuan</b></th>
                        <th class="text-center"><b>Metode Pemeriksaan</b></th>
                        <th class="text-center"><b>Result Type</b></th>
                        <th class="text-center"><b>Interpertation Type</b></th>
                        <th class="text-center"><b>Allow Age</b></th>
                        <th class="text-center"><b>Allow Sex</b></th>
                    </tr>
                </thead>
                <tbody>
    ';
    // Data rincian pemeriksaan
    $QryDetail = $Conn->prepare("SELECT * FROM laboratorium_rincian WHERE id_laboratorium = ? ORDER BY category_pemeriksaan ASC, nama_pemeriksaan ASC");
    $QryDetail->bind_param("s", $id_laboratorium);
    $QryDetail->execute();
    $ResultDetail = $QryDetail->get_result();

    if ($ResultDetail->num_rows === 0) {
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Belum ada rincian pemeriksaan</small>
                </td>
            </tr>
        ';
    } else {
        $no = 1;
        while ($DataDetail = $ResultDetail->fetch_assoc()) {
            $id_referensi_pemeriksaan = $DataDetail['id_referensi_pemeriksaan'];
            $category_pemeriksaan     = $DataDetail['category_pemeriksaan'] ?? '-';
            $nama_pemeriksaan         = $DataDetail['nama_pemeriksaan'] ?? '-';
            $metode_pemeriksaan       = $DataDetail['metode_pemeriksaan'] ?? '-';
            $interpertasi             = $DataDetail['interpertasi'] ?? '-';
            $keterangan               = $DataDetail['keterangan'] ?? '-';

            // Satuan
            $satuan                     = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'unit_display');
            $result_type                = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'result_type');
            $result_interpertation_type = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'result_interpertation_type');
            $allow_age                  = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_age');
            $allow_sex                  = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_sex');

            // Routing aalow Age dan sex
            if(empty($allow_age)){
                $allow_age = '<span class="text-danger">No</span>';
            }else{
                $allow_age = '<span class="text-success">Yes</span>';
            }
            if(empty($allow_sex)){
                $allow_sex = '<span class="text-danger">No</span>';
            }else{
                $allow_sex = '<span class="text-success">Yes</span>';
            }
            echo '
                <tr>
                    <td class="text-center"><small>'.$no.'</small></td>
                    <td><small>'.$nama_pemeriksaan.'</small></td>
                    <td><small>'.$category_pemeriksaan.'</small></td>
                    <td><small>'.$satuan.'</small></td>
                    <td><small>'.$metode_pemeriksaan.'</small></td>
                    <td><small>'.$result_type.'</small></td>
                    <td><small>'.$result_interpertation_type.'</small></td>
                    <td><small>'.$allow_age.'</small></td>
                    <td><small>'.$allow_sex.'</small></td>
                </tr>
            ';
            $no++;
        }
    }

    echo '
                </tbody>
            </table>
        </div>
    ';
    $QryDetail->close();

    
?>
