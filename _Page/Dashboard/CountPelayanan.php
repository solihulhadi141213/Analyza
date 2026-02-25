<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Set header JSON
    header('Content-Type: application/json');

    // Siapkan variabel default
    $response = [
        "diminta" => 0,
        "ditolak" => 0,
        "diterima" => 0,
        "selesai" => 0
    ];

    // Optimasi: hitung seluruh metrik dalam satu query agregasi
    $query = "
        SELECT
            COALESCE(SUM(CASE WHEN status = 'Diminta' THEN 1 ELSE 0 END), 0) AS diminta,
            COALESCE(SUM(CASE WHEN status IN ('Ditolak', 'Dibatalkan') THEN 1 ELSE 0 END), 0) AS ditolak,
            COALESCE(SUM(CASE WHEN status NOT IN ('Diminta', 'Ditolak', 'Dibatalkan') THEN 1 ELSE 0 END), 0) AS diterima,
            COALESCE(SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END), 0) AS selesai
        FROM laboratorium
    ";

    $result = $Conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $response = [
            "diminta" => (int) $row['diminta'],
            "ditolak" => (int) $row['ditolak'],
            "diterima" => (int) $row['diterima'],
            "selesai" => (int) $row['selesai']
        ];
    }

    // Response
    echo json_encode($response);
?>
