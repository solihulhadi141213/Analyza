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
    $kode_dokter_pengirim = $Data['kode_dokter_pengirim'] ?? '';
    $ihs_dokter_pengirim  = $Data['ihs_dokter_pengirim'] ?? '';
    $nama_dokter_pengirim = $Data['nama_dokter_pengirim'] ?? '';
    $datetime_diminta     = $Data['datetime_diminta'] ?? '';
    $datetime_diterima    = $Data['datetime_diterima'] ?? '';
    $datetime_spesimen    = $Data['datetime_spesimen'] ?? '';
    $datetime_hasil       = $Data['datetime_hasil'] ?? '';
    $diagnosis            = $Data['diagnosis'];

    // Buka Informasi Petugas
    $Qry2 = $Conn->prepare("SELECT * FROM access WHERE id_access = ?");
    $Qry2->bind_param("i", $SessionIdAccess);
    if (!$Qry2->execute()) {
        $error2=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data akses petugas!<br>Keterangan : '.$error2.'</small>
            </div>
        ';
        exit;
    }
    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    //Buat Variabel
    $access_ihs  = $Data2['access_ihs'];
    $access_name = $Data2['access_name'];
   

    // Menampilkan FORM
    echo '
        <input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">
    ';
    
    // Informasi Umum
    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>A. Waktu Pengambilan Spesimen</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tanggal_spesimen"><small>Tanggal Pengambilan</small></label>
            </div>
            <div class="col-md-8">
                <input type="date" name="tanggal_spesimen" id="tanggal_spesimen" class="form-control" value="'.date('Y-m-d').'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="jam_spesimen"><small>Pukul/Jam Pengambilan</small></label>
            </div>
            <div class="col-md-8">
                <input type="time" name="jam_spesimen" id="jam_spesimen" class="form-control" value="'.date('H:i').'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>B. Petugas Pengambil Spesimen</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="collector_name"><small>Nama Petugas</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="collector_name" id="collector_name" class="form-control" value="'.$access_name.'" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="collector_ihs"><small>ID IHS Petugas</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="collector_ihs" id="collector_ihs" class="form-control" value="'.$access_ihs.'" required>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>C. Informasi Spesimen</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_jenis_spesimen"><small>Nama/Jenis Spesimen</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_jenis_spesimen" id="id_referensi_jenis_spesimen" class="form-control" required>
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_metode_sample"><small>Metode Pengambilan</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_metode_sample" id="id_referensi_metode_sample" class="form-control" required>
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_body_site"><small>Lokasi Tubuh (<i>Body Site</i>)</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_body_site" id="id_referensi_body_site" class="form-control" required>
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_referensi_container"><small>Nama/Jenis Kemasan (<i>Container</i>)</small></label>
            </div>
            <div class="col-md-8">
                <select name="id_referensi_container" id="id_referensi_container" class="form-control" required>
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="quantity_value"><small>Jumlah / Volume Spesimen</small></label>
            </div>
            <div class="col-md-8">
                <div class="">
                    <div class="input-group mb-3">
                      <input type="number" min="0" step="0.01" name="quantity_value" id="quantity_value" class="form-control" required>
                      <span class="input-group-text" id="quantity_unit">{Unit}</span>
                    </div>
                </div>
            </div>
        </div>
    ';
?>
<div class="row mb-3 mt-3">
    <div class="col-12">
        <b><small>E. Parameter Pemeriksaan</small></b>
    </div>
    <div class="col-12">
        <small><small>Pilih parameter pemeriksaan yang akan dihasilkan oleh spesimen ini.</small></small>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <td class="text-center"><b>Pilih</b></td>
                        <td class="text-left"><b>Parameter Pemeriksaan</b></td>
                        <td class="text-left"><b>Kategori</b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Jumlah Rincian Pemeriksaan
                        $jumlah_pemeriksaan = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium'"));
                        if(empty($jumlah_pemeriksaan)){
                            echo '
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <small class="text-danger">Daftar Rincian Pemeriksaan Tidak Ditemukan</small>
                                    </td>
                                </tr>
                            ';
                        }else{
                            // Menampilkan Daftar Pemeriksaan
                            $query = mysqli_query($Conn, "SELECT * FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium'");
                            while ($data = mysqli_fetch_array($query)) {
                                $id_laboratorium_rincian  = $data['id_laboratorium_rincian'];
                                $id_referensi_pemeriksaan = $data['id_referensi_pemeriksaan'];
                                $id_laboratorium_spesimen = $data['id_laboratorium_spesimen'];
                                $nama_pemeriksaan         = $data['nama_pemeriksaan'];
                                $category_pemeriksaan     = $data['category_pemeriksaan'];

                                // Jika Sudah Terhubung Dengan Spesimen
                                if(!empty($data['id_laboratorium_spesimen'])){
                                    echo '
                                        <tr class="table table-active">
                                            <td class="text-center text text-grayish">
                                                <i class="bi bi-check-circle"></i>
                                            </td>
                                            <td class="text-left">
                                                <small class="text text-grayish">'.$nama_pemeriksaan.'</small>
                                            </td>
                                            <td class="text-left">
                                                <small class="text text-grayish">'.$category_pemeriksaan.'</small>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    echo '
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" id="rincian_terpilih'.$id_laboratorium_rincian.'" checked name="rincian_terpilih[]" value="'.$id_laboratorium_rincian.'">
                                            </td>
                                            <td class="text-left">
                                                <label for="rincian_terpilih'.$id_laboratorium_rincian.'">
                                                    <small class="text text-dark">'.$nama_pemeriksaan.'</small>
                                                </label>
                                            </td>
                                            <td class="text-left">
                                                <label for="rincian_terpilih'.$id_laboratorium_rincian.'">
                                                    <small class="text text-dark">'.$category_pemeriksaan.'</small>
                                                </label>
                                            </td>
                                        </tr>
                                    ';
                                }
                                
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>