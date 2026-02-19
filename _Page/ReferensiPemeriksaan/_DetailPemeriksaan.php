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
    $id_referensi_pemeriksaan   = $Data['id_referensi_pemeriksaan'];
    $nama_pemeriksaan           = $Data['nama_pemeriksaan'];
    $category_pemeriksaan       = $Data['category_pemeriksaan'];
    $code_pemeriksaan           = $Data['code_pemeriksaan'];
    $display_pemeriksaan        = $Data['display_pemeriksaan'];
    $system_pemeriksaan         = $Data['system_pemeriksaan'];
    $unit                       = $Data['unit'] ?? '-';
    $unit_display               = $Data['unit_display'] ?? '-';
    $unit_code                  = $Data['unit_code'] ?? '-';
    $unit_system                = $Data['unit_system'] ?? '-';
    $result_type                = $Data['result_type'];
    $result_interpertation_type = $Data['result_interpertation_type'];
    $allow_age                  = $Data['allow_age'];
    $allow_sex                  = $Data['allow_sex'];
        
    // Penjelasan result_type
    $referensi_result_type = [
        "Numeric" => "Hasil berbasis nilai angka bilangan bulat",
        "Decimal" => "Hasil berbasis nilai angka desimal",
        "Coded" => "Hasil berbasis referensi kelompok kode",
        "Coded" => "Hasil berbasis referensi kelompok kode",
        "Text" => "Hasil berbasis text bebas (Kualitatif)",
        "Boolean" => "Hasil berbasis pernyataan (Y-Tidak / True-False)"
    ];
    $keterangan_result_type = $referensi_result_type[$result_type] ?? '-';

    // Penjelasan result_interpertation_type
    $referensi_result_interpertation_type = [
        "Range" => "Interpertasi hasil dilakukan berdasarkan jarak level/nilai tertentu",
        "Category" => "Interpertasi hasil dilakukan berdasarkan kelompok nilai tertentu"
    ];
    $keterangan_result_interpertation_type = $referensi_result_interpertation_type[$result_interpertation_type] ?? '-';

    // Routing Allow Age
    if($allow_age==1){
        $label_allow_age = '<label class="badge bg-success">Yes</label>';
    }else{
        $label_allow_age = '<label class="badge bg-dark">No</label>';
    }

    // Routing Allow Sex
    if($allow_sex==1){
        $label_allow_sex = '<label class="badge bg-success">Yes</label>';
    }else{
        $label_allow_sex = '<label class="badge bg-dark">No</label>';
    }
