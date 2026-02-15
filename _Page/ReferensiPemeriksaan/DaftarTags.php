<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

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

    if($keyword==""){
        echo '
            <a href="javascript:void(0);" class="PilihTags" data-id="">
                <small class="d-inline-flex mb-2 px-2 py-1 text-success-emphasis bg-success-subtle border border-success-subtle rounded-2">
                    <small>ALL</small>
                </small>
            </a>
        ';
    }else{
        echo '
            <a href="javascript:void(0);" class="PilihTags" data-id="">
                <small class="d-inline-flex mb-2 px-2 py-1 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-2">
                    <small>ALL</small>
                </small>
            </a>
        ';
    }
    
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT DISTINCT category_pemeriksaan FROM referensi_pemeriksaan"));
    if(!empty($jml_data)){
        $query = mysqli_query($Conn, "SELECT DISTINCT category_pemeriksaan FROM referensi_pemeriksaan  ORDER BY category_pemeriksaan ASC");
        while ($data = mysqli_fetch_array($query)) {
            $category_pemeriksaan   = $data['category_pemeriksaan'];
            $jumlah = mysqli_num_rows(mysqli_query($Conn, "SELECT id_referensi_pemeriksaan FROM referensi_pemeriksaan WHERE category_pemeriksaan='$category_pemeriksaan'"));

            // Tampilkan Tags
            if($keyword_by=='category_pemeriksaan' && $category_pemeriksaan==$keyword){
                echo '
                    <a href="javascript:void(0);" class="PilihTags" data-id="'.$category_pemeriksaan.'">
                        <small class="d-inline-flex mb-2 px-2 py-1 text-success-emphasis bg-success-subtle border border-success-subtle rounded-2">
                            <small><i class="bi bi-tag"></i> '.$category_pemeriksaan.' ('.$jumlah.')</small>
                        </small>
                    </a>
                ';
            }else{
                echo '
                    <a href="javascript:void(0);" class="PilihTags" data-id="'.$category_pemeriksaan.'">
                        <small class="d-inline-flex mb-2 px-2 py-1 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-2">
                            <small><i class="bi bi-tag"></i> '.$category_pemeriksaan.' ('.$jumlah.')</small>
                        </small>
                    </a>
                ';
            }
            
        }
    }
?>
