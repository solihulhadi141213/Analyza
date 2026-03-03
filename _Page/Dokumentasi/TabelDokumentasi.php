<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }
    //Keyword_by
    if(!empty($_POST['keyword_by'])){
        $keyword_by=$_POST['keyword_by'];
    }else{
        $keyword_by="";
    }
    //keyword
    if(!empty($_POST['keyword'])){
        $keyword=$_POST['keyword'];
    }else{
        $keyword="";
    }
    //batas
    if(!empty($_POST['batas'])){
        $batas=$_POST['batas'];
    }else{
        $batas="10";
    }
    //ShortBy
    if(!empty($_POST['ShortBy'])){
        $ShortBy=$_POST['ShortBy'];
    }else{
        $ShortBy="DESC";
    }
    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy=$_POST['OrderBy'];
    }else{
        $OrderBy="id_dokumentasi";
    }
    //Atur Page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
    }
    if(empty($keyword_by)){
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_dokumentasi FROM dokumentasi"));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_dokumentasi FROM dokumentasi WHERE dokumentasi_title like '%$keyword%' OR dokumentasi_category like '%$keyword%' OR dokumentasi_description like '%$keyword%' OR dokumentasi_author like '%$keyword%'"));
        }
    }else{
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_dokumentasi FROM dokumentasi"));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_dokumentasi FROM dokumentasi WHERE $keyword_by like '%$keyword%'"));
        }
    }
    
    //Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
    }else{
        $no = 1+$posisi;
        //KONDISI PENGATURAN MASING FILTER
            if(empty($keyword_by)){
            if(empty($keyword)){
                $query = mysqli_query($Conn, "SELECT*FROM dokumentasi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
            }else{
                $query = mysqli_query($Conn, "SELECT*FROM dokumentasi WHERE dokumentasi_title like '%$keyword%' OR dokumentasi_category like '%$keyword%' OR dokumentasi_description like '%$keyword%' OR dokumentasi_author like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
            }
        }else{
            if(empty($keyword)){
                $query = mysqli_query($Conn, "SELECT*FROM dokumentasi ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
            }else{
                $query = mysqli_query($Conn, "SELECT*FROM dokumentasi WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
            }
        }
        while ($data = mysqli_fetch_array($query)) {
            $id_dokumentasi          = $data['id_dokumentasi'];
            $dokumentasi_title       = $data['dokumentasi_title'];
            $dokumentasi_category    = $data['dokumentasi_category'];
            $dokumentasi_datetime    = $data['dokumentasi_datetime'];
            $dokumentasi_description = $data['dokumentasi_description'];
            $dokumentasi_author      = $data['dokumentasi_author'];
            $publish                 = $data['publish'];

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
            
            // Tampilkan Data
            echo '
                <tr>
                    <td class="text-center"><small>'.$no.'</small></td>
                    <td>
                        <a href="javascript:void(0);" class="text text-decoration-underline modal_detail" data-id="'.$id_dokumentasi.'">
                            <small>'.$dokumentasi_title.'</small>
                        </a>
                    </td>
                    <td><small><i>'.$dokumentasi_category.'</i></small></td>
                    <td><small><i>'.date('d/m/Y H:i', strtotime($dokumentasi_datetime)).'</i></small></td>
                    <td><small><i>'.$dokumentasi_author.'</i></small></td>
                    <td class="text-center"><small>'.$label_publish.'</small></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                            <li class="dropdown-header text-start">
                                <h6>Option</h6>
                            </li>
                            <li>
                                <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_dokumentasi.'">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="'.$id_dokumentasi.'">
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
?>
<script>
    //Creat Javascript Variabel
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#page_info').html(''+curent_page+' / '+page_count+'');
    
    //Set Pagging Button
    if(curent_page==1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count<=curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }
</script>