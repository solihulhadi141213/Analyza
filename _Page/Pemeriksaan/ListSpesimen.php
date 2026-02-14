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
        $where = "WHERE (nama_spesimen LIKE ? OR display_spesimen LIKE ? OR code_spesimen LIKE ?)";
    }

    $countSql = "SELECT COUNT(*) as total FROM referensi_jenis_spesimen $where";
    $countStmt = $Conn->prepare($countSql);

    if ($where) {
        $countStmt->bind_param("sss", $search, $search, $search);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT * FROM referensi_jenis_spesimen $where ORDER BY nama_spesimen ASC LIMIT $limit OFFSET $offset";
    $stmt = $Conn->prepare($sql);

    if ($where) {
        $stmt->bind_param("sss", $search, $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $id_referensi_jenis_spesimen = $row['id_referensi_jenis_spesimen'];
        $nama_spesimen               = $row['nama_spesimen'];
        $display_spesimen            = $row['display_spesimen'];
        $code_spesimen               = $row['code_spesimen'];
        $system_spesimen             = $row['system_spesimen'];
        $data[] = [
            "id"      => $id_referensi_jenis_spesimen,
            "text"    => "$code_spesimen - $nama_spesimen ($display_spesimen)",
            "display" => $display_spesimen,
            "code"    => $code_spesimen,
            "system"  => $system_spesimen
        ];
    }

    echo json_encode([
        "results" => $data,
        "more"    => ($offset + $limit) < $total
    ]);
?>
