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
            nama_container LIKE ? OR
            display_container LIKE ? OR
            code_container LIKE ?
        )";
    }

    $countSql = "SELECT COUNT(*) as total FROM referensi_container $where";
    $countStmt = $Conn->prepare($countSql);

    if ($where) {
        $countStmt->bind_param("sss", $search, $search, $search);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT * FROM referensi_container $where ORDER BY nama_container ASC LIMIT $limit OFFSET $offset";
    $stmt = $Conn->prepare($sql);

    if ($where) {
        $stmt->bind_param("sss", $search, $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id"          => $row['id_referensi_container'],
            "text"        => $row['nama_container'],
            "display"     => $row['display_container'],
            "code"        => $row['code_container'],
            "system"      => $row['system_container'],
            "kapasitas"   => $row['kapasitas_container'],
            "unit"        => $row['unit_container'],
            "unit_code"   => $row['code_unit_container'],
            "unit_system" => $row['system_unit_container'],
        ];
    }

    echo json_encode([
        "results" => $data,
        "more"    => ($offset + $limit) < $total
    ]);
?>
