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
    if(empty($_POST['id_kunjungan'])){
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

    // Buat Variabel id_kunjungan dan sanitasi
    $id_kunjungan       = validateAndSanitizeInput($_POST['id_kunjungan']);
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
    $id_encounter = $Data['id_encounter'];
    $tujuan       = $Data['tujuan'];
    $pembayaran   = $Data['pembayaran'];

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
    $id_encounter_org = $metadata['id_encounter'];
    $tanggal          = $metadata['tanggal'];
    $tujuan_org       = $metadata['tujuan'];
    $pembayaran_org   = $metadata['pembayaran'];
    $tanggal          = $metadata['tanggal'];
   

      // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';
    echo '<input type="hidden" name="id_kunjungan" value="'.$id_kunjungan.'">';

      // Inisialisasi Variabel Pembaharuan Data
    $perlu_pembaharuan = 0;
    $update_encounter  = "";
    $update_tujuan     = "";
    $update_pembayaran = "";
    if($id_encounter_org!==$id_encounter){
        $update_encounter  = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
        $perlu_pembaharuan = $perlu_pembaharuan + 1;
    }
    if($tujuan_org!==$tujuan){
        $update_tujuan     = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
        $perlu_pembaharuan = $perlu_pembaharuan + 1;
    }
    if($pembayaran_org!==$pembayaran){
        $update_pembayaran = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i></span>';
        $perlu_pembaharuan = $perlu_pembaharuan + 1;
    }
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>ID Kunjungan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$id_kunjungan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Tanggal Kunjungan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$tanggal.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>ID Encounter</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$id_encounter_org.' '.$update_encounter.'</small>
                <input type="hidden" name="id_encounter" value="'.$id_encounter_org.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Tujuan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$tujuan_org.' '.$update_tujuan.'</small>
                <input type="hidden" name="tujuan" value="'.$tujuan_org.'">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Pembayaran</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$pembayaran_org.' '.$update_pembayaran.'</small>
                <input type="hidden" name="pembayaran" value="'.$pembayaran_org.'">
            </div>
        </div>
    ';

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