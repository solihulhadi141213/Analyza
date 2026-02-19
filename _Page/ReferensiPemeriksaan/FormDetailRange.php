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

    //id_referensi_range wajib terisi
    if(empty($_POST['id_referensi_range'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Nilai Rujukan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_referensi_range' dan sanitasi
    $id_referensi_range = validateAndSanitizeInput($_POST['id_referensi_range']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM referensi_range WHERE id_referensi_range = ?");
    $Qry->bind_param("i", $id_referensi_range);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        // Buat Variabel
        $id_referensi_range       = $Data['id_referensi_range'];
        $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'];
        $umur_kategori            = $Data['umur_kategori'] ?? '-';
        $umur_min                 = $Data['umur_min'];
        $umur_max                 = $Data['umur_max'];
        $umur_unit                = $Data['umur_unit'];
        $jenis_kelamin            = $Data['jenis_kelamin'];
        $nilai_min                = $Data['nilai_min'];
        $nilai_max                = $Data['nilai_max'];
        $operator                 = $Data['operator'];
        $label                    = $Data['label'];
        $fhir_display             = $Data['fhir_display'];
        $fhir_code                = $Data['fhir_code'];
        $fhir_system              = $Data['fhir_system'];
        $conclusion               = $Data['conclusion'] ?? '-';

        // Buka $allow_age dan $allow_sex
        $unit_display = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'unit_display');
        $allow_age = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_age');
        $allow_sex = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_sex');
        
        // Menentukan Penyataan nilai rujukan
        if($operator=="More"){
            $nilai_rujukan = "n ≥ $nilai_min";
        }
        if($operator=="Between"){
            $nilai_rujukan = "$nilai_min - $nilai_max";
        }
        if($allow_age==1){
            if(empty($umur_min)){
                $show_age = "$umur_kategori (> $umur_max $umur_unit)";
            }else{
                if(empty($umur_max)){
                    $show_age = "$umur_kategori (< $umur_min $umur_unit)";
                }else{
                        $show_age = "$umur_kategori ($umur_min - $umur_max $umur_unit)";
                }
            }
            
        }else{
            $show_age = 'All';
        }

        if($allow_sex==1){
            if($jenis_kelamin==""){
                $show_sex = "All";
            }else{
                if($jenis_kelamin=="Laki-laki"){
                    $show_sex = "L";
                }else{
                    if($jenis_kelamin=="Perempuan"){
                        $show_sex = "P";
                    }else{
                        $show_sex = "All";
                    }
                }
            }
        }else{
            $show_sex = "All";
        }
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_range" value="'.$id_referensi_range.'">
            <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
            <div class="row mb-2">
                <div class="col-12">
                    <small><b># Klasifikasi Nilai</b></small>
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
            <div class="row mb-2">
                <div class="col-4"><small>Kesimpulan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$conclusion.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Operator Nilai</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nilai_rujukan.' '.$unit_display.'</small>
                </div>
            </div>
            <div class="row mb-2 mt-3">
                <div class="col-12 mt-3">
                    <small><b># Nilai Dan Operator</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nilai Min</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nilai_min.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Nilai Max</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nilai_max.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Operator</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$operator.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Unit / Satuan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$unit_display.'</small>
                </div>
            </div>
        ';
    }
    if($allow_age==1){
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

    if($allow_sex==1){
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
            <div class="row mb-2">
                <div class="col-4"><small>Kode</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$show_sex.'</small>
                </div>
            </div>
        ';
        
    }
?>