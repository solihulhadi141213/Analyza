<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // Kategori
        if($keyword_by=="system_metode_sample"){
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT system_metode_sample FROM referensi_metode_sample ORDER BY system_metode_sample ASC");
            while ($data = mysqli_fetch_array($query)) {
                $system_metode_sample= $data['system_metode_sample'];
                echo '<option value="'.$system_metode_sample.'">'.$system_metode_sample.'</option>';
            }
            echo '</select>';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>