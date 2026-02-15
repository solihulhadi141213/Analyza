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

    // Referensi Usia
    if(empty($_POST['id_referensi_usia'])){
        $id_referensi_usia = "";
        $umur_kategori     = "";
        $umur_min          = "";
        $umur_max          = "";
        $umur_unit         = "";
        $notasi_usia       = "";
    }else{
        $id_referensi_usia = $_POST['id_referensi_usia'];

        // Buka Detail Klasifikasi Usia
        $umur_kategori = GetDetailData($Conn, 'referensi_usia', 'id_referensi_usia', $id_referensi_usia, 'umur_kategori');
        $umur_min      = GetDetailData($Conn, 'referensi_usia', 'id_referensi_usia', $id_referensi_usia, 'umur_min');
        $umur_max      = GetDetailData($Conn, 'referensi_usia', 'id_referensi_usia', $id_referensi_usia, 'umur_max');
        $umur_unit     = GetDetailData($Conn, 'referensi_usia', 'id_referensi_usia', $id_referensi_usia, 'umur_unit');

        if(empty($umur_min)){
            $notasi_usia = "0 - $umur_max $umur_unit";
        }else{
            if(empty($umur_max)){
                $notasi_usia = "> $umur_min $umur_unit";
            }else{
                $notasi_usia = "$umur_min - $umur_max $umur_unit";
            }
        }
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
    
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
        <input type="hidden" name="id_referensi_usia" value="'.$id_referensi_usia.'">
        <input type="hidden" name="umur_kategori" value="'.$umur_kategori.'">
        <input type="hidden" name="umur_min" value="'.$umur_min.'">
        <input type="hidden" name="umur_max" value="'.$umur_max.'">
        <input type="hidden" name="umur_unit" value="'.$umur_unit.'">
    ';
    

    if($allow_age==true){
        echo '
            <div class="row mb-2">
                <div class="col-md-12">
                    <small><b>Klasifikasi Berdasarkan Usia</b></small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kategori Usia</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$umur_kategori.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Rentang Usia</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$notasi_usia.'</small>
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
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nilai_hasil">
                    <small>Hasil Pemeriksaan</small>
                </label>
            </div>
            <div class="col-md-8">
                <div class="input-group mb-3">
                    <input type="text" name="nilai_hasil" id="nilai_hasil" class="form-control">
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
            <small><i>Normal Value</i></small>
            </div>
            <div class="col-md-8">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="normal_value" name="normal_value" value="1">
                    <label class="form-check-label" for="normal_value">
                        <small class="text text-grayish">
                            <small>Tetapkan Sebagai Nilai Normal</small>
                        </small>
                    </label>
                </div>
            </div>
        </div>
    ';
    
?>