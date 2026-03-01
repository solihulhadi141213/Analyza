<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    require "../../vendor/autoload.php";
    date_default_timezone_set("Asia/Jakarta");

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;

    function stopExport($message)
    {
        echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>Export Rincian Spesimen</title></head><body>";
        echo "<p style=\"color:#dc2626;\">" . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . "</p>";
        echo "</body></html>";
        exit;
    }

    // Validasi akses
    if (empty($SessionIdAccess)) {
        stopExport("Sesi Akses Sudah Berakhir! Silahkan Login Ulang!");
    }

    // Validasi input
    if (empty($_POST["periode"])) {
        stopExport("Informasi periode tidak boleh kosong.");
    }
    if (empty($_POST["keyword"])) {
        stopExport("Keyword periode tidak boleh kosong.");
    }
    if (empty($_POST["code"])) {
        stopExport("Kode spesimen tidak boleh kosong.");
    }

    $periode = validateAndSanitizeInput($_POST["periode"]);
    $keyword = validateAndSanitizeInput($_POST["keyword"]);
    $code = validateAndSanitizeInput($_POST["code"]);

    if ($periode !== "Tahun" && $periode !== "Bulan") {
        stopExport("Periode tidak valid.");
    }

    // Format file: hanya Excel
    $format_file = isset($_POST["format_file"]) ? validateAndSanitizeInput($_POST["format_file"]) : "Excel";
    if ($format_file !== "Excel") {
        stopExport("Format file hanya mendukung Excel.");
    }

    // Ambil nama spesimen
    $nama_spesimen = "-";
    $stmt_nama = $Conn->prepare("
        SELECT nama_spesimen
        FROM laboratorium_spesimen
        WHERE code_spesimen = ?
        LIMIT 1
    ");
    if ($stmt_nama) {
        $stmt_nama->bind_param("s", $code);
        $stmt_nama->execute();
        $result_nama = $stmt_nama->get_result();
        if ($row_nama = $result_nama->fetch_assoc()) {
            $nama_spesimen = $row_nama["nama_spesimen"];
        }
        $stmt_nama->close();
    }

    // Judul periode
    $judul_periode = "PERIODE TAHUN " . $keyword;
    if ($periode === "Bulan") {
        $bulan_map = [
            "01" => "JANUARI", "02" => "FEBRUARI", "03" => "MARET", "04" => "APRIL",
            "05" => "MEI", "06" => "JUNI", "07" => "JULI", "08" => "AGUSTUS",
            "09" => "SEPTEMBER", "10" => "OKTOBER", "11" => "NOVEMBER", "12" => "DESEMBER"
        ];
        $tahun = substr($keyword, 0, 4);
        $bulan = substr($keyword, 5, 2);
        if (isset($bulan_map[$bulan])) {
            $judul_periode = "PERIODE " . $bulan_map[$bulan] . " " . $tahun;
        }
    }

    // Query rincian tanpa paging
    $stmt = $Conn->prepare("
        SELECT
            l.nama,
            l.id_pasien,
            l.datetime_diminta,
            ls.nama_metode_sample,
            ls.bodysite_nama,
            ls.nama_container,
            ls.quantity_value,
            ls.quantity_unit
        FROM laboratorium_spesimen ls
        INNER JOIN laboratorium l ON ls.id_laboratorium = l.id_laboratorium
        WHERE ls.code_spesimen = ?
        AND ls.datetime_spesimen LIKE CONCAT(?, '%')
        ORDER BY ls.datetime_spesimen DESC
    ");

    if (!$stmt) {
        stopExport("Gagal menyiapkan query export.");
    }

    $stmt->bind_param("ss", $code, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        $stmt->close();
        stopExport("Tidak ada data rincian spesimen yang bisa diexport.");
    }

    if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\Spreadsheet")) {
        $stmt->close();
        stopExport("Pustaka PhpSpreadsheet tidak ditemukan.");
    }

    // Buat file Excel (.xlsx)
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Rincian Spesimen");

    $sheet->mergeCells("A1:H1");
    $sheet->setCellValue("A1", "RINCIAN LAPORAN SPESIMEN");
    $sheet->mergeCells("A2:H2");
    $sheet->setCellValue("A2", "SPESIMEN: " . $nama_spesimen);
    $sheet->mergeCells("A3:H3");
    $sheet->setCellValue("A3", $judul_periode);
    $sheet->getStyle("A1:H3")->getFont()->setBold(true);
    $sheet->getStyle("A1:H3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue("A5", "No");
    $sheet->setCellValue("B5", "Nama Pasien");
    $sheet->setCellValue("C5", "RM");
    $sheet->setCellValue("D5", "Tanggal/Jam");
    $sheet->setCellValue("E5", "Method");
    $sheet->setCellValue("F5", "Body Site");
    $sheet->setCellValue("G5", "Container");
    $sheet->setCellValue("H5", "Value");
    $sheet->getStyle("A5:H5")->getFont()->setBold(true);
    $sheet->getStyle("A5:H5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $rowIndex = 6;
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $tanggal_jam = "-";
        if (!empty($row["datetime_diminta"])) {
            $tanggal_jam = date("d/m/Y H:i", strtotime($row["datetime_diminta"]));
        }
        $value = trim((string)$row["quantity_value"] . " " . (string)$row["quantity_unit"]);

        $sheet->setCellValue("A" . $rowIndex, $no);
        $sheet->setCellValue("B" . $rowIndex, $row["nama"]);
        $sheet->setCellValue("C" . $rowIndex, $row["id_pasien"]);
        $sheet->setCellValue("D" . $rowIndex, $tanggal_jam);
        $sheet->setCellValue("E" . $rowIndex, $row["nama_metode_sample"]);
        $sheet->setCellValue("F" . $rowIndex, $row["bodysite_nama"]);
        $sheet->setCellValue("G" . $rowIndex, $row["nama_container"]);
        $sheet->setCellValue("H" . $rowIndex, $value);

        $rowIndex++;
        $no++;
    }

    // Atur lebar kolom
    foreach (["A" => 6, "B" => 28, "C" => 16, "D" => 18, "E" => 20, "F" => 20, "G" => 20, "H" => 16] as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }
    $sheet->getStyle("A6:A" . ($rowIndex - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $filename = "Rincian_Spesimen_" . preg_replace("/[^a-zA-Z0-9_\\-]/", "_", $code) . "_" . date("Ymd_His") . ".xlsx";
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
    header("Cache-Control: max-age=0");

    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");

    $stmt->close();
    exit;
?>
