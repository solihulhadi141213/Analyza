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
    }else{
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
       
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_referensi_pemeriksaan" value="'.$id_referensi_pemeriksaan.'">
            <div class="row mb-2">
                <div class="col-4"><small>Nama Pemeriksaan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$nama_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kategori</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$category_pemeriksaan.'</small>
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
                <div class="col-4"><small><i>Display</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$display_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>System</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long">'.$system_pemeriksaan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Result Type</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$keterangan_result_type.'">
                        '.$result_type.'
                    </small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Interpertation</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish text-long underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$keterangan_result_interpertation_type.'">
                        '.$result_interpertation_type.'
                    </small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Allow By Age</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">'.$label_allow_age.'</div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small><i>Allow By Sex</i></small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">'.$label_allow_sex.'</div>
            </div>
        ';
    }
?>