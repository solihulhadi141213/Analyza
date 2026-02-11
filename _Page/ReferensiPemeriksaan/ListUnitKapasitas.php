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
            nama_satuan LIKE ? OR
            unit_satuan LIKE ? OR
            code_satuan LIKE ?
        )";
    }

    $countSql = "SELECT COUNT(*) as total FROM referensi_satuan $where";
    $countStmt = $Conn->prepare($countSql);

    if ($where) {
        $countStmt->bind_param("sss", $search, $search, $search);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT * FROM referensi_satuan $where ORDER BY nama_satuan ASC LIMIT $limit OFFSET $offset";
    $stmt = $Conn->prepare($sql);

    if ($where) {
        $stmt->bind_param("sss", $search, $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id"      => $row['unit_satuan'],
            "text"    => $row['nama_satuan'] . " (" . $row['unit_satuan'] . ")",
            "display" => $row['unit_satuan'],
            "code"    => $row['code_satuan'],
            "system"  => $row['system_satuan']
        ];
    }

    echo json_encode([
        "results" => $data,
        "more"    => ($offset + $limit) < $total
    ]);
?>
