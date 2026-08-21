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

    //id_laboratorium_rincian wajib terisi
    if(empty($_POST['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_rincian' dan sanitasi
    $id_laboratorium_rincian = validateAndSanitizeInput($_POST['id_laboratorium_rincian']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
    $Qry->bind_param("i", $id_laboratorium_rincian);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari tabel laboratorium_rincian!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data rincian laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium          = $Data['id_laboratorium'];
    $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'];
    
    // Buka Data Laboratorium
    $Qry2 = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry2->bind_param("s", $id_laboratorium);
    if (!$Qry2->execute()) {
        $error2=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari tabel laboratorium!<br>Keterangan : '.$error2.'</small>
            </div>
        ';
        exit;
    }
    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    if (empty($Data2)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    $id_pasien        = $Data2['id_pasien'];
    $nama             = $Data2['nama'];
    $gender           = $Data2['gender'];
    $tanggal_lahir    = $Data2['tanggal_lahir'];
    $datetime_diminta = $Data2['datetime_diminta'];

    // Hitung usia berdasarkan tanggal lahir sampai tanggal diminta
    $usia_pasien = '';
    $SatuanUsia = '';
    $usia_tahun = null;
    $usia_bulan = null;
    $usia_hari = null;
    if (!empty($tanggal_lahir) && !empty($datetime_diminta)) {
        try {
            $tgl_lahir_obj = new DateTime($tanggal_lahir);
            $tgl_diminta_obj = new DateTime($datetime_diminta);

            if ($tgl_lahir_obj <= $tgl_diminta_obj) {
                $selisih_usia = $tgl_lahir_obj->diff($tgl_diminta_obj);
                $usia_tahun = (int)$selisih_usia->y;
                $usia_bulan = (int)($selisih_usia->y * 12 + $selisih_usia->m);
                $usia_hari = (int)$selisih_usia->days;
                if ($selisih_usia->y > 0) {
                    $usia_pasien_real = $selisih_usia->y;
                    $usia_pasien = $selisih_usia->y . ' Tahun';
                    $SatuanUsia = 'Tahun';
                } elseif ($selisih_usia->m > 0) {
                    $usia_pasien_real = $selisih_usia->m;
                    $usia_pasien = $selisih_usia->m . ' Bulan';
                    $SatuanUsia = 'Bulan';
                } else {
                    $usia_pasien_real = $selisih_usia->d;
                    $usia_pasien = $selisih_usia->d . ' Hari';
                    $SatuanUsia = 'Hari';
                }
            }
        } catch (Exception $e) {
            $usia_pasien = '';
        }
    }

    // Buka Referensi Pemeriksaan
    $Qry3 = $Conn->prepare("SELECT * FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
    $Qry3->bind_param("i", $id_referensi_pemeriksaan);
    if (!$Qry3->execute()) {
        $error3=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari tabel referensi_pemeriksaan!<br>Keterangan : '.$error3.'</small>
            </div>
        ';
        exit;
    }
    $Result3 = $Qry3->get_result();
    $Data3 = $Result3->fetch_assoc();
    $Qry3->close();

    if (empty($Data3)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data Referensi Pemeriksaan tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    $result_type                = $Data3['result_type'];
    $category_pemeriksaan       = $Data3['category_pemeriksaan'];
    $nama_pemeriksaan           = $Data3['nama_pemeriksaan'];
    $code_pemeriksaan           = $Data3['code_pemeriksaan'];
    $system_pemeriksaan         = $Data3['system_pemeriksaan'];
    $unit                       = $Data3['unit'];
    $display_pemeriksaan        = $Data3['display_pemeriksaan'];
    $result_interpertation_type = $Data3['result_interpertation_type'];
    $allow_age                  = $Data3['allow_age'];
    $allow_sex                  = $Data3['allow_sex'];

    // Klasifikasi usia Pasien
    $klasifikasi_usia = "";
    $id_referensi_usia = "";
    $QueryKelasUsia = mysqli_query($Conn, "SELECT * FROM referensi_usia WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan' AND umur_unit='$SatuanUsia'");
    while ($RowKelasUsia = mysqli_fetch_assoc($QueryKelasUsia)) {
        if(empty($RowKelasUsia['umur_min'])){
            if($usia_pasien_real <= $RowKelasUsia['umur_max']){
                $klasifikasi_usia  = $RowKelasUsia['umur_kategori'];
                 $id_referensi_usia = $RowKelasUsia['id_referensi_usia'];
            }
        }else{
            if(empty($RowKelasUsia['umur_max'])){
                if($usia_pasien_real >= $RowKelasUsia['umur_min']){
                    $klasifikasi_usia  = $RowKelasUsia['umur_kategori'];
                     $id_referensi_usia = $RowKelasUsia['id_referensi_usia'];
                }
            }else{
                if($usia_pasien_real >= $RowKelasUsia['umur_min'] && $usia_pasien_real <= $RowKelasUsia['umur_max']){
                    $klasifikasi_usia  = $RowKelasUsia['umur_kategori'];
                     $id_referensi_usia = $RowKelasUsia['id_referensi_usia'];
                }
            }
        }
    }

?>
    <input type="hidden" name="id_laboratorium" value="<?php echo $id_laboratorium; ?>">
    <input type="hidden" name="id_laboratorium_rincian" value="<?php echo $id_laboratorium_rincian; ?>">
    <input type="hidden" name="id_referensi_pemeriksaan" value="<?php echo $id_referensi_pemeriksaan; ?>">
    <input type="hidden" name="id_referensi_usia" value="<?php echo $id_referensi_usia; ?>">
    <input type="hidden" name="gender" value="<?php echo $gender; ?>">
    <input type="hidden" name="usia" value="<?php echo $usia_pasien; ?>">
    <input type="hidden" name="satuan_usia" value="<?php echo $SatuanUsia; ?>">
    <input type="hidden" name="result_type" value="<?php echo $result_type; ?>">
    <input type="hidden" name="result_interpertation_type" value="<?php echo $result_interpertation_type; ?>">
    <input type="hidden" name="allow_age" value="<?php echo $allow_age; ?>">
    <input type="hidden" name="allow_sex" value="<?php echo $allow_sex; ?>">
    
    <div class="row mb-2">
        <div class="col-12">
            <small>
                <b>A. Informasi Pasien</b>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>No.RM</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $id_pasien; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Nama Pasien</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $nama; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Gender</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $gender; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Tanggal Lahir</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo date('d/m/Y', strtotime($tanggal_lahir)); ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Usia (Saat Pemeriksaan)</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $usia_pasien; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Kategori Usia</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $klasifikasi_usia; ?></small></div>
    </div>
    <div class="row mb-2 mt-3">
        <div class="col-12">
            <small>
                <b>B. Informasi Pemeriksaan</b>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Kategori</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $category_pemeriksaan; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Nama Pemeriksaan</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $nama_pemeriksaan; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>Display</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><i><?php echo $display_pemeriksaan; ?></i></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>Code</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><i><?php echo $code_pemeriksaan; ?></i></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small><i>System</i></small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><i><?php echo $system_pemeriksaan; ?></i></small></div>
    </div>
    <div class="row mb-2 mt-3">
        <div class="col-12">
            <small>
                <b>C. Tipe Interpertasi</b>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Tipe Data Hasil</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $result_type; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Metode Interpertasi</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $result_interpertation_type; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Interpertasi Usia</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $allow_age; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Interpertasi Gender</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $allow_sex; ?></small></div>
    </div>
    <div class="row mb-2 mt-3">
        <div class="col-12">
            <small>
                <b>D. Form Hasil</b>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="hasil_pemeriksaan"><small>Hasil Pemeriksaan</small></label>
        </div>
        <div class="col-md-1"></div>
       <?php 
            // Menampilkan Form Hasil Berdasarkan Tipe Data
            if($result_type=="Numeric"){
                echo '
                    <div class="col-md-7">
                        <input type="number" min="0" step="1" name="hasil_pemeriksaan" id="hasil_pemeriksaan" class="form-control" placeholder="00">
                    </div>
                ';
            }
            if($result_type=="Decimal"){
                echo '
                    <div class="col-md-7">
                        <input type="number" min="0" step="0.001" name="hasil_pemeriksaan" id="hasil_pemeriksaan" class="form-control" placeholder="0.00">
                    </div>
                ';
            }
            if($result_type=="Text"){
                echo '
                    <div class="col-md-7">
                        <textarea name="hasil_pemeriksaan" id="hasil_pemeriksaan" class="form-control"></textarea>
                    </div>
                ';
            }
            if($result_type=="Boolean"){
                echo '
                    <div class="col-md-7">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hasil_pemeriksaan" id="hasil_pemeriksaan1" value="1" checked="">
                            <label class="form-check-label" for="hasil_pemeriksaan1">
                                <small>Ya</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hasil_pemeriksaan" id="hasil_pemeriksaan2" value="1" checked="">
                            <label class="form-check-label" for="hasil_pemeriksaan2">
                                <small>Tidak</small>
                            </label>
                        </div>
                    </div>
                ';
            }
            if($result_type=="Coded"){
                
                // Menampilkan Corm Radio Button dari referensi_category
                $table_row_category = '';
                $query_list = mysqli_query($Conn, "SELECT * FROM referensi_category WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
                while ($row = mysqli_fetch_assoc($query_list)) {
                    $id_referensi_category = htmlspecialchars($row['id_referensi_category']);
                    $nilai_hasil           = htmlspecialchars($row['nilai_hasil']);
                    $label                 = htmlspecialchars($row['label']);
                    $normal_value          = $row['normal_value'];
                    
                    
                    // Default: tampilkan baris
                    $is_show = true;

                    // Jika referensi menggunakan filter usia, lakukan matching berdasarkan unit usia
                    if($allow_age==1){

                        // Jika Berkaitan Dengan Jenis Kelamin
                        if($allow_sex==1){
                            $jenis_kelamin = $row['jenis_kelamin'];
                            if($jenis_kelamin!==$gender){
                                $is_show = false;
                            }
                        }else{
                            $is_show = false;
                            $umur_unit = strtolower(trim((string)$row['umur_unit']));
                            $umur_min  = $row['umur_min'];
                            $umur_max  = $row['umur_max'];

                            if($umur_unit === 'tahun'){
                                $usia_dibandingkan = $usia_tahun;
                            } elseif($umur_unit === 'bulan'){
                                $usia_dibandingkan = $usia_bulan;
                            } elseif($umur_unit === 'hari'){
                                $usia_dibandingkan = $usia_hari;
                            } else {
                                $usia_dibandingkan = $usia_tahun;
                            }

                            if($usia_dibandingkan !== null){
                                $is_show = true;
                                $has_min = ($umur_min !== null && $umur_min !== '' && is_numeric($umur_min));
                                $has_max = ($umur_max !== null && $umur_max !== '' && is_numeric($umur_max));

                                // Konvensi: umur_max = 0 berarti tidak ada batas atas (dewasa/open ended)
                                if($has_max && (float)$umur_max == 0){
                                    $has_max = false;
                                }

                                if($has_min && $usia_dibandingkan < (float)$umur_min){
                                    $is_show = false;
                                }
                                if($has_max && $usia_dibandingkan > (float)$umur_max){
                                    $is_show = false;
                                }
                            }
                        }
                    }else{
                        if($allow_sex==1){
                            $jenis_kelamin = $row['jenis_kelamin'];
                            if($jenis_kelamin==$gender){
                                $is_show = true;
                            }else{
                                $is_show = false;
                            }
                        }
                    }


                    if($is_show){
                        $table_row_category .= '
                            <tr>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input coded_input" name="hasil_pemeriksaan" id="hasil_pemeriksaan_'.$id_referensi_category.'" value="'.$id_referensi_category.'">
                                </td>
                                <td><small>'.$nilai_hasil.'</small></td>
                                <td><small>'.$label.'</small></td>
                            </tr>
                        ';
                    }
                }

                if(empty($table_row_category)){
                    $table_row_category = '
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                <small>Tidak ada pilihan hasil yang sesuai usia pasien.</small>
                            </td>
                        </tr>
                    ';
                }
                echo '
                    <div class="col-md-12">
                        <div class="table table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-center">
                                            <b><i class="bi bi-check-circle"></i></b>
                                        </td>
                                        <td class="text-left"><b>Nilai/Hasil</b></td>
                                        <td class="text-left"><b>Label</b></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$table_row_category.'
                                </tbody>
                            </table>
                        </div>
                    </div>
                ';
            }
        ?>
    </div>
     <div class="row mb-2 mt-3">
        <div class="col-12">
            <small>
                <b>E. Metode Pemeriksaan</b>
            </small>
        </div>
    </div>
    <?php
        // Buka Data hasil Mapping
        $nama_metode_pemeriksaan    = "";
        $display_metode_pemeriksaan = "";
        $code_metode_pemeriksaan    = "";
        $system_metode_pemeriksaan  = "";

        $QryRef = $Conn->prepare("SELECT id_referensi_metode_pemeriksaan FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan = ?");
        $QryRef->bind_param("i", $id_referensi_pemeriksaan);
        if (!$QryRef->execute()) {
            $ErrorRef=$Conn->error;
            echo '
                <div class="alert alert-danger text-center">
                    <small>Terjadi kesalahan pada saat membuka data dari tabel laboratorium!<br>Keterangan : '.$ErrorRef.'</small>
                </div>
            ';
        }
        $ResultRef = $QryRef->get_result();
        $DataRef = $ResultRef->fetch_assoc();
        $QryRef->close();
        if (!empty($DataRef['id_referensi_metode_pemeriksaan'])) {
            $id_referensi_metode_pemeriksaan = $DataRef['id_referensi_metode_pemeriksaan'];
            $nama_metode_pemeriksaan    = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'nama_metode_pemeriksaan');
            $display_metode_pemeriksaan = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'display_metode_pemeriksaan');
            $code_metode_pemeriksaan    = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'code_metode_pemeriksaan');
            $system_metode_pemeriksaan  = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'system_metode_pemeriksaan');
        }else{
            $id_referensi_metode_pemeriksaan = "";
            $nama_metode_pemeriksaan = "";
        }
    ?>
    <div class="row mb-2">
        <div class="col-5"><small>Pilih Metode</small></div>
        <div class="col-7">
            <select name="id_referensi_metode_pemeriksaan" id="id_referensi_metode_pemeriksaan" class="form-control">
                <option selected value="<?php echo $id_referensi_metode_pemeriksaan; ?>"><?php echo $nama_metode_pemeriksaan; ?></option>
            </select>
        </div>
    </div>
    
