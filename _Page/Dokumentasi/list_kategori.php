<?php
    header('Content-Type: application/json');
    include "../../_Config/Connection.php";

    $search = $_POST['search'] ?? '';
    $search = mysqli_real_escape_string($Conn, $search);

    $where = "";
    if($search != ''){
        $where = "AND dokumentasi_category LIKE '%$search%'";
    }

    $query = "
        SELECT DISTINCT dokumentasi_category 
        FROM dokumentasi 
        WHERE dokumentasi_category != '' 
        $where
        ORDER BY dokumentasi_category ASC
    ";

    $result = mysqli_query($Conn, $query);

    $data = [];

    while($row = mysqli_fetch_assoc($result)){
        $data[] = [
            "id"   => $row['dokumentasi_category'],
            "text" => $row['dokumentasi_category']
        ];
    }

    echo json_encode([
        "results" => $data,
        "more"    => false
    ]);

?>