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
    }else{
        $allow_age = "";
        $allow_sex = "";
    }

    // Routing $umur_unit
    if($umur_unit=="Tahun"){
        $label_unit_usia_1 = "selected";
        $label_unit_usia_2 = "";
        $label_unit_usia_3 = "";
    }else{
        if($umur_unit=="Bulan"){
            $label_unit_usia_1 = "";
            $label_unit_usia_2 = "selected";
            $label_unit_usia_3 = "";
        }else{
            if($umur_unit=="Hari"){
                $label_unit_usia_1 = "";
                $label_unit_usia_2 = "";
                $label_unit_usia_3 = "selected";
            }else{
                $label_unit_usia_1 = "selected";
                $label_unit_usia_2 = "";
                $label_unit_usia_3 = "";
            }
        }
    }

    // Routing Jenis Kelamin
    if($allow_sex==1){
        if($jenis_kelamin==""){
            $show_sex_0 = "";
            $show_sex_1 = "";
            $show_sex_2 = "selected";
        }else{
            if($jenis_kelamin=="Laki-laki"){
                $show_sex_0 = "selected";
                $show_sex_1 = "";
                $show_sex_2 = "";
            }else{
                if($jenis_kelamin=="Perempuan"){
                    $show_sex_0 = "";
                    $show_sex_1 = "selected";
                    $show_sex_2 = "";
                }else{
                    $show_sex_0 = "";
                    $show_sex_1 = "";
                    $show_sex_2 = "selected";
                }
            }
        }
    }else{
        $show_sex_0 = "";
        $show_sex_1 = "";
        $show_sex_2 = "selected";
    }

    // Normal Value
    if(empty($Data['normal_value'])){
        $label_normal_value = '';
    }else{
        $label_normal_value = 'checked';
    }

    // Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_category" value="'.$id_referensi_category.'">
        <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
    ';
    if($allow_age==true){
        echo '
            <div class="row mb-3">
                <div class="col-md-12">
                   <small> <b>Klasifikasi Berdasarkan Usia</b></small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_kategori_edit">
                        <small>Klasifikasi Usia</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="umur_kategori" id="umur_kategori_edit" class="form-control" value="'.$umur_kategori.'" required>
                    <small class="text text-grayish">
                        <small>Klasifikasi usia berdasarkan jarak usia Min - Max (Contoh : Balita, Neonatus, Anak-anak, Remaja Dll.)</small>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_min_edit">
                        <small>Usia Min</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" min="0" step="1" name="umur_min" id="umur_min_edit" class="form-control" value="'.$umur_min.'" placeholder="0">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_max_edit">
                        <small>Usia Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" min="0" step="1" name="umur_max" id="umur_max_edit" class="form-control" value="'.$umur_max.'" placeholder="0">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_unit_edit">
                        <small>Unit / Satuan Usia</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <select class="form-control" name="umur_unit" id="umur_unit_edit" required>
                        <option '.$label_unit_usia_1.' value="Tahun">Tahun</option>
                        <option '.$label_unit_usia_2.' value="Bulan">Bulan</option>
                        <option '.$label_unit_usia_3.' value="Hari">Hari</option>
                    </select>
                </div>
            </div>
            
        ';
    }

    if($allow_sex==true){
        echo '
            <div class="row mb-3 mt-3">
                <div class="col-md-12 mt-3">
                    <small><b>Klasifikasi Berdasarkan Jenis Kelamin</b></small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="jenis_kelamin_edit">
                        <small>Jenis Kelamin</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <select class="form-control" name="jenis_kelamin" id="jenis_kelamin_edit" required>
                        <option '.$show_sex_0.' value="Laki-laki">Laki-laki</option>
                        <option '.$show_sex_1.' value="Perempuan">Perempuan</option>
                        <option '.$show_sex_2.' value="All">Semua Gender</option>
                    </select>
                </div>
            </div>
        ';
    }

    echo '
        <div class="row mb-3 mt-3">
            <div class="col-md-12 mt-3">
                <small><b>Dasar Penentuan Interpertasi Hasil</b></small>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nilai_hasil_edit">
                    <small>Hasil Pemeriksaan</small>
                </label>
            </div>
            <div class="col-md-8">
                <div class="input-group mb-3">
                    <input type="text" name="nilai_hasil" id="nilai_hasil_edit" class="form-control" value="'.$nilai_hasil.'">
                </div>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-3 mt-3">
            <div class="col-md-12 mt-3">
                <small> <b>Label Klasifikasi Hasil</b></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="label_hasil_edit">
                    <small>Label Hasil</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="label_hasil" id="label_hasil_edit" class="form-control" value="'.$label.'" required>
                <small class="text text-grayish">
                    <small>Klasifikasi hasil yang ditetapkan secara lokal, menggunakan Bahasa Indonesia agar mudah dipahami.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fhir_display_edit">
                    <small><i>Display</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="fhir_display" id="fhir_display_edit" class="form-control" value="'.$fhir_display.'">
                <small class="text text-grayish">
                    <small>Klasifikasi hasil yang ditetapkan berdasarkan FHIR.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fhir_code_edit">
                    <small><i>Code</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="fhir_code" id="fhir_code_edit" class="form-control" value="'.$fhir_code.'">
                <small class="text text-grayish">
                    <small>Kode hasil yang ditetapkan berdasarkan FHIR.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fhir_system_edit">
                    <small><i>System</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="url" name="fhir_system" id="fhir_system_edit" class="form-control" placeholder="https://" value="'.$fhir_system.'">
                <small class="text text-grayish">
                    <small>Standar system yang digunakan.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
               <small><i>Normal Value</i></small>
            </div>
            <div class="col-md-8">
                <div class="form-check">
                    <input class="form-check-input" '.$label_normal_value.' type="checkbox" id="normal_value_edit2" name="normal_value" value="1">
                    <label class="form-check-label" for="normal_value_edit2">
                        <small class="text text-grayish">
                            <small>Tetapkan Sebagai Nilai Normal</small>
                        </small>
                    </label>
                </div>
            </div>
        </div>
    ';
?>
