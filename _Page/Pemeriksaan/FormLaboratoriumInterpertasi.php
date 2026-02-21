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

    //Data Yang Wajib Diisi (Mandatory)
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Rincian Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['id_referensi_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Referensi Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['gender'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Informasi Gender pasien Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['result_type'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Tipe Hasil Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['result_interpertation_type'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Metode Interpertasi Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel Dengan Value Wajib
    $id_laboratorium            = $_POST['id_laboratorium'];
    $id_laboratorium_rincian    = $_POST['id_laboratorium_rincian'];
    $id_referensi_pemeriksaan   = $_POST['id_referensi_pemeriksaan'];
    $gender                     = $_POST['gender'];
    $result_type                = $_POST['result_type'];
    $result_interpertation_type = $_POST['result_interpertation_type'];

    // Buat Variabel Dengan Value Tidak Wajib
    if(empty($_POST['id_referensi_usia'])){
        $id_referensi_usia = "";
    }else{
        $id_referensi_usia = $_POST['id_referensi_usia'];
    }
    if(empty($_POST['usia'])){
        $usia = 0;
    }else{
        $usia = $_POST['usia'];
    }
    if(empty($_POST['satuan_usia'])){
        $satuan_usia = "";
    }else{
        $satuan_usia = $_POST['satuan_usia'];
    }
    if(empty($_POST['hasil_pemeriksaan'])){
        $hasil_pemeriksaan = "";
    }else{
        $hasil_pemeriksaan = $_POST['hasil_pemeriksaan'];
    }
    if(empty($_POST['allow_age'])){
        $allow_age = 0;
    }else{
        $allow_age = $_POST['allow_age'];
    }
    if(empty($_POST['allow_sex'])){
        $allow_sex = 0;
    }else{
        $allow_sex = $_POST['allow_sex'];
    }

    // Cek Jika Interpertasi Berkaitan Dengan Range Atau Category
    $id_referensi_category = NULL;
    $id_referensi_range    = NULL;
    $hasil                 = $hasil_pemeriksaan;
    $hasil_interpertasi    = "";
    $hasil_conclusion      = "";
    $id_referensi_usia     = "";
    $umur_kategori         = "";

    // Apabila Pemeriksaan Berbasis Usia (Cari id_referensi_usia dari tabel referensi_usia)
    if($allow_age==1){
        $query_referensi_usia = mysqli_query($Conn, "SELECT * FROM referensi_usia WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
        while ($RowReferensiUsia = mysqli_fetch_assoc($query_referensi_usia)) {
            if($RowReferensiUsia['umur_unit']==$satuan_usia){
                if(empty($RowReferensiUsia['umur_max'])){
                    if($usia>=$RowReferensiUsia['umur_min']){
                        $id_referensi_usia = $RowReferensiUsia['id_referensi_usia'];
                        $umur_kategori     = $RowReferensiUsia['umur_kategori'];
                    }
                }else{
                    if($usia>=$RowReferensiUsia['umur_min'] && $usia<=$RowReferensiUsia['umur_max']){
                        $id_referensi_usia = $RowReferensiUsia['id_referensi_usia'];
                        $umur_kategori     = $RowReferensiUsia['umur_kategori'];
                    }
                }
            }
        }
    }

    //Jika 'result_type' adalah 'Coded'
    // Untuk result_type = Coded maka result_interpertation_type adalah Category
    if($result_type=="Coded"){
        $id_referensi_range    = NULL;
        // Buka Data Category Berdasarkan 'hasil_pemeriksaan'
        // Buka Referensi Pemeriksaan
        $QryCodedInterpertation = $Conn->prepare("SELECT * FROM referensi_category WHERE id_referensi_category = ?");
        $QryCodedInterpertation->bind_param("i", $hasil_pemeriksaan);
        if (!$QryCodedInterpertation->execute()) {
            $ErrorCodedInterpertation=$Conn->error;
            echo '
                <div class="alert alert-danger text-center">
                    <small>Terjadi kesalahan pada saat membuka data dari tabel <i>referensi_category</i>!<br>Keterangan : '.$ErrorCodedInterpertation.'</small>
                </div>
            ';
            exit;
        }
        $ResultCodedInterpertation = $QryCodedInterpertation->get_result();
        $DataCodedInterpertation = $ResultCodedInterpertation->fetch_assoc();
        $QryCodedInterpertation->close();
        if (empty($DataCodedInterpertation)) {
            echo '
                <div class="alert alert-danger text-center">
                    <small>Hasil yang anda pilih tidak cocok dengan referensi Category manapun!</small>
                </div>
            ';
            exit;
        }
        $id_referensi_category = $DataCodedInterpertation['id_referensi_category'];
        $hasil                 = $DataCodedInterpertation['nilai_hasil'];
        $hasil_interpertasi    = $DataCodedInterpertation['label'];
        if(!empty($DataCodedInterpertation['normal_value'])){
            $hasil_conclusion = "Normal";
        }
    }
    
    //Jika 'result_type' adalah 'Numeric' Dan 'Decimal'
    // Untuk result_type = Numeric || Decimal maka result_interpertation_type adalah Range atau None
    if($result_type=='Numeric' || $result_type=='Decimal'){
        if($result_interpertation_type=='None'){
            $id_referensi_category = NULL;
            $id_referensi_range    = NULL;
            $hasil                 = $hasil_pemeriksaan;
            $hasil_interpertasi    = "";
            $hasil_conclusion      = "";
        }else{  
            // Maka Asumsinya Bahwa Pemeriksaan Diinterpertasikan Berdasarkan Range
            $query_range_list = mysqli_query($Conn, "SELECT * FROM referensi_range WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan'");
            while ($RowRangeList = mysqli_fetch_assoc($query_range_list)) {
                $id_referensi_usia = $RowRangeList['id_referensi_usia'];
                $nilai_min         = $RowRangeList['nilai_min'];
                $nilai_max         = $RowRangeList['nilai_max'];
                $operator          = $RowRangeList['operator'];
                $label             = $RowRangeList['label'];
                $conclusion        = $RowRangeList['conclusion'];
                $normal_value      = $RowRangeList['normal_value'];

                // Apabila Pemeriksaan Tidak Berkaitan Dengan Usia dan Jenis Kelamin
                if($allow_age==0 && $allow_sex==0){
                    if($operator=="Between"){
                        if($hasil_pemeriksaan>=$nilai_min && $hasil_pemeriksaan<=$nilai_max){
                            $id_referensi_category = NULL;
                            $id_referensi_range = $RowRangeList['id_referensi_range'];
                            $hasil                 = $hasil_pemeriksaan;
                            $hasil_interpertasi    = $label;
                            $hasil_conclusion      = $conclusion;
                        }
                    }
                    if($operator=="More"){
                        if($hasil_pemeriksaan>=$nilai_min){
                            $id_referensi_category = NULL;
                            $id_referensi_range    = $RowRangeList['id_referensi_range'];
                            $hasil                 = $hasil_pemeriksaan;
                            $hasil_interpertasi    = $label;
                            $hasil_conclusion      = $conclusion;
                        }
                    }
                }
                // Apabila Pemeriksaan Berkaitan Dengan Usia dan Jenis Kelamin
                if($allow_age==1 && $allow_sex==1){
                    if($RowRangeList['jenis_kelamin']==$gender){
                        if($RowRangeList['id_referensi_usia']==$id_referensi_usia){
                            if($operator=="Between"){
                                if($hasil_pemeriksaan>=$nilai_min && $hasil_pemeriksaan<=$nilai_max){
                                    $id_referensi_category = NULL;
                                    $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                    $hasil                 = $hasil_pemeriksaan;
                                    $hasil_interpertasi    = $label;
                                    $hasil_conclusion      = $conclusion;
                                }
                            }
                            if($operator=="More"){
                                if($hasil_pemeriksaan>=$nilai_min){
                                    $id_referensi_category = NULL;
                                    $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                    $hasil                 = $hasil_pemeriksaan;
                                    $hasil_interpertasi    = $label;
                                    $hasil_conclusion      = $conclusion;
                                }
                            }
                        }
                    }
                    
                }
                // Apabila Pemeriksaan Berkaitan Dengan Usia Saja
                if($allow_age==1 && $allow_sex==0){
                    if($RowRangeList['id_referensi_usia']==$id_referensi_usia){
                        if($operator=="Between"){
                            if($hasil_pemeriksaan>=$nilai_min && $hasil_pemeriksaan<=$nilai_max){
                                $id_referensi_category = NULL;
                                $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                $hasil                 = $hasil_pemeriksaan;
                                $hasil_interpertasi    = $label;
                                $hasil_conclusion      = $conclusion;
                            }
                        }
                        if($operator=="More"){
                            if($hasil_pemeriksaan>=$nilai_min){
                                $id_referensi_category = NULL;
                                $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                $hasil                 = $hasil_pemeriksaan;
                                $hasil_interpertasi    = $label;
                                $hasil_conclusion      = $conclusion;
                            }
                        }
                    }
                    
                }
                // Apabila Pemeriksaan Berkaitan Dengan Usia Saja
                if($allow_age==1 && $allow_sex==0){
                    if($RowRangeList['id_referensi_usia']==$id_referensi_usia){
                        if($operator=="Between"){
                            if($hasil_pemeriksaan>=$nilai_min && $hasil_pemeriksaan<=$nilai_max){
                                $id_referensi_category = NULL;
                                $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                $hasil                 = $hasil_pemeriksaan;
                                $hasil_interpertasi    = $label;
                                $hasil_conclusion      = $conclusion;
                            }
                        }
                        if($operator=="More"){
                            if($hasil_pemeriksaan>=$nilai_min){
                                $id_referensi_category = NULL;
                                $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                $hasil                 = $hasil_pemeriksaan;
                                $hasil_interpertasi    = $label;
                                $hasil_conclusion      = $conclusion;
                            }
                        }
                    }
                    
                }
                // Apabila Pemeriksaan Berkaitan Dengan Jenis Kelamin Saja
                if($allow_age==0 && $allow_sex==1){
                    if($RowRangeList['jenis_kelamin']==$gender){
                        if($operator=="Between"){
                            if($hasil_pemeriksaan>=$nilai_min && $hasil_pemeriksaan<=$nilai_max){
                                $id_referensi_category = NULL;
                                $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                $hasil                 = $hasil_pemeriksaan;
                                $hasil_interpertasi    = $label;
                                $hasil_conclusion      = $conclusion;
                            }
                        }
                        if($operator=="More"){
                            if($hasil_pemeriksaan>=$nilai_min){
                                $id_referensi_category = NULL;
                                $id_referensi_range    = $RowRangeList['id_referensi_range'];
                                $hasil                 = $hasil_pemeriksaan;
                                $hasil_interpertasi    = $label;
                                $hasil_conclusion      = $conclusion;
                            }
                        }
                    }
                    
                }

            }
        }
    }
    if($result_type=='Coded'){
        if($result_interpertation_type=='None'){
            $id_referensi_category = NULL;
            $id_referensi_range    = NULL;
            $hasil                 = $hasil_pemeriksaan;
            $hasil_interpertasi    = "";
            $hasil_conclusion      = "";
        }else{  
            // Menampilkan Semua List Referensi Category
            $query_category_list = mysqli_query($Conn, "SELECT * FROM referensi_category WHERE id_referensi_pemeriksaan='$id_referensi_pemeriksaan' AND id_referensi_category='$hasil_pemeriksaan'");
            while ($RowCategoryList = mysqli_fetch_assoc($query_category_list)) {
                $id_referensi_category = $RowCategoryList['id_referensi_category'];
                $hasil                 = $RowCategoryList['nilai_hasil'];
                $hasil_interpertasi    = $RowCategoryList['label'];
                $hasil_conclusion      = $RowCategoryList['fhir_display'];
            }
        }
    }
   
    // Menangkap Metode Pemeriksaan
    if(empty($_POST['id_referensi_metode_pemeriksaan'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Metode pemeriksaan yang digunakan tidak boleh kosong!!</small>
            </div>
        ';
        exit;
    }
    $id_referensi_metode_pemeriksaan = $_POST['id_referensi_metode_pemeriksaan'];

    // Membuka Metode pemeriksaan
    $nama_metode_pemeriksaan    = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'nama_metode_pemeriksaan');
    $display_metode_pemeriksaan = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'display_metode_pemeriksaan');
    $code_metode_pemeriksaan    = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'code_metode_pemeriksaan');
    $system_metode_pemeriksaan  = GetDetailData($Conn, 'referensi_metode_pemeriksaan', 'id_referensi_metode_pemeriksaan', $id_referensi_metode_pemeriksaan, 'system_metode_pemeriksaan');
?>
    <!-- Untuk Mengirim FK -->
    <input type="hidden" name="id_laboratorium_rincian" value="<?php echo $id_laboratorium_rincian; ?>">
    <input type="hidden" name="id_referensi_category" value="<?php echo $id_referensi_category; ?>">
    <input type="hidden" name="id_referensi_range" value="<?php echo $id_referensi_range; ?>">

    <!-- Untuk Mengirim Metode Pemeriksaan -->
     <input type="hidden" name="id_referensi_metode_pemeriksaan" value="<?php echo $id_referensi_metode_pemeriksaan; ?>">
     <input type="hidden" name="metode_pemeriksaan" value="<?php echo $nama_metode_pemeriksaan; ?>">
     <input type="hidden" name="metode_pemeriksaan_display" value="<?php echo $display_metode_pemeriksaan; ?>">
     <input type="hidden" name="metode_pemeriksaan_code" value="<?php echo $code_metode_pemeriksaan; ?>">
     <input type="hidden" name="metode_pemeriksaan_system" value="<?php echo $system_metode_pemeriksaan; ?>">

    <!-- Tampilkan Informasi Dasar -->
    <div class="row mb-2">
        <div class="col-4"><small>Tipe Data Hasil</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $result_type; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Klasifikasi Usia</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7"><small class="text text-grayish"><?php echo $umur_kategori; ?></small></div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12">
            <label for="hasil"><small>Hasil Pemeriksaan</small></label>
        </div>
        <div class="col-md-12">
            <input type="text" name="hasil" id="hasil" class="form-control" value="<?php echo $hasil; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="hasil_interpertasi"><small>Interpertasi</small></label>
        </div>
        <div class="col-md-12">
            <input type="text" name="hasil_interpertasi" id="hasil_interpertasi" class="form-control" value="<?php echo $hasil_interpertasi; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="hasil_conclusion"><small>Kesimpulan</small></label>
        </div>
        <div class="col-md-12">
            <input type="text" name="hasil_conclusion" id="hasil_conclusion" class="form-control" value="<?php echo $hasil_conclusion; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="hasil_keterangan"><small>Keterangan Lain</small></label>
        </div>
        <div class="col-md-12">
            <textarea name="hasil_keterangan" id="hasil_keterangan" class="form-control"></textarea>
        </div>
    </div>
    
    
    
    
