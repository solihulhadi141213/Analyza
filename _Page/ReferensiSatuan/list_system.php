<?php
    include "../../_Config/Connection.php";
    $query = mysqli_query($Conn, "SELECT DISTINCT system_satuan FROM referensi_satuan ORDER BY system_satuan ASC");
    while ($data = mysqli_fetch_array($query)) {
        $system_satuan = $data['system_satuan'];
        echo '<option value="'.$system_satuan.'">';
    }
?>