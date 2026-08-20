<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi id_kunjungan tidak boleh kosong
    if(!empty($_POST['id_kunjungan'])){

        // Buat Variabel id_kunjungan dan sanitasi
        $id_kunjungan = validateAndSanitizeInput($_POST['id_kunjungan']);

        // Buka URL SIMRS
        $status_connection_simrs = 1;
        $url_connection_simrs = GetDetailData($Conn,'connection_simrs','status_connection_simrs',$status_connection_simrs,'url_connection_simrs');

        //Dapatkan Token SIMRS
        $token = GetSimrsToken($Conn);

        // Jika Token Tidak Valid Dan Gagal Dibuat
        if ($token === false) {
            echo '
                <div class="alert alert-danger">
                    <small>Gagal mendapatkan token SIMRS!</small>
                </div>
            ';
            exit;
        }

        // Mulai CURL service API SIMRS Untuk Mendapatkan Detail Kunjungan
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/get_detail_kunjungan.php?id='.$id_kunjungan.'',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'token: '.$token.'',
                'X-API-Key: ••••••'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Ubah Response Menjadi Arry
        $data = json_decode($response, true);

        // Jika Response Tidak Valid
        if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
            echo '
                <div class="alert alert-danger">
                    <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
                </div>
            ';
            exit;
        }

        // Buka Metadata
        $metadata = $data['metadata'];

        // Buat Variabel Penting
        $id_encounter = $metadata['id_encounter'];
        $tujuan       = $metadata['tujuan'];
        $id_dokter    = $metadata['id_dokter'];

        //Routing asal kiriman
        if($tujuan=="Rajal"){
            $asal_kiriman = $metadata['poliklinik'];
        }else{
            $asal_kiriman = $metadata['ruangan'];
        }

        $id_pasien     = $metadata['pasien']['id_pasien'];
        $ihs_pasien    = $metadata['pasien']['id_ihs'];
        $id_encounter  = $metadata['id_encounter'];
        $nama          = $metadata['pasien']['nama'];
        $tanggal_lahir = $metadata['pasien']['tanggal_lahir'];
        $gender        = $metadata['pasien']['gender'];
        $tujuan        = $metadata['tujuan'];
        $pembayaran    = $metadata['pembayaran'];
        $DiagAwal      = $metadata['DiagAwal'];
    }else{
        $id_kunjungan  = "";
        $id_encounter  = "";
        $id_pasien     = "";
        $ihs_pasien    = "";
        $nama          = "";
        $tanggal_lahir = "";
        $gender        = "";
        $tujuan        = "";
        $pembayaran    = "";
        $id_dokter     = "";
        $asal_kiriman  = "";
        $DiagAwal  = "";
    }
    
    // Routing Tujuan
    if(empty($tujuan)){
        $label_tujuan_1 = "selected";
        $label_tujuan_2 = "";
        $label_tujuan_3 = "";
    }else{
        if($tujuan=="Rajal"){
            $label_tujuan_1 = "";
            $label_tujuan_2 = "selected";
            $label_tujuan_3 = "";
        }else{
            if($tujuan=="Ranap"){
                $label_tujuan_1 = "";
                $label_tujuan_2 = "";
                $label_tujuan_3 = "selected";
            }else{
                $label_tujuan_1 = "selected";
                $label_tujuan_2 = "";
                $label_tujuan_3 = "";
            }
        }
    }

    // Routing Gender
    if(empty($gender)){
        $label_gender_1 = "selected";
        $label_gender_2 = "";
        $label_gender_3 = "";
    }else{
        if($gender=="Laki-laki"){
            $label_gender_1 = "";
            $label_gender_2 = "selected";
            $label_gender_3 = "";
        }else{
            if($gender=="Perempuan"){
                $label_gender_1 = "";
                $label_gender_2 = "";
                $label_gender_3 = "selected";
            }else{
                $label_gender_1 = "selected";
                $label_gender_2 = "";
                $label_gender_3 = "";
            }
        }
    }

    // Extract DiagAwal
    $DiagAwal       = isset($DiagAwal) ? trim($DiagAwal) : '';
    $kode_diag      = '';
    $deskripsi_diag = '';

    if (!empty($DiagAwal)) {
        $parts = explode('-', $DiagAwal, 2); 
        $kode_diag      = isset($parts[0]) ? trim($parts[0]) : '';
        $deskripsi_diag = isset($parts[1]) ? trim($parts[1]) : '';
    }

    //Tampilkan Form
    echo '
        <div class="row mb-3">
            <div class="col-12">
                <b><small>A. Informasi Pasien</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_pasien"><small>No.RM</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="id_pasien" id="id_pasien" class="form-control" value="'.$id_pasien.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="ihs_pasien"><small>ID IHS Pasien</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="ihs_pasien" id="ihs_pasien" class="form-control" value="'.$ihs_pasien.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_pasien"><small>Nama Pasien</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" value="'.$nama.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tanggal_lahir"><small>Tanggal Lahir</small></label>
            </div>
            <div class="col-md-8">
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="'.$tanggal_lahir.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="gender"><small>Gender</small></label>
            </div>
            <div class="col-md-8">
                <select name="gender" id="gender" class="form-control">
                    <option '.$label_gender_1.' value="">Pilih</option>
                    <option '.$label_gender_2.' value="Laki-laki">Laki-laki</option>
                    <option '.$label_gender_3.' value="Perempuan">Perempuan</option>
                </select>
            </div>
        </div>
    ';
    
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12">
                <b><small>B. Informasi Kunjungan</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_kunjungan"><small>ID.Kunjungan</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="id_kunjungan" id="id_kunjungan" class="form-control" value="'.$id_kunjungan.'">
            </div>
        </div>
         <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_encounter"><small>ID. Encounter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="id_encounter" id="id_encounter" class="form-control" value="'.$id_encounter.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tujuan"><small>Tujuan Kunjungan</small></label>
            </div>
            <div class="col-md-8">
                <select name="tujuan" id="tujuan" class="form-control">
                    <option '.$label_tujuan_1.' value="">Pilih</option>
                    <option '.$label_tujuan_2.' value="Rajal">Rajal</option>
                    <option '.$label_tujuan_3.' value="Ranap">Ranap</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="pembayaran"><small>Pembayaran</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="pembayaran" id="pembayaran" class="form-control" value="'.$pembayaran.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fakses"><small>Faskes Pengirim</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="fakses" id="fakses" class="form-control" value="'.$company_name.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="unit"><small>Unit / Instalasi</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="unit" id="unit" class="form-control" value="'.$asal_kiriman.'">
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>C. Informasi Permintaan</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <label for="tanggal_diminta"><small>Tanggal/Jam Permintaan</small></label>
            </div>
            <div class="col-md-4 mb-2">
                <input type="date" name="tanggal_diminta" id="tanggal_diminta" class="form-control" value="'.date('Y-m-d').'">
            </div>
            <div class="col-md-4 mb-2">
                <input type="time" name="jam_diminta" id="jam_diminta" class="form-control" value="'.date('H:i').'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="priority"><small>Prioritisasi</small></label>
            </div>
            <div class="col-md-8">
                <select name="priority" id="priority" class="form-control">
                    <option value="routine">Biasa</option>
                    <option value="urgent">Segera</option>
                    <option value="stat">Gawat</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="puasa"><small>Status Puasa</small></label>
            </div>
            <div class="col-md-8">
                <select name="puasa" id="puasa" class="form-control">
                    <option value="0">Tidak Puasa</option>
                    <option value="1">Puasa</option>
                </select>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>D. Dokter Pengirim</small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_dokter_pengirim"><small>Nama  Dokter Pengirim</small></label>
            </div>
            <div class="col-md-8">
                <select name="nama_dokter_pengirim" id="nama_dokter_pengirim" class="form-control">
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="kode_dokter_pengirim"><small>Kode Dokter Pengirim</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="kode_dokter_pengirim" id="kode_dokter_pengirim" class="form-control" value="">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="ihs_dokter_pengirim"><small>IHS Dokter Pengirim</small></label>
            </div> 
            <div class="col-md-8">
                <input type="text" name="ihs_dokter_pengirim" id="ihs_dokter_pengirim" class="form-control" value="">
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <b><small>E. Diagnosis <i>(Reson Code)</i></small></b>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_diagnosis"><small><i>Diagnosis</i></small></label>
            </div>
            <div class="col-md-8">
                <select name="id_diagnosis" id="id_diagnosis" class="form-control">
                    <option value=""></option>
                    <option selected value="'.$kode_diag.'">'.$kode_diag.' - '.$deskripsi_diag.'</option>
                </select>
                <input type="hidden" name="diagnosis_display" id="diagnosis_display" value="'.$deskripsi_diag.'">
                <input type="hidden" name="diagnosis_code" id="diagnosis_code" value="'.$kode_diag.'">
                <input type="hidden" name="diagnosis_system" id="diagnosis_system" value="http://hl7.org/fhir/sid/icd-10">
                <small>
                    <small>
                        Informasi diagnosis yang mendasari perlunya pemeriksaan laboratorium ini. 
                        Menggunakan standar ICD 10 dan secara umum menggunakan referensi dari system : http://hl7.org/fhir/sid/icd-10
                    </small>
                </small>
            </div>
        </div>
    ';
