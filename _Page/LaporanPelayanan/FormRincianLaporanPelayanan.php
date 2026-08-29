<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    if(empty($_POST['keyword'])){
        echo '
            <div class="alert alert-danger">
                <small>Kata Kunci Rincian Laporan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['periode'])){
        echo '
            <div class="alert alert-danger">
                <small>Periode Laporan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    $label_periode = "";
    $keyword = $_POST['keyword'];
    $periode = $_POST['periode'];
    if($periode=="Tahunan"){
        $label_periode = date('F Y', strtotime($keyword));
    }
    if($periode=="Bulanan"){
        $label_periode = date('d F Y', strtotime($keyword));
    }
    $jumlah_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE datetime_diminta like '%$keyword%'"));
?>
<input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="periode" value="<?php echo htmlspecialchars($periode, ENT_QUOTES, 'UTF-8'); ?>">
<div class="row mb-2">
    <div class="col-12 text-center">
        <h4><b>RINCIAN LAPORAN PELAYANAN</b></h4>
        <i>Periode <?php echo $label_periode; ?></i>
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-center" valign="middle" rowspan="2"><b>No</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Nama Pasien</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>No.RM</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Gender</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Tujuan</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Pembayaran</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Priority</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Status</b></td>
                        <td class="text-center" valign="middle" colspan="2"><b>Tanggal/Jam</b></td>
                        <td class="text-center" valign="middle" rowspan="2"><b>Durasi</b></td>
                    </tr>
                    <tr>
                        <td class="text-center"><b>Diminta</b></td>
                        <td class="text-center"><b>Selesai</b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(empty($jumlah_data)){
                            echo '
                                <tr class="text-center" colspan="9">
                                    <small class="text-danger">Data Layanan Tidak Ditemukan</small>
                                </tr>
                            ';
                        }else{
                            $no =1;
                            $query = mysqli_query($Conn, "SELECT * FROM laboratorium WHERE datetime_diminta like '%$keyword%' ORDER BY datetime_diminta DESC");
                            while ($data = mysqli_fetch_array($query)) {
                                $id_laboratorium  = $data['id_laboratorium'];
                                $id_pasien        = $data['id_pasien'];
                                $nama             = $data['nama'];
                                $gender           = $data['gender'];
                                $tanggal_lahir    = $data['tanggal_lahir'];
                                $datetime_diminta = $data['datetime_diminta'];
                                $datetime_hasil   = $data['datetime_hasil'];
                                $tujuan           = $data['tujuan'];
                                $pembayaran       = $data['pembayaran'];
                                $priority         = $data['priority'];
                                $status           = $data['status'];
                                // Routing Tanggal Diminta Dan Hasil
                                if(!empty($data['datetime_diminta'])){
                                    $label_datetime_diminta = date('d/m/Y H:i', strtotime($datetime_diminta));
                                }else{
                                    $label_datetime_diminta = "-";
                                }
                                if(!empty($data['datetime_hasil'])){
                                    $label_datetime_hasil = date('d/m/Y H:i', strtotime($datetime_hasil));
                                }else{
                                    $label_datetime_hasil = "-";
                                }

                                // Apabila Waktu hasil Tidak Kosong Maka Hitung Durasi
                                $durasi = "";
                                if(!empty($data['datetime_hasil'])){
                                    $waktu_mulai = strtotime($datetime_diminta);
                                    $waktu_selesai = strtotime($datetime_hasil);
                                    if($waktu_mulai!==false && $waktu_selesai!==false){
                                        $selisih_detik = $waktu_selesai - $waktu_mulai;
                                        if($selisih_detik<0){
                                            $selisih_detik = 0;
                                        }

                                        if($selisih_detik < 60){
                                            $durasi = $selisih_detik . ' Detik';
                                        }elseif($selisih_detik < 3600){
                                            $nilai = round($selisih_detik / 60, 2);
                                            $durasi = $nilai . ' Menit';
                                        }elseif($selisih_detik < 86400){
                                            $nilai = round($selisih_detik / 3600, 2);
                                            $durasi = $nilai . ' Jam';
                                        }elseif($selisih_detik < 2592000){
                                            $nilai = round($selisih_detik / 86400, 2);
                                            $durasi = $nilai . ' Hari';
                                        }elseif($selisih_detik < 31536000){
                                            $nilai = round($selisih_detik / 2592000, 2);
                                            $durasi = $nilai . ' Bulan';
                                        }else{
                                            $nilai = round($selisih_detik / 31536000, 2);
                                            $durasi = $nilai . ' Tahun';
                                        }
                                    }
                                }

                                if(!empty($durasi)){
                                    $label_durasi = '<i>'.$durasi.'</i>';
                                }else{
                                    $label_durasi = '<i>-</i>';
                                }

                                // Label Status
                                $label_priority = '<span class="text text-danger">None</span>';
                                if($priority=="routine"){
                                    $label_priority = '<span class="text text-grayish">Biasa</span>';
                                }
                                if($priority=="urgent"){
                                    $label_priority = '<span class="text text-info">Segera</span>';
                                }
                                if($priority=="stat"){
                                    $label_priority = '<span class="text text-warning">Gawat</span>';
                                }
                                echo '
                                    <tr>
                                        <td class="text-center"><small>'.$no.'</small></td>
                                        <td class="text-left"><small>'.$nama.'</small></td>
                                        <td class="text-center"><small>'.$id_pasien.'</small></td>
                                        <td class="text-center"><small>'.$gender.'</small></td>
                                        <td class="text-center"><small>'.$tujuan.'</small></td>
                                        <td class="text-center"><small>'.$pembayaran.'</small></td>
                                        <td class="text-center"><small>'.$label_priority.'</small></td>
                                        <td class="text-center"><small>'.$status.'</small></td>
                                        <td class="text-left"><small>'.$label_datetime_diminta.'</small></td>
                                        <td class="text-left"><small>'.$label_datetime_hasil.'</small></td>
                                        <td class="text-center"><small>'.$label_durasi.'</small></td>
                                    </tr>
                                ';
                                $no++;
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
