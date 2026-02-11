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

    //id_referensi_category wajib terisi
    if(empty($_POST['id_referensi_category'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Category Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_category' dan sanitasi
    $id_referensi_category = validateAndSanitizeInput($_POST['id_referensi_category']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_category WHERE id_referensi_category = ?");
    $Qry->bind_param("i", $id_referensi_category);
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

    if(empty($Data)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Data Category Tidak Ditemukan.</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel Inti
    $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'] ?? "";
    $nilai_hasil              = $Data['nilai_hasil'] ?? "-";
    $label                    = $Data['label'] ?? "-";
    $fhir_display             = $Data['fhir_display'] ?? "-";
    $fhir_code                = $Data['fhir_code'] ?? "-";
    $fhir_system              = $Data['fhir_system'] ?? "-";

    // Variabel opsional (bergantung struktur tabel)
    $umur_kategori = $Data['umur_kategori'] ?? "-";
    $umur_min      = $Data['umur_min'] ?? "";
    $umur_max      = $Data['umur_max'] ?? "";
    $umur_unit     = $Data['umur_unit'] ?? "";
    $jenis_kelamin = $Data['jenis_kelamin'] ?? "All";
    $conclusion    = $Data['conclusion'] ?? "-";

    // Ambil konfigurasi pemeriksaan
    $allow_age = 0;
    $allow_sex = 0;
    if(!empty($id_referensi_pemeriksaan)){
        $allow_age = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_age');
        $allow_sex = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_sex');
    }

    // Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_category" value="'.$id_referensi_category.'">
        <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
        <div class="row mb-2">
            <div class="col-12">
                <small><b># Klasifikasi Category</b></small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Label Interpertasi</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$label.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nilai Hasil</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nilai_hasil.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Display</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$fhir_display.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$fhir_code.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>System</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$fhir_system.'</small>
            </div>
        </div>
    ';

    if($allow_age==1 && array_key_exists('umur_unit', $Data)){
        echo '
            <div class="row mb-2 mt-3">
                <div class="col-12 mt-3">
                    <small><b># Klasifikasi Usia</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kategori usia</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$umur_kategori.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Usia Min</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$umur_min.' '.$umur_unit.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Usia Max</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$umur_max.' '.$umur_unit.'</small>
                </div>
            </div>
        ';
    }

    if($allow_sex==1 && array_key_exists('jenis_kelamin', $Data)){
        echo '
            <div class="row mb-2 mt-3">
                <div class="col-12 mt-3">
                    <small><b># Klasifikasi Gender</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jenis Kelamin</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$jenis_kelamin.'</small>
                </div>
            </div>
        ';
    }
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
