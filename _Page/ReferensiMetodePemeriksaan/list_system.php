<?php
    include "../../_Config/Connection.php";
    $query = mysqli_query($Conn, "SELECT DISTINCT system_metode_pemeriksaan FROM referensi_metode_pemeriksaan ORDER BY system_metode_pemeriksaan ASC");
    while ($data = mysqli_fetch_array($query)) {
        $system_metode_pemeriksaan = $data['system_metode_pemeriksaan'];
        echo '<option value="'.$system_metode_pemeriksaan.'">';
    }
?>
