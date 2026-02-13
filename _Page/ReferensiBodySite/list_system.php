<?php
    include "../../_Config/Connection.php";
    $query = mysqli_query($Conn, "SELECT DISTINCT body_site_system FROM referensi_body_site ORDER BY body_site_system ASC");
    while ($data = mysqli_fetch_array($query)) {
        $body_site_system = $data['body_site_system'];
        echo '<option value="'.$body_site_system.'">';
    }
?>