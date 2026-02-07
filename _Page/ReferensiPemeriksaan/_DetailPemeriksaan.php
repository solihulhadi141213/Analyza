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
<div class="row mb-2">
    <div class="col-md-12 text-end">
        <button type="button" class="btn btn-md btn-dark btn-floating" id="kembali_ke_data" title="Kembali Ke Tabel Referensi Pemeriksaan">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-md btn-floating btn-outline-dark reload_detail" title="Reload Data">
            <i class="bi bi-repeat"></i>
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <b class="card-title">Detail Referensi Pemeriksaan</b>
                    </div>
                     <div class="col-2 text-end">
                        <button type="button" class="btn btn-md btn-floating btn-secondary modal_edit" data-id="<?php echo $id_referensi_pemeriksaan; ?>" title="Edit Referensi Pemeriksaan">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-5"><small>Nama Pemeriksaan</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6"><small><?php echo $nama_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small>Kategori</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6"><small><?php echo $category_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Code</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6"><small><?php echo $code_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Display</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6"><small><?php echo $display_pemeriksaan; ?></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>System</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6"><small><?php echo $system_pemeriksaan; ?></small></div>
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
                    <div class="col-6">
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
                    <div class="col-6">
                        <?php echo $label_allow_age; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-5"><small><i>Allow By Sex</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-6">
                        <?php echo $label_allow_sex; ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>