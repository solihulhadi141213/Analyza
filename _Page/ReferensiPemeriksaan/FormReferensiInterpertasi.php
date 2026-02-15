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
    $nama_pemeriksaan           = $Data['nama_pemeriksaan'];
    $category_pemeriksaan       = $Data['category_pemeriksaan'];
    $display_pemeriksaan        = $Data['display_pemeriksaan'];
    $code_pemeriksaan           = $Data['code_pemeriksaan'];
    $system_pemeriksaan         = $Data['system_pemeriksaan'];
    $unit_display               = $Data['unit_display'];
    $result_type                = $Data['result_type'];
    $result_interpertation_type = $Data['result_interpertation_type'];
    $allow_age                  = $Data['allow_age'];
    $allow_sex                  = $Data['allow_sex'];
    
    // Tampilkan Informasi Pemeriksaan
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>Kategori</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$category_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$nama_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Display</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$display_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$code_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>System</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish text-long">'.$system_pemeriksaan.'</small>
            </div>
        </div>
    ';

    // Jika Tidak Diinterpertasikan
    if($result_interpertation_type=="None"){
        echo '
            <div class="row mb-2">
                <div class="col-12">
                     <div class="alert alert-danger">
                        <small>Pemeriksaan ini tidak memiliki interpertasi</small>
                    </div>
                </div>
            </div>
        ';
        exit;
    }
?>

<!-- Menampilkan Tabel Interpertasi Berdasakran Tipe -->
<?php if($result_interpertation_type=="Range"){ ?>
    <div class="row mt-2">
        <div class="col-12">
            <div class="table table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <td align="center"><b>No</b></td>
                            <td align="left"><b>Usia</b></td>
                            <td align="left"><b>L/P</b></td>
                            <td align="left"><b>Klasifikasi</b></td>
                            <td align="left"><b>Nilai</b></td>
                            <td align="left"><b>Kesimpulan</b></td>
                            <td align="left"><b>Normal?</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Jika Tidak Diklasifikasikan Berdasarkan usia
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

                                    // Normal Value
                                    if(empty($data['normal_value'])){
                                        $label_normal_value = '<span class="text-danger"><i class="bi bi-x-circle"></i></span>';
                                    }else{
                                        $label_normal_value = '<span class="text-success"><i class="bi bi-check-circle"></i></span>';
                                    }

                                    echo '
                                        <tr>
                                            <td align="center"><small>'.$no.'</small></td>
                                            <td align="left"><small>'.$show_age.'</small></td>
                                            <td align="left"><small>'.$show_sex.'</small></td>
                                            <td align="left"><small>'.$label.'</small></td>
                                            <td align="left"><small>'.$nilai_rujukan.' '.$unit_display.'</small></td>
                                            <td align="left"><small>'.$conclusion.'</small></td>
                                            <td align="center">'.$label_normal_value.'</td>
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
<?php } ?>
<?php if($result_interpertation_type=="Category"){ ?>
    <div class="row mt-2">
        <div class="col-12">
            <div class="table table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                           <td align="center"><b>No</b></td>
                            <td align="left"><b>Usia</b></td>
                            <td align="left"><b>L/P</b></td>
                            <td align="left"><b>Label Category</b></td>
                            <td align="left"><b>Hasil</b></td>
                            <td align="center"><b>Normal?</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
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
                                            <td align="left"><small>'.$show_age.'</small></td>
                                            <td align="left"><small>'.$show_sex.'</small></td>
                                            <td align="left"><small>'.$label.'</small></td>
                                            <td align="left"><small>'.$nilai_hasil.'</small></td>
                                            <td align="center">'.$label_normal_value.'</td>
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
<?php } ?>