<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    $JmlHalaman = 0;
    $page       = 0;
    
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
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
        $OrderBy="id_referensi_pemeriksaan";
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
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_pemeriksaan FROM referensi_pemeriksaan  "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_pemeriksaan FROM referensi_pemeriksaan WHERE nama_pemeriksaan like '%$keyword%' OR category_pemeriksaan like '%$keyword%' OR code_pemeriksaan like '%$keyword%' OR display_pemeriksaan like '%$keyword%' OR system_pemeriksaan like '%$keyword%' OR result_type like '%$keyword%'"));
        }
    }else{
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_pemeriksaan FROM referensi_pemeriksaan  "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_pemeriksaan FROM referensi_pemeriksaan  WHERE $keyword_by like '%$keyword%'"));
        }
    }
    //Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("Page : 0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }
    $no = 1+$posisi;
    //KONDISI PENGATURAN MASING FILTER
    if(empty($keyword_by)){
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT*FROM referensi_pemeriksaan  ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM referensi_pemeriksaan  WHERE nama_pemeriksaan like '%$keyword%' OR category_pemeriksaan like '%$keyword%' OR code_pemeriksaan like '%$keyword%' OR display_pemeriksaan like '%$keyword%' OR system_pemeriksaan like '%$keyword%' OR result_type like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT*FROM referensi_pemeriksaan  ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM referensi_pemeriksaan  WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_referensi_pemeriksaan   = $data['id_referensi_pemeriksaan'];
        $nama_pemeriksaan           = $data['nama_pemeriksaan'];
        $code_pemeriksaan           = $data['code_pemeriksaan'];
        $display_pemeriksaan        = $data['display_pemeriksaan'];
        $system_pemeriksaan         = $data['system_pemeriksaan'];
        $category_pemeriksaan       = $data['category_pemeriksaan'];
        $result_type                = $data['result_type'];
        $result_interpertation_type = $data['result_interpertation_type'];
        $allow_age                  = $data['allow_age'];
        $allow_sex                  = $data['allow_sex'];

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
        
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_referensi_pemeriksaan  .'">
                        <small>'.$nama_pemeriksaan.'</small>
                    </a>
                </td>
                <td><small>'.$category_pemeriksaan.'</small></td>
                <td><small><i>'.$display_pemeriksaan.'</i></small></td>
                <td>
                    <small class="text text-grayish underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$system_pemeriksaan.'">
                        '.$code_pemeriksaan.'
                    </small>
                </td>
                <td>
                    <small class="text text-grayish underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$keterangan_result_type.'">
                        '.$result_type.'
                    </small>
                </td>
                <td>
                    <small class="text text-grayish underscore_doted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="'.$keterangan_result_interpertation_type.'">
                        '.$result_interpertation_type.'
                    </small>
                </td>
                <td><small>'.$label_allow_age.'</small></td>
                <td><small>'.$label_allow_sex.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_referensi_pemeriksaan  .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_referensi_pemeriksaan  .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete" href="javascript:void(0)" data-id="'.$id_referensi_pemeriksaan  .'">
                                <i class="bi bi-x"></i> Hapus
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        ';
        $no++;
    }
?>
<script>
    //Creat Javascript Variabel
    var page_count  = <?php echo $JmlHalaman; ?>;
    var curent_page = <?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#page_info').html('Page : '+curent_page+' / '+page_count+'');
    
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