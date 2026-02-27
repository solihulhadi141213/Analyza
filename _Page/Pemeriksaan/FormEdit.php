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
    $id_pasien            = $Data['id_pasien'];
    $id_kunjungan         = $Data['id_kunjungan'];
    $ihs_pasien           = $Data['ihs_pasien'];
    $id_encounter         = $Data['id_encounter'];
    $nama                 = $Data['nama'];
    $gender               = $Data['gender'];
    $tanggal_lahir        = $Data['tanggal_lahir'];
    $tujuan               = $Data['tujuan'];
    $pembayaran           = $Data['pembayaran'];
    $fakses               = $Data['fakses'];
    $unit                 = $Data['unit'];
    $priority             = $Data['priority'];
    $puasa                = $Data['puasa'];
    $status               = $Data['status'];
    $kode_dokter_pengirim = $Data['kode_dokter_pengirim'];
    $ihs_dokter_pengirim  = $Data['ihs_dokter_pengirim'];
    $nama_dokter_pengirim = $Data['nama_dokter_pengirim'];
    $nama_dokter_penerima = $Data['nama_dokter_penerima'];
    $kode_dokter_penerima = $Data['kode_dokter_penerima'];
    $ihs_dokter_penerima  = $Data['ihs_dokter_penerima'];
    $datetime_diminta     = $Data['datetime_diminta'];
    $datetime_diterima    = $Data['datetime_diterima'];
    $datetime_spesimen    = $Data['datetime_spesimen'];
    $datetime_hasil       = $Data['datetime_hasil'];
    $diagnosis            = $Data['diagnosis'];
    $keterangan           = $Data['keterangan'];

    // Select Gender
    $label_gender_1 = "";
    $label_gender_2 = "";
    $label_gender_3 = "";
    if($gender==""){
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
                $label_gender_1 = "";
                $label_gender_2 = "";
                $label_gender_3 = "";
            }
        }
    }

    // Select $priority
    if($priority=="routine"){
        $select_prioritas_1 = "selected";
        $select_prioritas_2 = "";
        $select_prioritas_3 = "";
    }else{
        if($priority=="urgent"){
            $select_prioritas_1 = "";
            $select_prioritas_2 = "selected";
            $select_prioritas_3 = "";
        }else{
            if($priority=="stat"){
                $select_prioritas_1 = "";
                $select_prioritas_2 = "";
                $select_prioritas_3 = "selected";
            }else{
                $select_prioritas_1 = "";
                $select_prioritas_2 = "";
                $select_prioritas_3 = "";
            }
        }
    }

    // Select $priority
    if($tujuan==""){
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


    // Form Hidden
    echo '<input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">';

    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_pasien_edit"><small>No.RM</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="id_pasien" id="id_pasien_edit" class="form-control" value="'.$id_pasien.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="ihs_pasien_edit"><small>ID IHS Pasien</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="ihs_pasien" id="ihs_pasien_edit" class="form-control" value="'.$ihs_pasien.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_pasien_edit"><small>Nama Pasien</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="nama_pasien" id="nama_pasien_edit" class="form-control" value="'.$nama.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tanggal_lahir_edit"><small>Tanggal Lahir</small></label>
            </div>
            <div class="col-md-8">
                <input type="date" name="tanggal_lahir" id="tanggal_lahir_edit" class="form-control" value="'.$tanggal_lahir.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="gender_edit"><small>Gender</small></label>
            </div>
            <div class="col-md-8">
                <select name="gender" id="gender_edit" class="form-control">
                    <option '.$label_gender_1.' value="">Pilih</option>
                    <option '.$label_gender_2.' value="Laki-laki">Laki-laki</option>
                    <option '.$label_gender_3.' value="Perempuan">Perempuan</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fakses_edit"><small>Faskes Pengirim</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="fakses" id="fakses_edit" class="form-control" value="'.$fakses.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="unit_edit"><small>Unit / Instalasi</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="unit" id="unit_edit" class="form-control" value="'.$unit.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_kunjungan_edit"><small>ID.Kunjungan</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="id_kunjungan" id="id_kunjungan_edit" class="form-control" value="'.$id_kunjungan.'">
            </div>
        </div>
         <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_encounter_edit"><small>ID. Encounter</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="id_encounter" id="id_encounter_edit" class="form-control" value="'.$id_encounter.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tujuan_edit"><small>Tujuan Kunjungan</small></label>
            </div>
            <div class="col-md-8">
                <select name="tujuan" id="tujuan_edit" class="form-control">
                    <option '.$label_tujuan_1.' value="">Pilih</option>
                    <option '.$label_tujuan_2.' value="Rajal">Rajal</option>
                    <option '.$label_tujuan_3.' value="Ranap">Ranap</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="pembayaran_edit"><small>Pembayaran</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" name="pembayaran" id="pembayaran_edit" class="form-control" value="'.$pembayaran.'">
            </div>
        </div>
    ';

    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="priority_edit"><small>Prioritisasi</small></label>
            </div>
            <div class="col-md-8">
                <select name="priority" id="priority_edit" class="form-control">
                    <option '.$select_prioritas_1.' value="routine">Biasa</option>
                    <option '.$select_prioritas_2.' value="urgent">Segera</option>
                    <option '.$select_prioritas_3.' value="stat">Gawat</option>
                </select>
            </div>
        </div>
    ';
?>
