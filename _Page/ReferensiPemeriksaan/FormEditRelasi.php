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

    //id_referensi_pemeriksaan_relasi wajib terisi
    if(empty($_POST['id_referensi_pemeriksaan_relasi'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_pemeriksaan_relasi' dan sanitasi
    $id_referensi_pemeriksaan_relasi = validateAndSanitizeInput($_POST['id_referensi_pemeriksaan_relasi']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan_relasi = ?");
    $Qry->bind_param("i", $id_referensi_pemeriksaan_relasi);
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

    // Buat Variabel
    $id_referensi_metode_pemeriksaan = $Data['id_referensi_metode_pemeriksaan'];
    $id_referensi_jenis_spesimen     = $Data['id_referensi_jenis_spesimen'];
    $id_referensi_metode_sample      = $Data['id_referensi_metode_sample'];
    $id_referensi_container          = $Data['id_referensi_container'];

    // Menampilkan Metode Pemeriksaan
    $QryMetodePemeriksaan = $Conn->prepare("SELECT * FROM referensi_metode_pemeriksaan WHERE id_referensi_metode_pemeriksaan = ?");
    $QryMetodePemeriksaan->bind_param("i", $id_referensi_metode_pemeriksaan);
    if (!$QryMetodePemeriksaan->execute()) {
        $error_metode_pemeriksaan=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka tabel referensi_metode_pemeriksaan !<br>Keterangan : '.$error_metode_pemeriksaan.'</small>
            </div>
        ';
        exit;
    }
    $ResultMetodePemeriksaan = $QryMetodePemeriksaan->get_result();
    $DataMetodePemeriksaan = $ResultMetodePemeriksaan->fetch_assoc();
    $QryMetodePemeriksaan->close();

    // Menampilkan Jenis Spesimen
    $QryJenisSpesimen = $Conn->prepare("SELECT * FROM referensi_jenis_spesimen WHERE id_referensi_jenis_spesimen = ?");
    $QryJenisSpesimen->bind_param("i", $id_referensi_jenis_spesimen);
    if (!$QryJenisSpesimen->execute()) {
        $error_jenis_spesimen=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka tabel referensi_metode_pemeriksaan !<br>Keterangan : '.$error_jenis_spesimen.'</small>
            </div>
        ';
        exit;
    }
    $ResultJenisSpesimen = $QryJenisSpesimen->get_result();
    $DataJenisSpesimen = $ResultJenisSpesimen->fetch_assoc();
    $QryJenisSpesimen->close();

    // Menampilkan Metode Spesimen
    $QryMetodeSpesimen = $Conn->prepare("SELECT * FROM referensi_metode_sample WHERE id_referensi_metode_sample = ?");
    $QryMetodeSpesimen->bind_param("i", $id_referensi_metode_sample);
    if (!$QryMetodeSpesimen->execute()) {
        $error_metode_spesimen=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka tabel referensi_metode_pemeriksaan !<br>Keterangan : '.$error_metode_spesimen.'</small>
            </div>
        ';
        exit;
    }
    $ResultMetodeSpesimen = $QryMetodeSpesimen->get_result();
    $DataMetodeSpesimen = $ResultMetodeSpesimen->fetch_assoc();
    $QryMetodeSpesimen->close();

    // Menampilkan Kontainer
    $QryKontainer = $Conn->prepare("SELECT * FROM referensi_container WHERE id_referensi_container = ?");
    $QryKontainer->bind_param("i", $id_referensi_container);
    if (!$QryKontainer->execute()) {
        $ErrorKontainer=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka tabel referensi_container !<br>Keterangan : '.$ErrorKontainer.'</small>
            </div>
        ';
        exit;
    }
    $ResultKontainer = $QryKontainer->get_result();
    $DataKontainer = $ResultKontainer->fetch_assoc();
    $QryKontainer->close();
    
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_pemeriksaan_relasi" value="'.$id_referensi_pemeriksaan_relasi.'">
    ';
?>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="id_referensi_metode_pemeriksaan_edit"><small>Metode Pemeriksaan</small></label>
        </div>
        <div class="col-md-8">
            <select name="id_referensi_metode_pemeriksaan" id="id_referensi_metode_pemeriksaan_edit" class="form-control">
                <option value="">Pilih Metode Pemeriksaan</option>
                <?php
                    if(!empty($DataMetodePemeriksaan['id_referensi_metode_pemeriksaan'])){
                        echo '
                            <option selected value="'.$DataMetodePemeriksaan['id_referensi_metode_pemeriksaan'].'">
                                '.$DataMetodePemeriksaan['nama_metode_pemeriksaan'].'
                            </option>
                        ';
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="id_referensi_jenis_spesimen_edit"><small>Jenis Spesimen</small></label>
        </div>
        <div class="col-md-8">
            <select name="id_referensi_jenis_spesimen" id="id_referensi_jenis_spesimen_edit" class="form-control">
                <option value="">Pilih Metode Pemeriksaan</option>
                <?php
                    if(!empty($DataJenisSpesimen['id_referensi_jenis_spesimen'])){
                        echo '
                            <option selected value="'.$DataJenisSpesimen['id_referensi_jenis_spesimen'].'">
                                '.$DataJenisSpesimen['nama_spesimen'].'
                            </option>
                        ';
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="id_referensi_metode_sample_edit"><small>Metode Spesimen</small></label>
        </div>
        <div class="col-md-8">
            <select name="id_referensi_metode_sample" id="id_referensi_metode_sample_edit" class="form-control">
                <option value="">Pilih Metode Spesimen</option>
                <?php
                    if(!empty($DataMetodeSpesimen['id_referensi_metode_sample'])){
                        echo '
                            <option selected value="'.$DataMetodeSpesimen['id_referensi_metode_sample'].'">
                                '.$DataMetodeSpesimen['nama_metode_sample'].'
                            </option>
                        ';
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="id_referensi_container_edit"><small>Kontainer</small></label>
        </div>
        <div class="col-md-8">
            <select name="id_referensi_container" id="id_referensi_container_edit" class="form-control">
                <option value="">Pilih Kontainer</option>
                <?php
                    if(!empty($DataKontainer['id_referensi_container'])){
                        echo '
                            <option selected value="'.$DataKontainer['id_referensi_container'].'">
                                '.$DataKontainer['nama_container'].'
                            </option>
                        ';
                    }
                ?>
            </select>
        </div>
    </div>