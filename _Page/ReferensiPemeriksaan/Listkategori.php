<?php
    //koneksi dan session
    include "../../_Config/Connection.php";

    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT DISTINCT category_pemeriksaan FROM referensi_pemeriksaan"));
    if(!empty($jml_data)){
        $query = mysqli_query($Conn, "SELECT DISTINCT category_pemeriksaan FROM referensi_pemeriksaan ORDER BY category_pemeriksaan ASC");
        while ($data = mysqli_fetch_array($query)) {
            $category_pemeriksaan = $data['category_pemeriksaan'];
            echo '<option value="'.$category_pemeriksaan.'">';
        }
    }
?>