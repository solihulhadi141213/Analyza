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
        exit;
    }
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

    // Buka $allow_age dan $allow_sex
    $unit_display = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'unit_display');
    $allow_age = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_age');
    $allow_sex = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'allow_sex');
    $result_type = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'result_type');
    
    // Menentukan Penyataan nilai rujukan
    if($operator==""){
        $operator_0 = "selected";
        $operator_1 = "";
        $operator_2 = "";
        $operator_3 = "";
        $operator_4 = "";
        $operator_5 = "";
        $operator_6 = "";
    }
    if($operator=="<"){
        $operator_0 = "";
        $operator_1 = "selected";
        $operator_2 = "";
        $operator_3 = "";
        $operator_4 = "";
        $operator_5 = "";
        $operator_6 = "";
    }
    if($operator==">"){
        $operator_0 = "";
        $operator_1 = "";
        $operator_2 = "selected";
        $operator_3 = "";
        $operator_4 = "";
        $operator_5 = "";
        $operator_6 = "";
    }
    if($operator=="<="){
        $operator_0 = "";
        $operator_1 = "";
        $operator_2 = "";
        $operator_3 = "selected";
        $operator_4 = "";
        $operator_5 = "";
        $operator_6 = "";
    }
    if($operator==">="){
        $operator_0 = "";
        $operator_1 = "";
        $operator_2 = "";
        $operator_3 = "";
        $operator_4 = "selected";
        $operator_5 = "";
        $operator_6 = "";
    }
    if($operator=="-"){
        $operator_0 = "";
        $operator_1 = "";
        $operator_2 = "";
        $operator_3 = "";
        $operator_4 = "";
        $operator_5 = "selected";
        $operator_6 = "";
    }
    if($operator=="between"){
        $operator_0 = "";
        $operator_1 = "";
        $operator_2 = "";
        $operator_3 = "";
        $operator_4 = "";
        $operator_5 = "";
        $operator_6 = "selected";
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
            $show_sex_0 = "selected";
            $show_sex_1 = "";
            $show_sex_2 = "";
        }else{
            if($jenis_kelamin=="Laki-laki"){
                $show_sex_0 = "";
                $show_sex_1 = "selected";
                $show_sex_2 = "";
            }else{
                if($jenis_kelamin=="Perempuan"){
                    $show_sex_0 = "";
                    $show_sex_1 = "";
                    $show_sex_2 = "selected";
                }else{
                    $show_sex_0 = "selected";
                    $show_sex_1 = "";
                    $show_sex_2 = "";
                }
            }
        }
    }else{
        $show_sex_0 = "selected";
        $show_sex_1 = "";
        $show_sex_2 = "";
    }
       
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_range" value="'.$id_referensi_range.'">
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
                    <label for="umur_min">
                        <small>Usia Min</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" min="0" step="1" name="umur_min" id="umur_min_edit" class="form-control" placeholder="0" value="'.$umur_min.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_max">
                        <small>Usia Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" min="0" step="1" name="umur_max" id="umur_max_edit" class="form-control" placeholder="0" value="'.$umur_max.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_unit">
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
                        <option '.$show_sex_1.' value="Laki-laki">Laki-laki</option>
                        <option '.$show_sex_2.' value="Perempuan">Perempuan</option>
                        <option '.$show_sex_0.' value="All">Semua Gender</option>
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
    if($result_type=="Numeric"){
        echo '
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nilai_min_edit">
                        <small>Nilai Min</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="1" name="nilai_min" id="nilai_min_edit" class="form-control" placeholder="0.00" value="'.$nilai_min.'">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nilai_max_edit">
                        <small>Nilai Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="1" name="nilai_max" id="nilai_max_edit" class="form-control" placeholder="0.00" value="'.$nilai_max.'">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
        ';
    }
    if($result_type=="Decimal"){
        echo '
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nilai_min_edit">
                        <small>Nilai Min</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="0.01" name="nilai_min" id="nilai_min_edit" class="form-control" placeholder="0.00" value="'.$nilai_min.'">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nilai_max_edit">
                        <small>Nilai Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="0.01" name="nilai_max" id="nilai_max_edit" class="form-control" placeholder="0.00" value="'.$nilai_max.'">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
        ';
    }
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="operator_edit">
                    <small>Operator</small>
                </label>
            </div>
            <div class="col-md-8">
                <select class="form-control" name="operator" id="operator_edit">
                    <option '.$operator_0.' value="">Pilih</option>
                    <option '.$operator_1.' value="<"> X < N Min </option>
                    <option '.$operator_2.' value=">"> X > N Max </option>
                    <option '.$operator_3.' value="<="> X <= N Min </option>
                    <option '.$operator_4.' value=">="> X >= N Max </option>
                    <option '.$operator_5.' value="-"> N min - N Max (X >= N min | X <= N max) </option>
                    <option '.$operator_6.' value="between"> Between (X > N min | X < N max) </option>
                </select>
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
                <label for="conclusion_edit">
                    <small>Kesimpulan Akhir</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="conclusion" id="conclusion_edit" class="form-control" value="'.$conclusion.'">
                <small class="text text-grayish">
                    <small>Diisi Hanya Jika Hasil Pemeriksaan Menghasilkan Kesimpulan Akhir (Contoh : Normal, Abnormal)</small>
                </small>
            </div>
        </div>
    ';
?>