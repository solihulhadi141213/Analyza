<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    $search = $_POST['search'] ?? '';
    $page   = (int)($_POST['page'] ?? 1);
    if ($page < 1) {
        $page = 1;
    }

    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $where = "";
    if (!empty($search)) {
        $search = "%$search%";
        $where = "WHERE (
            nama_metode_pemeriksaan LIKE ? OR
            display_metode_pemeriksaan LIKE ? OR
            code_metode_pemeriksaan LIKE ?
        )";
    }

    $countSql = "SELECT COUNT(*) as total FROM referensi_metode_pemeriksaan $where";
    $countStmt = $Conn->prepare($countSql);

    if ($where) {
        $countStmt->bind_param("sss", $search, $search, $search);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT * FROM referensi_metode_pemeriksaan $where ORDER BY nama_metode_pemeriksaan ASC LIMIT $limit OFFSET $offset";
    $stmt = $Conn->prepare($sql);

    if ($where) {
        $stmt->bind_param("sss", $search, $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $nama_metode_pemeriksaan    = $row['nama_metode_pemeriksaan'];
        $display_metode_pemeriksaan = $row['display_metode_pemeriksaan'];
        $code_metode_pemeriksaan    = $row['code_metode_pemeriksaan'];

        $data[] = [
            "id"      => $row['id_referensi_metode_pemeriksaan'],
            "text"    => $row['nama_metode_pemeriksaan'],
            "nama"    => $row['nama_metode_pemeriksaan'],
            "display" => $row['display_metode_pemeriksaan'],
            "code"    => $row['code_metode_pemeriksaan'],
            "system"  => $row['system_metode_pemeriksaan']
        ];
    }

    echo json_encode([
        "results" => $data,
        "more"    => ($offset + $limit) < $total
    ]);
?>
