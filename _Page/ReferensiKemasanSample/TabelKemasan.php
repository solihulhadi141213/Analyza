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
                <td colspan="8" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
    }else{
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
            $OrderBy="id_referensi_container ";
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
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_container  FROM  referensi_container"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_container  FROM  referensi_container WHERE nama_container like '%$keyword%' OR display_container like '%$keyword%' OR code_container like '%$keyword%' OR system_container like '%$keyword%'"));
            }
        }else{
            if(empty($keyword)){
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_container  FROM  referensi_container"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_container  FROM  referensi_container WHERE $keyword_by like '%$keyword%'"));
            }
        }
        
        //Mengatur Halaman
        $JmlHalaman = ceil($jml_data/$batas); 
        if(empty($jml_data)){
            echo '
                <tr>
                    <td colspan="8" class="text-center">
                        <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                    </td>
                </tr>
            ';
        }else{
            $no = 1+$posisi;
            //KONDISI PENGATURAN MASING FILTER
             if(empty($keyword_by)){
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT*FROM referensi_container ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT*FROM referensi_container WHERE nama_container like '%$keyword%' OR display_container like '%$keyword%' OR code_container like '%$keyword%' OR system_container like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }else{
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT*FROM referensi_container ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT*FROM referensi_container WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }
            while ($data = mysqli_fetch_array($query)) {
                $id_referensi_container = $data['id_referensi_container'];
                $nama_container         = $data['nama_container'];
                $display_container      = $data['display_container'];
                $code_container         = $data['code_container'];
                $system_container       = $data['system_container'];
                $kapasitas_container    = $data['kapasitas_container'];
                $unit_container         = $data['unit_container'];
                $code_unit_container    = $data['code_unit_container'];
                $system_unit_container  = $data['system_unit_container'];
               
                // Tampilkan Data
                echo '
                    <tr>
                        <td class="text-center"><small>'.$no.'</small></td>
                        <td>
                            <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_referensi_container.'">
                                <small>'.$nama_container.'</small>
                            </a>
                        </td>
                        <td><small><i>'.$display_container.'</i></small></td>
                        <td><small>'.$code_container.'</small></td>
                        <td>
                            <small>
                                <a href="'.$system_container.'" target="_blank" class="text text-decoration-underline">'.$system_container.'</a>
                            </small>
                        </td>
                        <td><small>'.$kapasitas_container.'</small></td>
                        <td>
                            <small class="text text-grayish underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$system_unit_container.'">
                            '.$unit_container.' ('.$code_unit_container.')
                            </small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                <li class="dropdown-header text-start">
                                    <h6>Option</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_referensi_container.'">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_referensi_container.'">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="'.$id_referensi_container.'">
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