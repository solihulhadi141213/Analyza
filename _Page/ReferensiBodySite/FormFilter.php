<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // Kategori
        if($keyword_by=="body_site_system"){
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT body_site_system FROM referensi_body_site ORDER BY body_site_system ASC");
            while ($data = mysqli_fetch_array($query)) {
                $body_site_system= $data['body_site_system'];
                echo '<option value="'.$body_site_system.'">'.$body_site_system.'</option>';
            }
            echo '</select>';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>