<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // Kategori
        if($keyword_by=="system_satuan"){
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT system_satuan FROM referensi_satuan ORDER BY system_satuan ASC");
            while ($data = mysqli_fetch_array($query)) {
                $system_satuan= $data['system_satuan'];
                echo '<option value="'.$system_satuan.'">'.$system_satuan.'</option>';
            }
            echo '</select>';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>