?>
<div class="row mb-3">
    <div class="col-md-12 mb-3 text-end">
        <button type="button" class="btn btn-md btn-dark btn-floating" id="kembali_ke_data" title="Kembali Ke Tabel Referensi Pemeriksaan">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-outline-dark reload_detail" title="Reload Data">
            <i class="bi bi-repeat"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-outline-primary modal_edit" data-id="<?php echo $id_referensi_pemeriksaan; ?>" title="Edit Referensi Pemeriksaan">
            <i class="bi bi-pencil"></i>
        </button>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <b class="card-title"># Detail Referensi Jenis Pemeriksaan</b>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <small><b>A. Informasi Pemeriksaan</b></small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Nama Pemeriksaan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish">
                        <small><?php echo $nama_pemeriksaan; ?></small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kategori</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $category_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Code</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $code_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Display</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $display_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>System</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $system_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-12 mt-3">
                        <small><b>B. Unit / Satuan Hasil</b></small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Unit / Satuan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $unit; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Unit Display</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $unit_display; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Unit Code</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $unit_code; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Unit System</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish"><small><?php echo $unit_system; ?></small></div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-12 mt-3">
                        <small><b>C. Tipe Hasil & Cara Interpertasi</b></small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Result Type</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <small>
                            <code class="text text-grayish underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="<?php echo $keterangan_result_type; ?>">
                                <?php echo $result_type; ?>
                            </code>
                        </small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Interpertation</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish">
                        <small>
                            <code class="text text-grayish underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="<?php echo $keterangan_result_interpertation_type; ?>">
                                <?php echo $result_interpertation_type; ?>
                            </code>
                        </small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Allow By Age</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish">
                        <?php echo $label_allow_age; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Allow By Sex</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6 text- text-grayish">
                        <?php echo $label_allow_sex; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <?php
            // Jenis Pemeriksaan Dengan Tipe Interpestasii 'Range'
            if($result_interpertation_type=="Range"){
        ?>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <b class="card-title"># Referensi Nilai Rujukan <i>(Range)</i></b>
                        </div>
                        <div class="col-4 text-end">
                            <?php
                                if($allow_age==1){
                                    echo '
                                        <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah_kelas_usia" data-id="'.$id_referensi_pemeriksaan.'">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    ';
                                }else{
                                    echo '
                                        <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah_range" data-id="'.$id_referensi_pemeriksaan.'" data-usia="">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    ';
                                }
                            ?>
                            
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="table-dark">
                                    <td align="center"><b>No</b></td>
                                    <td align="center"><b>L/P</b></td>
                                    <td align="left"><b>Klasifikasi</b></td>
                                    <td align="left"><b>Nilai</b></td>
                                    <td align="center"><b>Unit</b></td>
                                    <td align="left"><b>Kesimpulan</b></td>
                                    <td align="center"><b>Normal?</b></td>
                                    <td align="center"><b>Opsi</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Jika data Berdasarkan usia
                                    if($allow_age==1){
                                        $JumlahKlasifikasiUsia = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_usia FROM referensi_usia WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));
                                        if(empty($JumlahKlasifikasiUsia)){
                                            echo '
                                                <tr>
                                                    <td colspan="8" align="center">
                                                        <span class="text-danger">Tidak Ada Data Referensi Interpertasi Hasil Yang Ditampilkan</span>
                                                    </td>
                                                </tr>
                                            ';
                                        }else{
                                            $NomorUsia=1;
                                            $QryUsia = mysqli_query($Conn, "SELECT*FROM referensi_usia WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                                            while ($DataUsia = mysqli_fetch_array($QryUsia)) {
                                                $id_referensi_usia = $DataUsia['id_referensi_usia'];
                                                $umur_kategori     = $DataUsia['umur_kategori'];
                                                $umur_unit         = $DataUsia['umur_unit'];
                                                if(empty($DataUsia['umur_min'])){
                                                    $umur_max    = $DataUsia['umur_max'];
                                                    $notasi_usia = "0 - $umur_max $umur_unit";
                                                }else{
                                                    if(empty($DataUsia['umur_max'])){
                                                        $umur_min    = $DataUsia['umur_min'];
                                                        $notasi_usia = "> $umur_min $umur_unit";
                                                    }else{
                                                        $umur_min    = $DataUsia['umur_min'];
                                                        $umur_max    = $DataUsia['umur_max'];
                                                        $notasi_usia = "$umur_min - $umur_max $umur_unit";
                                                    }
                                                }

                                                // Menampilkan Baris Kelas usia
                                                echo '
                                                    <tr>
                                                        <td align="center"><b>'.$NomorUsia.'</b></td>
                                                        <td align="left" colspan="6"><b>'.$umur_kategori.' ('.$notasi_usia.')</b></td>
                                                        <td align="center">
                                                            <button type="button" class="btn btn-sm btn-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                                <li class="dropdown-header text-start">
                                                                    <h6>Option</h6>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_tambah_range" href="javascript:void(0)" data-id="'.$id_referensi_pemeriksaan.'" data-usia="'.$id_referensi_usia.'">
                                                                        <i class="bi bi-plus"></i> Tambah Interpertasi
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_edit_kelasifikasi_usia" href="javascript:void(0)" data-id="'.$id_referensi_usia  .'">
                                                                        <i class="bi bi-pencil"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_hapus_kelasifikasi_usia" href="javascript:void(0)" data-id="'.$id_referensi_usia  .'">
                                                                        <i class="bi bi-x"></i> Hapus
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                ';

                                                // Menampilkan List Interpertasi Berdasarkan usia
                                                $jumlah_range = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_range FROM referensi_range WHERE id_referensi_usia='$id_referensi_usia' AND id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));
                                                if(empty($jumlah_range)){
                                                    echo '
                                                        <tr>
                                                            <td colspan="8" align="center">
                                                                <span class="text-danger">Tidak Ada Data Referensi Interpertasi Hasil Yang Ditampilkan</span>
                                                            </td>
                                                        </tr>
                                                    ';
                                                }else{
                                                    $no=1;
                                                    $query = mysqli_query($Conn, "SELECT*FROM referensi_range WHERE id_referensi_usia='$id_referensi_usia' AND id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                                                    while ($data = mysqli_fetch_array($query)) {
                                                        $id_referensi_range = $data['id_referensi_range'];
                                                        $umur_kategori      = $data['umur_kategori'] ?? '-';
                                                        $umur_min           = $data['umur_min'];
                                                        $umur_max           = $data['umur_max'];
                                                        $umur_unit          = $data['umur_unit'];
                                                        $jenis_kelamin      = $data['jenis_kelamin'];
                                                        $nilai_min          = $data['nilai_min'];
                                                        $nilai_max          = $data['nilai_max'];
                                                        $operator           = $data['operator'];
                                                        $label              = $data['label'];
                                                        $fhir_display       = $data['fhir_display'];
                                                        $fhir_code          = $data['fhir_code'];
                                                        $fhir_system        = $data['fhir_system'];
                                                        $conclusion         = $data['conclusion'];
                                                        $normal_value       = $data['normal_value'];

                                                        // Number Format
                                                        $tampil_min = number_format($nilai_min, 2, ',', '.');
                                                        $tampil_max = number_format($nilai_max, 2, ',', '.');

                                                        // Menentukan Penyataan nilai rujukan
                                                        if($operator=="More"){
                                                            $nilai_rujukan = "n ≥ $tampil_min";
                                                        }
                                                        if($operator=="Between"){
                                                            $nilai_rujukan = "$tampil_min - $tampil_max";
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

                                                        // Normal Value
                                                        if(empty($data['normal_value'])){
                                                            $label_normal_value = '<span class="text-danger"><i class="bi bi-x-circle"></i></span>';
                                                        }else{
                                                            $label_normal_value = '<span class="text-success"><i class="bi bi-check-circle"></i></span>';
                                                        }

                                                        echo '
                                                            <tr>
                                                                <td align="center"><small>'.$NomorUsia.'.'.$no.'</small></td>
                                                                <td align="center"><small>'.$show_sex.'</small></td>
                                                                <td align="left">
                                                                    <a href="javascript:void(0);" class="modal_detail_range" data-id="'.$id_referensi_range.'">
                                                                        <small>'.$label.'</small>
                                                                    </a>
                                                                </td>
                                                                <td align="left"><small>'.$nilai_rujukan.'</small></td>
                                                                <td align="center"><small>'.$unit_display.'</small></td>
                                                                <td align="left"><small>'.$conclusion.'</small></td>
                                                                <td align="center">'.$label_normal_value.'</td>
                                                                <td align="center">
                                                                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="bi bi-three-dots-vertical"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                                        <li class="dropdown-header text-start">
                                                                            <h6>Option</h6>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item modal_detail_range" href="javascript:void(0)" data-id="'.$id_referensi_range  .'">
                                                                                <i class="bi bi-info-circle"></i> Detail
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item modal_edit_range" href="javascript:void(0)" data-id="'.$id_referensi_range  .'">
                                                                                <i class="bi bi-pencil"></i> Edit
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item modal_delete_range" href="javascript:void(0)" data-id="'.$id_referensi_range  .'">
                                                                                <i class="bi bi-x"></i> Hapus
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        ';
                                                        $no++;
                                                    }
                                                }

                                                $NomorUsia++;
                                            }
                                        }
                                    }else{  

                                        // Jika Data Tidak Dikelompokan Berdasakan Usia
                                        $jumlah_range = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_range FROM referensi_range WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));
                                        if(empty($jumlah_range)){
                                            echo '
                                                <tr>
                                                    <td colspan="8" align="center">
                                                        <span class="text-danger">Tidak Ada Data Referensi Nilai Rujukan Yang Ditampilkan</span>
                                                    </td>
                                                </tr>
                                            ';
                                        }else{
                                            $no=1;
                                            $query = mysqli_query($Conn, "SELECT*FROM referensi_range WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                                            while ($data = mysqli_fetch_array($query)) {
                                                $id_referensi_range = $data['id_referensi_range'];
                                                $umur_kategori      = $data['umur_kategori'] ?? '-';
                                                $umur_min           = $data['umur_min'];
                                                $umur_max           = $data['umur_max'];
                                                $umur_unit          = $data['umur_unit'];
                                                $jenis_kelamin      = $data['jenis_kelamin'];
                                                $nilai_min          = $data['nilai_min'];
                                                $nilai_max          = $data['nilai_max'];
                                                $operator           = $data['operator'];
                                                $label              = $data['label'];
                                                $fhir_display       = $data['fhir_display'];
                                                $fhir_code          = $data['fhir_code'];
                                                $fhir_system        = $data['fhir_system'];
                                                $conclusion         = $data['conclusion'];
                                                $normal_value       = $data['normal_value'];

                                                // Number Format
                                                $tampil_min = number_format($nilai_min, 2, ',', '.');
                                                $tampil_max = number_format($nilai_max, 2, ',', '.');

                                                // Menentukan Penyataan nilai rujukan
                                                if($operator=="<"){
                                                    $nilai_rujukan = "< $tampil_min";
                                                }
                                                if($operator==">"){
                                                    $nilai_rujukan = "> $tampil_max";
                                                }
                                                if($operator=="<="){
                                                    $nilai_rujukan = "< $tampil_min";
                                                }
                                                if($operator==">="){
                                                    $nilai_rujukan = "> $tampil_max";
                                                }
                                                if($operator=="-"){
                                                    $nilai_rujukan = "$tampil_min - $tampil_max";
                                                }
                                                if($operator=="between"){
                                                    $nilai_rujukan = "$tampil_min > - < $tampil_max";
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

                                                // Normal Value
                                                if(empty($data['normal_value'])){
                                                    $label_normal_value = '<span class="text-danger"><i class="bi bi-x-circle"></i></span>';
                                                }else{
                                                    $label_normal_value = '<span class="text-success"><i class="bi bi-check-circle"></i></span>';
                                                }

                                                echo '
                                                    <tr>
                                                        <td align="center"><small>'.$no.'</small></td>
                                                        <td align="center"><small>'.$show_sex.'</small></td>
                                                        <td align="left">
                                                            <a href="javascript:void(0);" class="modal_detail_range" data-id="'.$id_referensi_range.'">
                                                                <small>'.$label.'</small>
                                                            </a>
                                                        </td>
                                                        <td align="left"><small>'.$nilai_rujukan.'</small></td>
                                                        <td align="center"><small>'.$unit_display.'</small></td>
                                                        <td align="left"><small>'.$conclusion.'</small></td>
                                                        <td align="center">'.$label_normal_value.'</td>
                                                        <td align="center">
                                                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                                <li class="dropdown-header text-start">
                                                                    <h6>Option</h6>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_detail_range" href="javascript:void(0)" data-id="'.$id_referensi_range  .'">
                                                                        <i class="bi bi-info-circle"></i> Detail
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_edit_range" href="javascript:void(0)" data-id="'.$id_referensi_range  .'">
                                                                        <i class="bi bi-pencil"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_delete_range" href="javascript:void(0)" data-id="'.$id_referensi_range  .'">
                                                                        <i class="bi bi-x"></i> Hapus
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                ';
                                                $no++;
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php }elseif($result_interpertation_type=="Category"){ ?>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <b class="card-title"># Referensi <i> Category </i></b>
                        </div>
                        <div class="col-4 text-end">
                            <?php
                                if($allow_age==1){
                                    echo '
                                        <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah_kelas_usia" data-id="'.$id_referensi_pemeriksaan.'">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    ';
                                }else{
                                    echo '
                                        <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah_category" data-id="'.$id_referensi_pemeriksaan.'" data-usia="">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    ';
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr class="table-dark">
                                    <td align="center"><b>No</b></td>
                                    <td align="left"><b>L/P</b></td>
                                    <td align="left"><b>Label Category</b></td>
                                    <td align="left"><b>Hasil</b></td>
                                    <td align="center"><b>Normal?</b></td>
                                    <td align="center"><b>Opsi</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if($allow_age==1){
                                        // Jika Category Berkaitan Dengan Klasifikasi Usia
                                        $JumlahKlasifikasiUsia = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_usia FROM referensi_usia WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));
                                        if(empty($JumlahKlasifikasiUsia)){
                                            echo '
                                                <tr>
                                                    <td colspan="8" align="center">
                                                        <span class="text-danger">Tidak Ada Data Referensi Interpertasi Hasil Yang Ditampilkan</span>
                                                    </td>
                                                </tr>
                                            ';
                                        }else{
                                            $NomorUsia=1;
                                            $QryUsia = mysqli_query($Conn, "SELECT*FROM referensi_usia WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                                            while ($DataUsia = mysqli_fetch_array($QryUsia)) {
                                                $id_referensi_usia = $DataUsia['id_referensi_usia'];
                                                $umur_kategori     = $DataUsia['umur_kategori'];
                                                $umur_unit         = $DataUsia['umur_unit'];
                                                if(empty($DataUsia['umur_min'])){
                                                    $umur_max    = $DataUsia['umur_max'];
                                                    $notasi_usia = "0 - $umur_max $umur_unit";
                                                }else{
                                                    if(empty($DataUsia['umur_max'])){
                                                        $umur_min    = $DataUsia['umur_min'];
                                                        $notasi_usia = "> $umur_min $umur_unit";
                                                    }else{
                                                        $umur_min    = $DataUsia['umur_min'];
                                                        $umur_max    = $DataUsia['umur_max'];
                                                        $notasi_usia = "$umur_min - $umur_max $umur_unit";
                                                    }
                                                }

                                                // Menampilkan Baris Kelas usia
                                                echo '
                                                    <tr>
                                                        <td align="center"><b>'.$NomorUsia.'</b></td>
                                                        <td align="left" colspan="4"><b>'.$umur_kategori.' ('.$notasi_usia.')</b></td>
                                                        <td align="center">
                                                            <button type="button" class="btn btn-sm btn-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                                <li class="dropdown-header text-start">
                                                                    <h6>Option</h6>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_tambah_category" href="javascript:void(0)" data-id="'.$id_referensi_pemeriksaan.'" data-usia="'.$id_referensi_usia.'">
                                                                        <i class="bi bi-plus"></i> Tambah Interpertasi
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_edit_kelasifikasi_usia" href="javascript:void(0)" data-id="'.$id_referensi_usia  .'">
                                                                        <i class="bi bi-pencil"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_hapus_kelasifikasi_usia" href="javascript:void(0)" data-id="'.$id_referensi_usia  .'">
                                                                        <i class="bi bi-x"></i> Hapus
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                ';

                                                // Menampilkan data Category
                                                $jumlah_category = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_category FROM referensi_category WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan' AND id_referensi_usia='$id_referensi_usia'"));
                                                if(empty($jumlah_category)){
                                                    echo '
                                                        <tr>
                                                            <td colspan="6" align="center">
                                                                <span class="text-danger">Tidak Ada Data Referensi Nilai Rujukan Yang Ditampilkan</span>
                                                            </td>
                                                        </tr>
                                                    ';
                                                }else{
                                                    $no=1;
                                                    $query = mysqli_query($Conn, "SELECT*FROM referensi_category WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan' AND id_referensi_usia='$id_referensi_usia'");
                                                    while ($data = mysqli_fetch_array($query)) {
                                                        $id_referensi_category = $data['id_referensi_category'];
                                                        $umur_kategori         = $data['umur_kategori'] ?? '-';
                                                        $umur_min              = $data['umur_min'];
                                                        $umur_max              = $data['umur_max'];
                                                        $umur_unit             = $data['umur_unit'];
                                                        $jenis_kelamin         = $data['jenis_kelamin'];
                                                        $nilai_hasil           = $data['nilai_hasil'];
                                                        $label                 = $data['label'];
                                                        $fhir_display          = $data['fhir_display'];
                                                        $fhir_code             = $data['fhir_code'];
                                                        $fhir_system           = $data['fhir_system'];

                                                        // Menampilkan Usia
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

                                                        // Menampilkan Jenis Kelamin
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

                                                        // Normal Value
                                                        if(empty($data['normal_value'])){
                                                            $label_normal_value = '<span class="text-danger"><i class="bi bi-x-circle"></i></span>';
                                                        }else{
                                                            $label_normal_value = '<span class="text-success"><i class="bi bi-check-circle"></i></span>';
                                                        }

                                                        echo '
                                                            <tr>
                                                                <td align="center"><small>'.$NomorUsia.'.'.$no.'</small></td>
                                                                <td align="left"><small>'.$show_sex.'</small></td>
                                                                <td align="left">
                                                                    <a href="javascript:void(0);" class="modal_detail_category" data-id="'.$id_referensi_category.'">
                                                                        <small>'.$label.'</small>
                                                                    </a>
                                                                </td>
                                                                <td align="left"><small>'.$nilai_hasil.'</small></td>
                                                                <td align="center">'.$label_normal_value.'</td>
                                                                <td align="center">
                                                                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="bi bi-three-dots-vertical"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                                        <li class="dropdown-header text-start">
                                                                            <h6>Option</h6>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item modal_detail_category" href="javascript:void(0)" data-id="'.$id_referensi_category  .'">
                                                                                <i class="bi bi-info-circle"></i> Detail
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item modal_edit_category" href="javascript:void(0)" data-id="'.$id_referensi_category  .'">
                                                                                <i class="bi bi-pencil"></i> Edit
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item modal_delete_category" href="javascript:void(0)" data-id="'.$id_referensi_category  .'">
                                                                                <i class="bi bi-x"></i> Hapus
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        ';
                                                        $no++;
                                                    }
                                                }
                                                

                                                $NomorUsia++;
                                            }
                                        }
                                    }else{
                                        $jumlah_category = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_category FROM referensi_category WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));
                                        if(empty($jumlah_category)){
                                            echo '
                                                <tr>
                                                    <td colspan="6" align="center">
                                                        <span class="text-danger">Tidak Ada Data Referensi Nilai Rujukan Yang Ditampilkan</span>
                                                    </td>
                                                </tr>
                                            ';
                                        }else{
                                            $no=1;
                                            $query = mysqli_query($Conn, "SELECT*FROM referensi_category WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                                            while ($data = mysqli_fetch_array($query)) {
                                                $id_referensi_category = $data['id_referensi_category'];
                                                $umur_kategori         = $data['umur_kategori'] ?? '-';
                                                $umur_min              = $data['umur_min'];
                                                $umur_max              = $data['umur_max'];
                                                $umur_unit             = $data['umur_unit'];
                                                $jenis_kelamin         = $data['jenis_kelamin'];
                                                $nilai_hasil           = $data['nilai_hasil'];
                                                $label                 = $data['label'];
                                                $fhir_display          = $data['fhir_display'];
                                                $fhir_code             = $data['fhir_code'];
                                                $fhir_system           = $data['fhir_system'];

                                                // Menampilkan Usia
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

                                                // Menampilkan Jenis Kelamin
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

                                                // Normal Value
                                                if(empty($data['normal_value'])){
                                                    $label_normal_value = '<span class="text-danger"><i class="bi bi-x-circle"></i></span>';
                                                }else{
                                                    $label_normal_value = '<span class="text-success"><i class="bi bi-check-circle"></i></span>';
                                                }

                                                echo '
                                                    <tr>
                                                        <td align="center"><small>'.$no.'</small></td>
                                                        <td align="left"><small>'.$show_sex.'</small></td>
                                                        <td align="left">
                                                            <a href="javascript:void(0);" class="modal_detail_category" data-id="'.$id_referensi_category.'">
                                                                <small>'.$label.'</small>
                                                            </a>
                                                        </td>
                                                        <td align="left"><small>'.$nilai_hasil.'</small></td>
                                                        <td align="center">'.$label_normal_value.'</td>
                                                        <td align="center">
                                                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                                <li class="dropdown-header text-start">
                                                                    <h6>Option</h6>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_detail_category" href="javascript:void(0)" data-id="'.$id_referensi_category  .'">
                                                                        <i class="bi bi-info-circle"></i> Detail
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_edit_category" href="javascript:void(0)" data-id="'.$id_referensi_category  .'">
                                                                        <i class="bi bi-pencil"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item modal_delete_category" href="javascript:void(0)" data-id="'.$id_referensi_category  .'">
                                                                        <i class="bi bi-x"></i> Hapus
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                ';
                                                $no++;
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <b class="card-title"># Referensi Metode & Spesimen</b>
                    </div>
                    <div class="col-4 text-end">
                        <button type="button" class="btn btn-md btn-primary btn-floating modal_tambah_relasi" data-id="<?php echo "$id_referensi_pemeriksaan"; ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Tambah Referensi Metode Dan Spesimen">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td align="center"><b>No</b></td>
                                <td align="left"><b>Metode Pemeriksaan</b></td>
                                <td align="left"><b>Spesimen</b></td>
                                <td align="left"><b>Pengambilan</b></td>
                                <td align="left"><b>Kontainer</b></td>
                                <td align="center"><b>Opsi</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $jumlah_referensi_relasi = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_pemeriksaan_relasi FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));
                                if(empty($jumlah_referensi_relasi)){
                                    echo '
                                        <tr>
                                            <td colspan="6" align="center">
                                                <span class="text-danger">Tidak Ada Data Referensi Nilai Rujukan Yang Ditampilkan</span>
                                            </td>
                                        </tr>
                                    ';
                                }else{
                                    $no=1;
                                    $query = mysqli_query($Conn, "SELECT*FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_referensi_pemeriksaan_relasi = $data['id_referensi_pemeriksaan_relasi'];
                                        $id_referensi_metode_pemeriksaan = $data['id_referensi_metode_pemeriksaan'];
                                        $id_referensi_jenis_spesimen     = $data['id_referensi_jenis_spesimen'];
                                        $id_referensi_metode_sample      = $data['id_referensi_metode_sample'];
                                        $id_referensi_container          = $data['id_referensi_container'];
                                       
                                        // Definisikan masing-masing ID
                                        $nama_metode_pemeriksaan = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'nama_metode_pemeriksaan');
                                        $nama_spesimen           = GetDetailData($Conn, 'referensi_jenis_spesimen', 'id_referensi_jenis_spesimen', $id_referensi_jenis_spesimen, 'nama_spesimen');
                                        $nama_metode_sample      = GetDetailData($Conn, 'referensi_metode_sample', 'id_referensi_metode_sample', $id_referensi_metode_sample, 'nama_metode_sample');
                                        $nama_container          = GetDetailData($Conn, 'referensi_container', 'id_referensi_container', $id_referensi_container, 'nama_container');

                                        echo '
                                            <tr>
                                                <td align="center"><small>'.$no.'</small></td>
                                                <td align="left">
                                                    <a href="javascript:void(0);" class="modal_detail_relasi" data-id="'.$id_referensi_pemeriksaan_relasi.'">
                                                        <small>'.$nama_metode_pemeriksaan.'</small>
                                                    </a>
                                                </td>
                                                <td align="left"><small>'.$nama_spesimen.'</small></td>
                                                <td align="left"><small>'.$nama_metode_sample.'</small></td>
                                                <td align="left"><small>'.$nama_container.'</small></td>
                                                <td align="center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-floating modal_delete_relasi" data-id="'.$id_referensi_pemeriksaan_relasi.'">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ';
                                        $no++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>