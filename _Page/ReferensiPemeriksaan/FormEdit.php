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
    $result_type                = $Data['result_type'];
    $result_interpertation_type = $Data['result_interpertation_type'];
    $allow_age                  = $Data['allow_age'];
    $allow_sex                  = $Data['allow_sex'];
    
    // Routing Allow Age
    if($allow_age==1){
        $label_allow_age = 'checked';
    }else{
        $label_allow_age = '';
    }

    // Routing Allow Sex
    if($allow_sex==1){
        $label_allow_sex = 'checked';
    }else{
        $label_allow_sex = '';
    }
    
    //Tampilkan Data
    echo '
        <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_pemeriksaan_edit">
                    <small>Nama Pemeriksaan</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" name="nama_pemeriksaan" id="nama_pemeriksaan_edit" class="form-control" value="'.$nama_pemeriksaan.'">
                <small class="text text-grayish"><small>Nama pemeriksaan dalam bahasa indonesia</small></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="category_pemeriksaan_edit">
                    <small>Kategori Pemeriksaan</small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" name="category_pemeriksaan" id="category_pemeriksaan_edit" list="list_kategori" value="'.$category_pemeriksaan.'" required>
                <datalist class="list_kategori" id="list_kategori"></datalist>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="code_pemeriksaan_edit">
                    <small><i>Code</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" name="code_pemeriksaan" id="code_pemeriksaan_edit" value="'.$code_pemeriksaan.'" required>
                <small class="text text-grayish"><small>Kode Pemeriksaan Berdasarkan Referensi Yang Digunakan</small></small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="display_pemeriksaan_edit">
                    <small><i>Display</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" name="display_pemeriksaan" id="display_pemeriksaan_edit" value="'.$display_pemeriksaan.'" required>
                <small class="text text-grayish">Display Pemeriksaan Berdasarkan Referensi Yang Digunakan</small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="system_pemeriksaan_edit">
                    <small><i>System</i></small>
                </label>
            </div>
            <div class="col-md-8">
                <input type="url" class="form-control" name="system_pemeriksaan" id="system_pemeriksaan_edit" value="'.$system_pemeriksaan.'" required>
                <small class="text text-grayish">URL System Berdasarkan Referensi Yang Digunakan</small>
            </div>
        </div>
    ';
?>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="result_type_edit">
                <small><i>Result Type</i></small>
            </label>
        </div>
        <div class="col-md-8">
            <select name="result_type" id="result_type_edit" class="form-control" required>
                <option <?php if($result_type==""){echo "selected";} ?> value="">Pilih</option>
                <option <?php if($result_type=="Numeric"){echo "selected";} ?> value="Numeric">Numeric (Berbasis Angka Bilangan Bulat)</option>
                <option <?php if($result_type=="Decimal"){echo "selected";} ?> value="Decimal">Decimal (Berbasis Angka Desimal)</option>
                <option <?php if($result_type=="Coded"){echo "selected";} ?> value="Coded">Coded (Berbasis Kode / Referensi Hasil)</option>
                <option <?php if($result_type=="Text"){echo "selected";} ?> value="Text">Text (hasil pemeriksaan berbasis teks bebas)</option>
                <option <?php if($result_type=="Boolean"){echo "selected";} ?> value="Boolean">Boolean (Ya / Tidak)</option>
            </select>
            <small class="text text-grayish">Tipe Data Hasil Pemeriksaan</small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="result_interpertation_type_edit">
                <small><i>Interpertation Type</i></small>
            </label>
        </div>
        <div class="col-md-8">
            <select name="result_interpertation_type" id="result_interpertation_type_edit" class="form-control" required>
                <?php if($result_type=="Numeric"||$result_type=="Decimal"){ ?>
                    <option <?php if($result_interpertation_type==""){echo "selected";} ?> value="">Pilih</option>
                    <option <?php if($result_interpertation_type=="Range"){echo "selected";} ?> value="Range">Range (Hasil merujuk pada jarak nilai tertentu)</option>
                    <option <?php if($result_interpertation_type=="Category"){echo "selected";} ?> value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                    <option <?php if($result_interpertation_type=="None"){echo "selected";} ?> value="None">Interpertasi Tidak Digunakan</option>
                <?php }else{ ?>
                    <option <?php if($result_interpertation_type==""){echo "selected";} ?> value="">Pilih</option>
                    <option <?php if($result_interpertation_type=="Category"){echo "selected";} ?> value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                    <option <?php if($result_interpertation_type=="None"){echo "selected";} ?> value="None">Interpertasi Tidak Digunakan</option>
                <?php } ?>
            </select>
            <small class="text text-grayish">Tipe Interpertasi Hasil Pemeriksaan</small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="allow_age_sex">
                <small><i>Interpertation Group</i></small>
            </label>
        </div>
        <div class="col-md-8">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="allow_age_edit" name="allow_age" value="1" <?php echo $label_allow_age; ?> >
                <label class="form-check-label" for="allow_age_edit">
                    <small>Interpertasi hasil mempertimbangkan <b>Umur / Usia</b> pasien</small>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="allow_sex_edit" name="allow_sex" value="1" <?php echo $label_allow_sex; ?> >
                <label class="form-check-label" for="allow_sex_edit">
                    <small>Interpertasi hasil mempertimbangkan <b>Jenis Kelamin</b> pasien</small>
                </label>
            </div>
        </div>
    </div>