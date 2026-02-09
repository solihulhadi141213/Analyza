<?php
    include "../../_Config/Connection.php";
    $query = mysqli_query($Conn, "SELECT DISTINCT system_spesimen FROM referensi_jenis_spesimen ORDER BY system_spesimen ASC");
    while ($data = mysqli_fetch_array($query)) {
        $system_spesimen = $data['system_spesimen'];
        echo '<option value="'.$system_spesimen.'">';
    }
?>