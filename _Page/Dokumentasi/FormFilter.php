<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // Kategori
        if($keyword_by=="dokumentasi_category"){
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT DISTINCT dokumentasi_category FROM dokumentasi ORDER BY dokumentasi_category ASC");
            while ($data = mysqli_fetch_array($query)) {
                $dokumentasi_category= $data['dokumentasi_category'];
                echo '<option value="'.$dokumentasi_category.'">'.$dokumentasi_category.'</option>';
            }
            echo '</select>';
        }else{
            
            // dokumentasi_author
            if($keyword_by=="dokumentasi_author"){
                echo '<select name="keyword" id="keyword" class="form-control">';
                echo '  <option value="">Pilih</option>';
                $query = mysqli_query($Conn, "SELECT DISTINCT dokumentasi_author FROM dokumentasi ORDER BY dokumentasi_author ASC");
                while ($data = mysqli_fetch_array($query)) {
                    $dokumentasi_author= $data['dokumentasi_author'];
                    echo '<option value="'.$dokumentasi_author.'">'.$dokumentasi_author.'</option>';
                }
                echo '</select>';
            }else{
                
                // publish
                if($keyword_by=="publish"){
                    echo '<select name="keyword" id="keyword" class="form-control">';
                    echo '  <option value="">Pilih</option>';
                    $query = mysqli_query($Conn, "SELECT DISTINCT publish FROM dokumentasi ORDER BY publish ASC");
                    while ($data = mysqli_fetch_array($query)) {
                        if(empty($data['publish'])){
                            echo '<option value="0">Draft</option>';
                        }else{
                            echo '<option value="1">Publish</option>';
                        }
                        
                    }
                    echo '</select>';
                }else{

                    // Datetime
                    if($keyword_by=="dokumentasi_datetime"){
                        echo '<input type="date" name="keyword" id="keyword" class="form-control">';
                    }else{
                        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
                    }
                }
            }
        }
    }
?>