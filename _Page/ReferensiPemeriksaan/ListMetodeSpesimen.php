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
            nama_metode_sample LIKE ? OR
            display_metode_sample LIKE ? OR
            code_metode_sample LIKE ?
        )";
    }

    $countSql = "SELECT COUNT(*) as total FROM referensi_metode_sample $where";
    $countStmt = $Conn->prepare($countSql);

    if ($where) {
        $countStmt->bind_param("sss", $search, $search, $search);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT * FROM referensi_metode_sample $where ORDER BY nama_metode_sample ASC LIMIT $limit OFFSET $offset";
    $stmt = $Conn->prepare($sql);

    if ($where) {
        $stmt->bind_param("sss", $search, $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id"      => $row['id_referensi_metode_sample'],
            "text"    => $row['nama_metode_sample'],
            "display" => $row['display_metode_sample'],
            "code"    => $row['code_metode_sample'],
            "system"  => $row['system_metode_sample']
        ];
    }

    echo json_encode([
        "results" => $data,
        "more"    => ($offset + $limit) < $total
    ]);
?>
