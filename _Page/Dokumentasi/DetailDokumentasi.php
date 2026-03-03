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

    //id_dokumentasi wajib terisi
    if(empty($_POST['id'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_dokumentasi' dan sanitasi
    $id_dokumentasi = validateAndSanitizeInput($_POST['id']);

    //Buka Detail 'dokumentasi' Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM dokumentasi WHERE id_dokumentasi = ?");
    $Qry->bind_param("i", $id_dokumentasi);
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

    if (empty($Data)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $dokumentasi_title       = $Data['dokumentasi_title'];
    $dokumentasi_category    = $Data['dokumentasi_category'];
    $dokumentasi_description = $Data['dokumentasi_description'];
    $dokumentasi_datetime    = $Data['dokumentasi_datetime'];
    $dokumentasi_author      = $Data['dokumentasi_author'];
    $publish                 = $Data['publish'];
    
    // Label Publish
    if($publish==1){
        $label_publish = '
            <a href="javascript:void(0);" class="p-1 bg-success-light rounded-2 border-1 border-success modal_update_status" data-id="'.$id_dokumentasi.'">
                <i class="bi bi-send"></i> Publish
            </a>
        ';
    }else{
        $label_publish = '
            <a href="javascript:void(0);" class="p-1 bg-danger-subtle rounded-2 border-1 text-danger modal_update_status" data-id="'.$id_dokumentasi.'">
                <i class="bi bi-file-text"></i> Draft
            </a>
        ';
    }
?>
<div class="row mb-3">
    <div class="col-12 text-end">
        <button type="button" class="btn btn-floating btn-dark kembali_ke_data" title="Kembali">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-floating btn-outline-info reload_detail" data-id="<?php echo $id_dokumentasi; ?>" title="Reload Detail Dokumentasi">
            <i class="bi bi-repeat"></i>
        </button>
        <button type="button" class="btn btn-md btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
            <li class="dropdown-header text-start">
                <h6>Option</h6>
            </li>
            <li>
                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="">
                    <i class="bi bi-plus"></i> Tambah Konten
                </a>
            </li>
            <li>
                <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </li>
            <li>
                <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>">
                    <i class="bi bi-x"></i> Hapus
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <b><?php echo $dokumentasi_title; ?></b><br>
                    </div>
                    <div class="col-4 text-end">
                        <small class="text text-grayish">
                            <i class="bi bi-tags"></i> <?php echo $dokumentasi_category; ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3 border-1 border-bottom">
                    <div class="col-8 mb-3">
                        <i class="text text-grayish"><?php echo $dokumentasi_description; ?></i>
                    </div>
                    <div class="col-4 mb-3 text-end">
                        <button type="button" class="btn btn-md btn-secondary btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-plus"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                            <li class="dropdown-header text-start">
                                <h6>Tambah Konten</h6>
                            </li>
                            <li>
                                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="" data-type_content="text">
                                    <i class="bi bi-justify"></i> Text
                                </a>
                                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="" data-type_content="list">
                                    <i class="bi bi-list-nested"></i> List
                                </a>
                                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="" data-type_content="image">
                                    <i class="bi bi-image"></i> Image
                                </a>
                                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="" data-type_content="video">
                                    <i class="bi bi-camera-video"></i> Video
                                </a>
                                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="" data-type_content="image_link">
                                    <i class="bi bi-link"></i> Image Link
                                </a>
                                <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="<?php echo $id_dokumentasi; ?>" data-order="down" data-order_by="" data-type_content="video_link">
                                    <i class="bi bi-link"></i> Video Link
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 mb-3">
                        <?php
                            $jumlah_rincian = mysqli_num_rows(mysqli_query($Conn, "SELECT id_dokumentasi_content FROM dokumentasi_content WHERE id_dokumentasi='$id_dokumentasi'"));
                            if(empty($jumlah_rincian)){
                                echo '
                                    <div class="alert alert-danger text-center">Tidak Ada Konten Yang Ditemukan Pada Dokumentasi Ini</div>
                                ';
                            }else{
                                // Looping Dokumentasi
                                $query = mysqli_query($Conn, "SELECT*FROM dokumentasi_content WHERE id_dokumentasi='$id_dokumentasi' ORDER BY order_content ASC");
                                while ($data = mysqli_fetch_array($query)) {
                                    $id_dokumentasi_content = $data['id_dokumentasi_content'];
                                    $order_content          = $data['order_content'];
                                    $type_content           = $data['type_content'];
                                    $file_type              = $data['file_type'];
                                    $value_content          = $data['value_content'];

                                    // Menampilkan value_content berdasarkan tipe
                                    if($type_content=="text" || $type_content=="list"){
                                        $value_content = $data['value_content'];
                                    }
                                    if($type_content=="image"){
                                        $value_content = '<img src="assets/Dokumentasi/image/'.$value_content.'" width="100%">';
                                    }
                                    if($type_content=="video"){
                                        $value_content = '
                                            <video width="100%" height="400px" controls>
                                                <source src="assets/Dokumentasi/video/'.$value_content.'" type="'.$file_type.'">
                                                Your browser does not support the video tag.
                                            </video>
                                        ';
                                    }
                                    echo '
                                        <div class="row mb-3">
                                            <div class="col-8">'.$value_content.'</div>
                                            <div class="col-4 text-end">
                                                <button type="button" class="btn btn-md btn-outline-secondary btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-chevron-up"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                    <li class="dropdown-header text-start">
                                                        <h6>Tambah Konten Di Atasnya</h6>
                                                    </li>
                                                     <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="up" data-order_by="'.$order_content.'" data-type_content="text">
                                                        <i class="bi bi-justify"></i> Text
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="up" data-order_by="'.$order_content.'" data-type_content="list">
                                                        <i class="bi bi-list-nested"></i> List
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="up" data-order_by="'.$order_content.'" data-type_content="image">
                                                        <i class="bi bi-image"></i> Image
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="up" data-order_by="'.$order_content.'" data-type_content="video">
                                                        <i class="bi bi-camera-video"></i> Video
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="up" data-order_by="'.$order_content.'" data-type_content="image_link">
                                                        <i class="bi bi-link"></i> Image Link
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="up" data-order_by="'.$order_content.'" data-type_content="video_link">
                                                        <i class="bi bi-link"></i> Video Link
                                                    </a>
                                                </ul>
                                                <button type="button" class="btn btn-md btn-outline-secondary btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                    <li class="dropdown-header text-start">
                                                        <h6>Tambah Konten Di Bawahnya</h6>
                                                    </li>
                                                     <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="down" data-order_by="'.$order_content.'" data-type_content="text">
                                                        <i class="bi bi-justify"></i> Text
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="down" data-order_by="'.$order_content.'" data-type_content="list">
                                                        <i class="bi bi-list-nested"></i> List
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="down" data-order_by="'.$order_content.'" data-type_content="image">
                                                        <i class="bi bi-image"></i> Image
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="down" data-order_by="'.$order_content.'" data-type_content="video">
                                                        <i class="bi bi-camera-video"></i> Video
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="down" data-order_by="'.$order_content.'" data-type_content="image_link">
                                                        <i class="bi bi-link"></i> Image Link
                                                    </a>
                                                    <a class="dropdown-item modal_tambah_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi.'" data-order="down" data-order_by="'.$order_content.'" data-type_content="video_link">
                                                        <i class="bi bi-link"></i> Video Link
                                                    </a>
                                                </ul>
                                                <button type="button" class="btn btn-md btn-outline-secondary btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                                    <li class="dropdown-header text-start">
                                                        <h6>Option</h6>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item modal_edit_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi_content.'" data-id_dokumentasi="'.$id_dokumentasi.'">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item modal_hapus_konten" href="javascript:void(0)" data-id="'.$id_dokumentasi_content.'" data-id_dokumentasi="'.$id_dokumentasi.'">
                                                            <i class="bi bi-x"></i> Hapus
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    ';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-6">
                        <small>
                            <i><i class="bi bi-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($dokumentasi_datetime)); ?></i><br>
                            <?php echo "Write By : $dokumentasi_author"; ?>
                        </small>
                    </div>
                    <div class="col-6 text-end">
                        <small>
                            <?php echo "Status : $label_publish"; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>