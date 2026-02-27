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
    if(empty($_POST['id_pasien'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Pasien Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel id_pasien dan sanitasi
    $id_pasien       = validateAndSanitizeInput($_POST['id_pasien']);
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    // Buka Detail Laboratorium
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
    $id_pasien            = $Data['id_pasien'];
    $id_kunjungan         = $Data['id_kunjungan'];
    $ihs_pasien           = $Data['ihs_pasien'];
    $id_encounter         = $Data['id_encounter'];
    $nama                 = $Data['nama'];
    $gender               = $Data['gender'];
    $tanggal_lahir        = $Data['tanggal_lahir'];

    // payload
    $payload = [
        "page"       => "1",
        "limit"      => "1",
        "short_by"   => "DESC",
        "order_by"   => "id_pasien",
        "keyword_by" => "id_pasien",
        "keyword"    => $id_pasien
    ];

    // Payload To PayloadJson
    $PayloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

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
        CURLOPT_URL => ''.$url_connection_simrs.'/API/SIMRS/kunjungan.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $PayloadJson,
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
    $jumlah_total_data = $metadata['jumlah_total_data'];
    $jumlah_halaman    = $metadata['jumlah_halaman'];
    $curent_page       = $metadata['curent_page'];
    $list_kunjungan    = $metadata['list_kunjungan'];

    // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';
    echo '<input type="hidden" name="id_pasien" value="'.$id_pasien.'">';

    $no                = 1;
    $perlu_pembaharuan = 0;
    foreach ($list_kunjungan as $list){
        $nama_pasien_org   = $list['nama'] ?? '-';
        $nik_org           = $list['nik'] ?? '-';
        $no_bpjs_org       = $list['no_bpjs'] ?? '-';
        $id_ihs_org        = $list['id_ihs'] ?? '-';
        $gender_org        = $list['gender'] ?? '-';
        $tempat_lahir_org  = $list['tempat_lahir'] ?? '-';
        $tanggal_lahir_org = $list['tanggal_lahir'] ?? '-';

        // Routing Informasi Yang Perlu Diperbaharui
        $update_nama          = "";
        $update_gender        = "";
        $update_ihs           = "";
        $update_tanggal_lahir = "";
        if($list['nama']!==$nama){
            $update_nama = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
            $perlu_pembaharuan = $perlu_pembaharuan + 1;
        }
        if($list['gender']!==$gender){
            $update_gender = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
            $perlu_pembaharuan = $perlu_pembaharuan + 1;
        }
        if($list['id_ihs']!==$ihs_pasien){
            $update_ihs = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
            $perlu_pembaharuan = $perlu_pembaharuan + 1;
        }
        if($list['tanggal_lahir']!==$tanggal_lahir){
            $update_tanggal_lahir = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
            $perlu_pembaharuan = $perlu_pembaharuan + 1;
        }
        echo '
             <div class="row mb-3">
                <div class="col-12">
                     <div class="row mb-2">
                        <div class="col-4"><small>No.RM</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$id_pasien.'</small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>Nama</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$nama_pasien_org.' '.$update_nama.'</small>
                            <input type="hidden" name="nama" value="'.$nama_pasien_org.'">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>NIK</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$nik_org.'</small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>No.BPJS</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$no_bpjs_org.'</small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>No.IHS Pasien</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$id_ihs_org.' '.$update_ihs.'</small>
                            <input type="hidden" name="ihs_pasien" value="'.$id_ihs_org.'">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>Gender</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$gender_org.' '.$update_gender.'</small>
                            <input type="hidden" name="gender" value="'.$gender_org.'">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>Tempat Lahir</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$tempat_lahir_org.'</small>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><small>Tanggal Lahir</small></div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <small class="text text-grayish">'.$tanggal_lahir_org.' '.$update_tanggal_lahir.'</small>
                            <input type="hidden" name="tanggal_lahir" value="'.$tanggal_lahir_org.'">
                        </div>
                    </div>
                </div>
            </div>
        ';
        $no++;
    }

    if(!empty($perlu_pembaharuan)){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <small>
                            Beberapa informasi yang tersedia memerlukan pembaharuan.
                            Silahkan Pilih Tombol <b>Update</b> Untuk Memperbaharui Informasi pasien
                        </small>
                    </div>
                </div>
            </div>
        ';
    }
?>