?>

<div class="row mb-3 mt-3">
    <div class="col-12">
        <b><small>E. Pilih Pemeriksaan</small></b>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <td class="text-center"><b><small>No</small></b></td>
                        <td class="text-left" colspan="2"><b><small>Nama Pemeriksaan</small></b></td>
                        <td class="text-left"><b><small>Display</small></b></td>
                        <td class="text-left"><b><small>Code</small></b></td>
                        <td class="text-left"><b><small>Unit</small></b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query = mysqli_query($Conn, "
                            SELECT 
                                id_referensi_pemeriksaan,
                                nama_pemeriksaan,
                                display_pemeriksaan,
                                code_pemeriksaan,
                                unit,
                                category_pemeriksaan
                            FROM referensi_pemeriksaan
                            ORDER BY category_pemeriksaan ASC, nama_pemeriksaan ASC
                        ");

                        if(!$query || mysqli_num_rows($query) === 0){
                            echo '
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                                    </td>
                                </tr>
                            ';
                        }else{
                            $no = 1;
                            $last_category = null;

                            while ($data = mysqli_fetch_assoc($query)) {
                                $category_pemeriksaan      = $data['category_pemeriksaan'] ?: '-';
                                $id_referensi_pemeriksaan  = $data['id_referensi_pemeriksaan'];
                                $nama_pemeriksaan          = $data['nama_pemeriksaan'];
                                $display_pemeriksaan       = $data['display_pemeriksaan'];
                                $code_pemeriksaan          = $data['code_pemeriksaan'];
                                $unit                      = $data['unit'];

                                // Render header hanya jika kategori berubah
                                if ($last_category !== $category_pemeriksaan) {
                                    echo '
                                        <tr>
                                            <td class="text-center">
                                                <input 
                                                    type="checkbox" 
                                                    class="cehck_all_sub"
                                                    id="cehck_all_sub'.$no.'"
                                                    data-category="'.htmlspecialchars($category_pemeriksaan, ENT_QUOTES).'"
                                                >
                                            </td>
                                            <td class="text-left" colspan="5">
                                                <label for="cehck_all_sub'.$no.'">
                                                    <small>
                                                        <b>'.htmlspecialchars($category_pemeriksaan).'</b>
                                                    </small>
                                                </label>
                                            </td>
                                        </tr>
                                    ';
                                    $last_category = $category_pemeriksaan;
                                    $no++;
                                }

                                echo '
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">
                                            <input 
                                                type="checkbox"
                                                name="id_referensi_pemeriksaan[]"
                                                class="checkbox_pemeriksaan"
                                                data-category="'.htmlspecialchars($category_pemeriksaan, ENT_QUOTES).'"
                                                id="id_referensi_pemeriksaan'.$id_referensi_pemeriksaan.'"
                                                value="'.(int)$id_referensi_pemeriksaan.'"
                                            >
                                        </td>
                                        <td class="text-left">
                                            <label for="id_referensi_pemeriksaan'.$id_referensi_pemeriksaan.'">
                                                <small>'.htmlspecialchars($nama_pemeriksaan).'</small>
                                            </label>
                                        </td>
                                        <td class="text-left">
                                            <small><i>'.htmlspecialchars($display_pemeriksaan).'</i></small>
                                        </td>
                                        <td class="text-left">
                                            <small>'.htmlspecialchars($code_pemeriksaan).'</small>
                                        </td>
                                        <td class="text-left">
                                            <small>'.htmlspecialchars($unit).'</small>
                                        </td>
                                    </tr>
                                ';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <b><small>G. Keterangan</i></small></b>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="keterangan"><small><i>Catatan / Keterangan Lain</i></small></label>
        <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
    </div>
</div>