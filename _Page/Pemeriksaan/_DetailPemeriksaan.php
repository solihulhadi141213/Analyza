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
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
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
    $id_pasien             = $Data['id_pasien'] ?? '';
    $id_kunjungan          = $Data['id_kunjungan'] ?? '';
    $ihs_pasien            = $Data['ihs_pasien'] ?? '';
    $id_encounter          = $Data['id_encounter'] ?? '';
    $nama                  = $Data['nama'] ?? '';
    $gender                = $Data['gender'] ?? '';
    $tanggal_lahir         = $Data['tanggal_lahir'] ?? '';
    $tujuan                = $Data['tujuan'] ?? '';
    $pembayaran            = $Data['pembayaran'] ?? '';
    $fakses                = $Data['fakses'] ?? '';
    $unit                  = $Data['unit'] ?? '';
    $priority              = $Data['priority'] ?? '';
    $puasa                 = $Data['puasa'] ?? '0';
    $status                = $Data['status'] ?? '';
    $kode_dokter_pengirim  = $Data['kode_dokter_pengirim'] ?? '';
    $ihs_dokter_pengirim   = $Data['ihs_dokter_pengirim'] ?? '';
    $nama_dokter_pengirim  = $Data['nama_dokter_pengirim'] ?? '';
    $datetime_diminta      = $Data['datetime_diminta'] ?? '';
    $datetime_diterima     = $Data['datetime_diterima'] ?? '';
    $datetime_spesimen     = $Data['datetime_spesimen'] ?? '';
    $datetime_hasil        = $Data['datetime_hasil'] ?? '';

    $label_puasa = ((string)$puasa === '1') ? 'Puasa' : 'Tidak Puasa';
    $tanggal_lahir_label = !empty($tanggal_lahir) ? date('d/m/Y', strtotime($tanggal_lahir)) : '-';
    $datetime_diminta_label = formatDateTimeStrict($datetime_diminta);
    $datetime_diterima_label = formatDateTimeStrict($datetime_diterima);
    $datetime_spesimen_label = formatDateTimeStrict($datetime_spesimen);
    $datetime_hasil_label = formatDateTimeStrict($datetime_hasil);
