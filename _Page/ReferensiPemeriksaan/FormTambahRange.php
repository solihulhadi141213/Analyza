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

    // Buat Variabel
    $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'];
    $result_type              = $Data['result_type'];
    $allow_age                = $Data['allow_age'];
    $allow_sex                = $Data['allow_sex'];

    // Unit Satuan
    $unit                       = $Data['unit'] ?? '-';
    $unit_display               = $Data['unit_display'] ?? '-';
    $unit_code                  = $Data['unit_code'] ?? '-';
    $unit_system                = $Data['unit_system'] ?? '-';
    
    //Tampilkan Data
    echo '
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
                    <label for="umur_kategori">
                        <small>Klasifikasi Usia</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="umur_kategori" id="umur_kategori" class="form-control" required>
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
                    <input type="number" min="0" step="1" name="umur_min" id="umur_min" class="form-control" placeholder="0">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_max">
                        <small>Usia Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="number" min="0" step="1" name="umur_max" id="umur_max" class="form-control" placeholder="0">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="umur_unit">
                        <small>Unit / Satuan Usia</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <select class="form-control" name="umur_unit" id="umur_unit" required>
                        <option value="Tahun">Tahun</option>
                        <option value="Bulan">Bulan</option>
                        <option value="Hari">Hari</option>
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
                    <label for="jenis_kelamin">
                        <small>Jenis Kelamin</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                        <option value="All">Semua Gender</option>
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
                    <label for="nilai_min">
                        <small>Nilai Min</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="1" name="nilai_min" id="nilai_min" class="form-control" placeholder="0.00">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nilai_max">
                        <small>Nilai Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="1" name="nilai_max" id="nilai_max" class="form-control" placeholder="0.00">
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
                    <label for="nilai_min">
                        <small>Nilai Min</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="0.01" name="nilai_min" id="nilai_min" class="form-control" placeholder="0.00">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nilai_max">
                        <small>Nilai Max</small>
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="number" min="0" step="0.01" name="nilai_max" id="nilai_max" class="form-control" placeholder="0.00">
                        <span class="input-group-text" id="basic-addon2">'.$unit_display.'</span>
                    </div>
                </div>
            </div>
        ';
    }
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="operator">
                    <small>Operator</small>
                </label>
            </div>
            <div class="col-md-8">
                <select class="form-control" name="operator" id="operator">
                    <option value="">Pilih</option>
                    <option value="<"> X < N Min </option>
                    <option value=">"> X > N Max </option>
                    <option value="<="> X <= N Min </option>
                    <option value=">="> X >= N Max </option>
                    <option value="-"> N min - N Max (X >= N min | X <= N max) </option>
                    <option value="between"> Between (X > N min | X < N max) </option>
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
                <label for="label_hasil">
                    <small>Label Hasil</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="label_hasil" id="label_hasil" class="form-control" required>
                <small class="text text-grayish">
                    <small>Klasifikasi hasil yang ditetapkan secara lokal, menggunakan Bahasa Indonesia agar mudah dipahami.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fhir_display">
                    <small><i>Display</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="fhir_display" id="fhir_display" class="form-control">
                <small class="text text-grayish">
                    <small>Klasifikasi hasil yang ditetapkan berdasarkan FHIR.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fhir_code">
                    <small><i>Code</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="fhir_code" id="fhir_code" class="form-control">
                <small class="text text-grayish">
                    <small>Kode hasil yang ditetapkan berdasarkan FHIR.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fhir_system">
                    <small><i>System</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="url" name="fhir_system" id="fhir_system" class="form-control" placeholder="https://">
                <small class="text text-grayish">
                    <small>Standar system yang digunakan.</small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="conclusion">
                    <small>Kesimpulan Akhir</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="conclusion" id="conclusion" class="form-control">
                <small class="text text-grayish">
                    <small>Diisi Hanya Jika Hasil Pemeriksaan Menghasilkan Kesimpulan Akhir (Contoh : Normal, Abnormal)</small>
                </small>
            </div>
        </div>
    ';
    
?>