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

    //id_referensi_usia wajib terisi
    if(empty($_POST['id_referensi_usia'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_usia' dan sanitasi
    $id_referensi_usia = validateAndSanitizeInput($_POST['id_referensi_usia']);
    
    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_usia WHERE id_referensi_usia = ?");
    $Qry->bind_param("i", $id_referensi_usia);
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

    // Buat Variabel Data
    $umur_kategori = $Data['umur_kategori'];
    $umur_min      = $Data['umur_min'];
    $umur_max      = $Data['umur_max'];
    $umur_unit     = $Data['umur_unit'];
    if($umur_unit=="Tahun"){
        $opsi_umur1 = "selected";
        $opsi_umur2 = "";
        $opsi_umur3 = "";
    }else{
        if($umur_unit=="Bulan"){
            $opsi_umur1 = "";
            $opsi_umur2 = "";
            $opsi_umur3 = "selected";
        }else{
            if($umur_unit=="Hari"){
                $opsi_umur1 = "";
                $opsi_umur2 = "";
                $opsi_umur3 = "selected";
            }else{
                
            }
        }
    }

    echo '<input type="hidden" name="id_referensi_usia" value="'.$id_referensi_usia.'">';
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_kategori_kelas_edit">
                    <small>Klasifikasi Usia</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="umur_kategori" id="umur_kategori_kelas_edit" class="form-control" value="'.$umur_kategori.'" required>
                <small class="text text-grayish">
                    <small>Klasifikasi usia berdasarkan jarak usia Min - Max (Contoh : Balita, Neonatus, Anak-anak, Remaja Dll.)</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_min_kelas_edit">
                    <small>Usia Min</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="number" min="0" step="1" name="umur_min" id="umur_min_kelas_edit" class="form-control" value="'.$umur_min.'" placeholder="0">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_max_kelas_edit">
                    <small>Usia Max</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="number" min="0" step="1" name="umur_max" id="umur_max_kelas_edit" class="form-control" value="'.$umur_max.'" placeholder="0">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="umur_unit_kelas_edit">
                    <small>Unit / Satuan Usia</small>
                </label>
            </div>
            <div class="col-md-8">
                <select class="form-control" name="umur_unit" id="umur_unit_kelas_edit" required>
                    <option '.$opsi_umur1.' value="Tahun">Tahun</option>
                    <option '.$opsi_umur2.' value="Bulan">Bulan</option>
                    <option '.$opsi_umur3.' value="Hari">Hari</option>
                </select>
            </div>
        </div>
        
    ';
?>