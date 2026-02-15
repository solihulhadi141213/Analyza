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

    //id_referensi_pemeriksaan wajib terisi
    if(empty($_POST['id_referensi_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_pemeriksaan' dan sanitasi
    $id_referensi_pemeriksaan = validateAndSanitizeInput($_POST['id_referensi_pemeriksaan']);
    
    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
    $Qry->bind_param("i", $id_referensi_pemeriksaan);
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

    echo '<input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">';
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_kategori_kelas">
                    <small>Klasifikasi Usia</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="umur_kategori" id="umur_kategori_kelas" class="form-control" required>
                <small class="text text-grayish">
                    <small>Klasifikasi usia berdasarkan jarak usia Min - Max (Contoh : Balita, Neonatus, Anak-anak, Remaja Dll.)</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_min_kelas">
                    <small>Usia Min</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="number" min="0" step="1" name="umur_min" id="umur_min_kelas" class="form-control" placeholder="0">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_max_kelas">
                    <small>Usia Max</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="number" min="0" step="1" name="umur_max" id="umur_max_kelas" class="form-control" placeholder="0">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_unit_kelas">
                    <small>Unit / Satuan Usia</small>
                </label>
            </div>
            <div class="col-md-8">
                <select class="form-control" name="umur_unit" id="umur_unit_kelas" required>
                    <option value="Tahun">Tahun</option>
                    <option value="Bulan">Bulan</option>
                    <option value="Hari">Hari</option>
                </select>
            </div>
        </div>
        
    ';
?>