<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // Kategori
        if($keyword_by=="system_spesimen"){
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT system_spesimen FROM referensi_jenis_spesimen ORDER BY system_spesimen ASC");
            while ($data = mysqli_fetch_array($query)) {
                $system_spesimen= $data['system_spesimen'];
                echo '<option value="'.$system_spesimen.'">'.$system_spesimen.'</option>';
            }
            echo '</select>';
        }else{
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>