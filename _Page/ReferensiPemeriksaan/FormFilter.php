<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // Kategori
        if($keyword_by=="category_pemeriksaan"){
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT category_pemeriksaan FROM referensi_pemeriksaan ORDER BY category_pemeriksaan ASC");
            while ($data = mysqli_fetch_array($query)) {
                $category_pemeriksaan= $data['category_pemeriksaan'];
                echo '<option value="'.$category_pemeriksaan.'">'.$category_pemeriksaan.'</option>';
            }
            echo '</select>';
        }else{

            // System
            if($keyword_by=="system_pemeriksaan"){
                echo '<select name="keyword" id="keyword" class="form-control">';
                echo '  <option value="">Pilih</option>';
                $query = mysqli_query($Conn, "SELECT DISTINCT system_pemeriksaan FROM referensi_pemeriksaan ORDER BY system_pemeriksaan ASC");
                while ($data = mysqli_fetch_array($query)) {
                    $system_pemeriksaan= $data['system_pemeriksaan'];
                    echo '<option value="'.$system_pemeriksaan.'">'.$system_pemeriksaan.'</option>';
                }
                echo '</select>';
            }else{
                
                // System
                if($keyword_by=="result_type"){
                    echo '<select name="keyword" id="keyword" class="form-control">';
                    echo '  <option value="">Pilih</option>';
                    $query = mysqli_query($Conn, "SELECT DISTINCT result_type FROM referensi_pemeriksaan ORDER BY result_type ASC");
                    while ($data = mysqli_fetch_array($query)) {
                        $result_type= $data['result_type'];
                        echo '<option value="'.$result_type.'">'.$result_type.'</option>';
                    }
                    echo '</select>';
                }else{

                    if($keyword_by=="result_interpertation_type"){
                        echo '<select name="keyword" id="keyword" class="form-control">';
                        echo '  <option value="">Pilih</option>';
                        $query = mysqli_query($Conn, "SELECT DISTINCT result_interpertation_type FROM referensi_pemeriksaan ORDER BY result_interpertation_type ASC");
                        while ($data = mysqli_fetch_array($query)) {
                            $result_interpertation_type= $data['result_interpertation_type'];
                            echo '<option value="'.$result_interpertation_type.'">'.$result_interpertation_type.'</option>';
                        }
                        echo '</select>';
                    }else{
                        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
                    }
                }
            }
        }
    }
?>