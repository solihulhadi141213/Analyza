<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Menangkap keyword dan page
    $keyword = $_POST['keyword'] ?? "";
    $page    = $_POST['page'] ?? 1;

    // Menentukan Limit dan offset
    $limit  = 10;
    $offset = ($page - 1) * $limit;

    // Kondisi WHERE untuk pencarian
    $where = "";
    if (!empty($keyword)) {
        $safe = mysqli_real_escape_string($Conn, $keyword);
        $where = "WHERE nama_satuan LIKE '%$safe%' OR unit_satuan LIKE '%$safe%' OR code_satuan LIKE '%$safe%'";
    }

    // Mulai Query
    $sql = "SELECT * FROM referensi_satuan $where ORDER BY nama_satuan ASC LIMIT $limit OFFSET $offset";
    $q = mysqli_query($Conn, $sql);
    $results = [];
    while ($row = mysqli_fetch_assoc($q)) {
        $nama_satuan = $row['nama_satuan'];
        $unit_satuan = $row['unit_satuan'];
        $code_satuan = $row['code_satuan'];

        // Buat Payload
        $results[] = [
            "id" => $row['id_referensi_satuan'],
            "text" => "$nama_satuan ($unit_satuan)"
        ];
    }

    // hitung total untuk pagination
    $countSql = "SELECT COUNT(*) total FROM referensi_satuan $where";
    $countQ = mysqli_query($Conn, $countSql);
    $total = mysqli_fetch_assoc($countQ)['total'];

    $more = ($offset + $limit) < $total;

    echo json_encode([
        "results" => $results,
        "more" => $more
    ]);
?>