<?php
    include "../../_Config/Connection.php";
    $query = mysqli_query($Conn, "SELECT DISTINCT system_container FROM referensi_container ORDER BY system_container ASC");
    while ($data = mysqli_fetch_array($query)) {
        $system_container = $data['system_container'];
        echo '<option value="'.$system_container.'">';
    }
?>