?>
<div class="row mb-3">
    <div class="col-md-12 mb-3 text-end">
        <button type="button" class="btn btn-md btn-dark btn-floating" id="kembali_ke_data" title="Kembali Ke Tabel Pemeriksaan">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-outline-dark reload_detail" title="Reload Data">
            <i class="bi bi-repeat"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-outline-primary modal_edit" data-id="<?php echo $id_laboratorium; ?>" title="Edit Pemeriksaan">
            <i class="bi bi-pencil"></i>
        </button>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <b class="card-title"># Detail Pemeriksaan</b>
            </div>
            <div class="card-body">
                <?php
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
                    ';
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <b class="card-title"># Rincian Pemeriksaan</b>
            </div>
            <div class="card-body">
                <div class="table table-responsive table-sm">
                    <table class="table table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td colspan="2"><b>Permintaan Pemeriksaan</b></td>
                                <td><b>Hasil</b></td>
                                <td><b>Interpertasi</b></td>
                                <td><b>Keterangan</b></td>
                                <td class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Service Request SATUSEHAT">
                                    <b>SR</b>
                                </td>
                                <td class="text-center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $jumlah_rincian = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium'"));
                                if(empty($jumlah_rincian)){
                                    echo '
                                        <tr>
                                            <td colspan="8" class="text-center">
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
                                                <td class="text-center"><small>'.$no.'</small></td>
                                                <td class="text-left" colspan="6"><small>'.$category_pemeriksaan.'</small></td>
                                            </tr>
                                        ';

                                        // Menampilkan 'laboratorium_rincian' berdasarkan category_pemeriksaan
                                        $no2 = 1;
                                        $query2 = mysqli_query($Conn, "SELECT * FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium' AND category_pemeriksaan='$category_pemeriksaan' ORDER BY nama_pemeriksaan ASC");
                                        while ($data2 = mysqli_fetch_array($query2)) {
                                            $id_laboratorium_rincian = $data2['id_laboratorium_rincian'];
                                            $nama_pemeriksaan        = $data2['nama_pemeriksaan'];
                                            $hasil                   = $data2['hasil'] ?? '-';
                                            $interpertasi            = $data2['interpertasi'] ?? '-';
                                            $keterangan              = $data2['keterangan'] ?? '-';

                                            // Routing ServiceRequest
                                            if(empty($data2['id_service_request'])){
                                                $sr = '
                                                    <button type="button" class="btn btn-sm btn-floating btn-outline-secondary modal_kirim_service_request" data-id="'.$id_laboratorium_rincian.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Kirim ServiceRequest">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                ';
                                            }else{
                                                $sr = '
                                                    <button type="button" class="btn btn-sm btn-floating btn-outline-success modal_detail_service_request" data-id="'.$id_laboratorium_rincian.'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail ServiceRequest">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                ';
                                            }
                                            
                                            echo '
                                                <tr>
                                                    <td class="text-center"></td>
                                                    <td class="text-center">
                                                        <small class="text text-grayish">'.$no.'.'.$no2.'</small>
                                                    </td>
                                                    <td class="text-left">
                                                        <small class="text text-grayish">'.$nama_pemeriksaan.'</small>
                                                    </td>
                                                    <td class="text-left">
                                                        <small class="text text-grayish">'.$hasil.'</small>
                                                    </td>
                                                    <td class="text-left">
                                                        <small class="text text-grayish">'.$interpertasi.'</small>
                                                    </td>
                                                    <td class="text-left">
                                                        <small class="text text-grayish">'.$keterangan.'</small>
                                                    </td>
                                                    <td class="text-center">'.$sr.'</td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                            <li class="dropdown-header text-start">
                                                                <h6>Option</h6>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item modal_edit_rincian" href="javascript:void(0)" data-id="'.$id_laboratorium_rincian.'">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item modal_hapus_rincian" href="javascript:void(0)" data-id="'.$id_laboratorium_rincian.'">
                                                                    <i class="bi bi-x"></i> Hapus
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            ';

                                            $no2++;
                                        }

                                        $no++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manakemen Spesimen -->
         <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <b># Spesimen</b>
                    </div>
                    <div class="col-4 text-end">
                        <button type="button" class="btn btn-sm btn-floating btn-secondary">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td class="text-center"><b>No</b></td>
                                <td><b>Nama Spesimen</b></td>
                                <td><b><i>Method</i></b></td>
                                <td><b><i>Body</i></b></td>
                                <td><b><i>Quantity</i></b></td>
                                <td><b><i>Container</i></b></td>
                                <td class="text-center"><b><i>Opsi</i></b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Jumlah Data Spesimen
                                $jumlah_spesimen = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_spesimen FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium'"));
                                if(empty($jumlah_spesimen)){
                                    echo '
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <small class="text-danger">Tidak Ada Data Spesimen Yang Ditampilkan</small>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    $no3 = 1;
                                    $query3 = mysqli_query($Conn, "SELECT * FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium' ORDER BY nama_spesimen ASC");
                                    while ($data3 = mysqli_fetch_array($query3)) {
                                        $id_laboratorium_spesimen = $data3['id_laboratorium_spesimen'];
                                        $id_speciment = $data3['id_speciment'];
                                        $nama_spesimen = $data3['nama_spesimen'] ?? '-';
                                        $display_spesimen = $data3['display_spesimen'] ?? '-';
                                        $nama_metode_sample = $data3['nama_metode_sample'] ?? '-';
                                        
                                        echo '
                                            <tr>
                                                <td class="text-center"></td>
                                                <td class="text-center">
                                                    <small class="text text-grayish">'.$no3.'</small>
                                                </td>
                                                <td class="text-left">
                                                    <small class="text text-grayish">'.$nama_spesimen.'</small>
                                                </td>
                                                <td class="text-left">
                                                    <small class="text text-grayish">'.$nama_metode_sample.'</small>
                                                </td>
                                                <td class="text-left">
                                                    <small class="text text-grayish">'.$interpertasi.'</small>
                                                </td>
                                                <td class="text-left">
                                                    <small class="text text-grayish">'.$keterangan.'</small>
                                                </td>
                                                <td class="text-center">'.$sr.'</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                        <li class="dropdown-header text-start">
                                                            <h6>Option</h6>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item modal_edit_rincian" href="javascript:void(0)" data-id="'.$id_laboratorium_spesimen.'">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item modal_hapus_rincian" href="javascript:void(0)" data-id="'.$id_laboratorium_spesimen.'">
                                                                <i class="bi bi-x"></i> Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        ';

                                        $no3++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
         </div>
    </div>
</div>