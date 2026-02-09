<?php
    include "../../_Config/Connection.php";
    $query = mysqli_query($Conn, "SELECT DISTINCT system_metode_sample FROM referensi_metode_sample ORDER BY system_metode_sample ASC");
    while ($data = mysqli_fetch_array($query)) {
        $system_metode_sample = $data['system_metode_sample'];
        echo '<option value="'.$system_metode_sample.'">';
    }
?>