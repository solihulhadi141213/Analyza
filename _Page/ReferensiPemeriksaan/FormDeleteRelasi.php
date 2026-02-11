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
                <small>ID rELASI Tidak Boleh Kosong!</small>
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

    // Definisikan masing-masing ID
    $nama_metode_pemeriksaan = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'nama_metode_pemeriksaan');
    $nama_spesimen           = GetDetailData($Conn, 'referensi_jenis_spesimen', 'id_referensi_jenis_spesimen', $id_referensi_jenis_spesimen, 'nama_spesimen');
    $nama_metode_sample      = GetDetailData($Conn, 'referensi_metode_sample', 'id_referensi_metode_sample', $id_referensi_metode_sample, 'nama_metode_sample');
    $nama_container          = GetDetailData($Conn, 'referensi_container', 'id_referensi_container', $id_referensi_container, 'nama_container');
       
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_pemeriksaan_relasi" value="'.$id_referensi_pemeriksaan_relasi.'">
        <div class="row mb-2">
            <div class="col-4"><small>Metode Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nama_metode_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Specimen</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nama_spesimen.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Metode Specimen</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nama_metode_sample.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kontainer</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nama_container.'</small>
            </div>
        </div>
    ';    

    echo '
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-danger text-center">
                    <small>Apakah anda yakin ingin menghapus data ini?</small>
                </div>
            </div>
        </div>
    ';
?>