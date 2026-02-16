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
    }else{
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
        $kode_dokter_pengirim = $Data['kode_dokter_pengirim'] ?? '';
        $ihs_dokter_pengirim  = $Data['ihs_dokter_pengirim'] ?? '';
        $nama_dokter_pengirim = $Data['nama_dokter_pengirim'] ?? '';
        $datetime_diminta     = $Data['datetime_diminta'] ?? '';
        $datetime_diterima    = $Data['datetime_diterima'] ?? '';
        $datetime_spesimen    = $Data['datetime_spesimen'] ?? '';
        $datetime_hasil       = $Data['datetime_hasil'] ?? '';
        $diagnosis            = $Data['diagnosis'];

        $label_puasa = ((string)$puasa === '1') ? 'Puasa' : 'Tidak Puasa';
        $tanggal_lahir_label = !empty($tanggal_lahir) ? date('d/m/Y', strtotime($tanggal_lahir)) : '-';
        $datetime_diminta_label = formatDateTimeStrict($datetime_diminta);
        $datetime_diterima_label = formatDateTimeStrict($datetime_diterima);
        $datetime_spesimen_label = formatDateTimeStrict($datetime_spesimen);
        $datetime_hasil_label = formatDateTimeStrict($datetime_hasil);

        // Ekstract Diagnosis
        $DiagnosisArry = json_decode($diagnosis, true);
        $diagnosis_code    = $DiagnosisArry['code'] ?? '-';
        $diagnosis_display = $DiagnosisArry['display'] ?? '-';
        $diagnosis_system  = $DiagnosisArry['system'] ?? '-';

        // Form Hidden
        echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';
       
        //Tampilkan Data
        echo '
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
            <div class="row mb-3 mt-3">
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
            <div class="row mb-3 mt-3">
                <div class="col-12">
                    <small><b>C. Informasi Permintaan</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Tanggal/Jam Diminta</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_diminta_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Priority</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$priority.'</small>
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
            <div class="row mb-2">
                <div class="col-4"><small>Tgl/Jam Diterima</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_diterima_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Tgl/Jam Spesimen</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_spesimen_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Tgl/Jam Hasil</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$datetime_hasil_label.'</small>
                </div>
            </div>
            <div class="row mb-3 mt-3">
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
            <div class="row mb-3 mt-3">
                <div class="col-12">
                    <small><b>D. Diagnosis <i>(Reson Code)</i></b></small>
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
        ';

        // Data rincian pemeriksaan
        $QryDetail = $Conn->prepare("
            SELECT
                id_referensi_pemeriksaan,
                nama_pemeriksaan,
                category_pemeriksaan,
                hasil,
                interpertasi,
                keterangan
            FROM laboratorium_rincian
            WHERE id_laboratorium = ?
            ORDER BY category_pemeriksaan ASC, nama_pemeriksaan ASC
        ");
        $QryDetail->bind_param("s", $id_laboratorium);
        $QryDetail->execute();
        $ResultDetail = $QryDetail->get_result();

        echo '
            <div class="row mb-3 mt-3">
                <div class="col-12">
                    <small><b>E. Rincian Pemeriksaan</b></small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center"><small>No</small></th>
                            <th><small>Kategori</small></th>
                            <th><small>Pemeriksaan</small></th>
                            <th><small>Hasil</small></th>
                            <th><small>Interpertasi</small></th>
                            <th><small>Keterangan</small></th>
                        </tr>
                    </thead>
                    <tbody>
        ';

        if ($ResultDetail->num_rows === 0) {
            echo '
                <tr>
                    <td colspan="6" class="text-center">
                        <small class="text-danger">Belum ada rincian pemeriksaan</small>
                    </td>
                </tr>
            ';
        } else {
            $no = 1;
            while ($DataDetail = $ResultDetail->fetch_assoc()) {
                $category_pemeriksaan = $DataDetail['category_pemeriksaan'] ?? '-';
                $nama_pemeriksaan = $DataDetail['nama_pemeriksaan'] ?? '-';
                $hasil = $DataDetail['hasil'] ?? '-';
                $interpertasi = $DataDetail['interpertasi'] ?? '-';
                $keterangan = $DataDetail['keterangan'] ?? '-';

                if ($hasil === '') { $hasil = '-'; }
                if ($interpertasi === '') { $interpertasi = '-'; }
                if ($keterangan === '') { $keterangan = '-'; }

                echo '
                    <tr>
                        <td class="text-center"><small>'.$no.'</small></td>
                        <td><small>'.$category_pemeriksaan.'</small></td>
                        <td><small>'.$nama_pemeriksaan.'</small></td>
                        <td><small>'.$hasil.'</small></td>
                        <td><small>'.$interpertasi.'</small></td>
                        <td><small>'.$keterangan.'</small></td>
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
    }

    // Jika Ditemukan Adanya Keterangan
    if(!empty($Data['keterangan'])){
        echo '
            <div class="row mb-3 mt-3">
                <div class="col-12 mb-2">
                    <small><b>F. Catatan / Keterangan Lain</b></small>
                </div>
                <div class="col-12 mb-2">
                    <small><i>('.$Data['keterangan'].')</i></small>
                </div>
            </div>
        ';
    }
?>
