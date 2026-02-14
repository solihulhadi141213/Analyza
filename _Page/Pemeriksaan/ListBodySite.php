<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    $search = $_POST['search'] ?? '';
    $page = (int) ($_POST['page'] ?? 1);
    if ($page < 1) {
        $page = 1;
    }

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where = "";
    if (!empty($search)) {
        $search = "%$search%";
        $where = "WHERE (
            body_site_nama LIKE ? OR
            body_site_display LIKE ? OR
            body_site_code LIKE ?
        )";
    }

    $countSql = "SELECT COUNT(*) as total FROM referensi_body_site $where";
    $countStmt = $Conn->prepare($countSql);
    if ($where) {
        $countStmt->bind_param("sss", $search, $search, $search);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT * FROM referensi_body_site $where ORDER BY body_site_nama ASC LIMIT $limit OFFSET $offset";
    $stmt = $Conn->prepare($sql);
    if ($where) {
        $stmt->bind_param("sss", $search, $search, $search);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $body_site_nama = $row['body_site_nama'];
        $body_site_display = $row['body_site_display'];
        $body_site_code = $row['body_site_code'];

        $data[] = [
            "id" => $row['id_referensi_body_site'],
            "text" => "$body_site_code - $body_site_nama ($body_site_display)",
            "display" => $body_site_display,
            "code" => $body_site_code,
            "system" => $row['body_site_system']
        ];
    }

    echo json_encode([
        "results" => $data,
        "more" => ($offset + $limit) < $total
    ]);
?>
