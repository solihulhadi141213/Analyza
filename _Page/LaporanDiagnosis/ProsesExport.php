<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    require "../../vendor/autoload.php";

    use Mpdf\Mpdf;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;

    date_default_timezone_set("Asia/Jakarta");

    function outError($message)
    {
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Export Laporan Diagnosis</title></head><body>';
        echo '<p style="color:#b91c1c;">' . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . '</p>';
        echo '</body></html>';
        exit;
    }

    function esc($text)
    {
        return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
    }

    if (empty($SessionIdAccess)) {
        outError("Sesi Akses Sudah Berakhir! Silahkan Login Ulang!");
    }

    $format_data = isset($_GET["format_data"]) ? trim($_GET["format_data"]) : "HTML";
    $periode = isset($_GET["periode"]) ? trim($_GET["periode"]) : "Semua";
    $tahun = isset($_GET["tahun"]) ? preg_replace("/[^0-9]/", "", $_GET["tahun"]) : "";
    $bulan = isset($_GET["bulan"]) ? preg_replace("/[^0-9]/", "", $_GET["bulan"]) : "";
    $bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);

    if ($format_data !== "PDF" && $format_data !== "Excel" && $format_data !== "HTML") {
        outError("Format data tidak valid.");
    }

    if ($periode !== "Semua" && $periode !== "Tahunan" && $periode !== "Bulanan") {
        outError("Periode data tidak valid.");
    }

    $nama_bulan = [
        "01" => "Januari",
        "02" => "Februari",
        "03" => "Maret",
        "04" => "April",
        "05" => "Mei",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "Agustus",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember"
    ];

    if (($periode === "Tahunan" || $periode === "Bulanan") && strlen($tahun) !== 4) {
        outError("Periode tahun tidak valid.");
    }

    if ($periode === "Bulanan" && !array_key_exists($bulan, $nama_bulan)) {
        outError("Periode bulan tidak valid.");
    }

    $where = " WHERE ld.icd_10_code IS NOT NULL ";
    $params = [];
    $types = "";

    if ($periode === "Tahunan") {
        $where .= " AND YEAR(l.datetime_diminta) = ? ";
        $params[] = (int)$tahun;
        $types .= "i";
    } elseif ($periode === "Bulanan") {
        $where .= " AND YEAR(l.datetime_diminta) = ? ";
        $where .= " AND MONTH(l.datetime_diminta) = ? ";
        $params[] = (int)$tahun;
        $params[] = (int)$bulan;
        $types .= "ii";
    }

    $sql = "
        SELECT
            ld.icd_10_code,
            ld.icd_10_display,
            ld.icd_10_system,
            COUNT(*) AS jumlah
        FROM laboratorium l
        INNER JOIN laboratorium_diagnostic ld
            ON l.id_laboratorium = ld.id_laboratorium
        $where
        GROUP BY
            ld.icd_10_code,
            ld.icd_10_display,
            ld.icd_10_system
        ORDER BY jumlah DESC
    ";

    $stmt = $Conn->prepare($sql);
    if (!$stmt) {
        outError("Gagal menyiapkan query export.");
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        outError("Gagal mengambil data export.");
    }

    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            "code" => $row["icd_10_code"],
            "display" => $row["icd_10_display"],
            "system" => $row["icd_10_system"],
            "jumlah" => (int)$row["jumlah"]
        ];
    }
    $stmt->close();

    if (count($rows) < 1) {
        outError("Tidak ada data diagnosis untuk periode yang dipilih.");
    }

    $subjudul = "Semua Periode";
    if ($periode === "Tahunan") {
        $subjudul = "Periode Tahun " . $tahun;
    } elseif ($periode === "Bulanan") {
        $subjudul = "Periode Bulan " . $nama_bulan[$bulan] . " " . $tahun;
    }

    $filename_base = "Laporan_Diagnosis_" . $periode;
    if ($periode === "Tahunan") {
        $filename_base .= "_" . $tahun;
    } elseif ($periode === "Bulanan") {
        $filename_base .= "_" . $tahun . "_" . $bulan;
    }
    $filename_base .= "_" . date("Ymd_His");

    if ($format_data === "HTML") {
        echo '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . esc($filename_base) . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #1f2937; }
                .title { text-align: center; margin-bottom: 14px; }
                .title h2 { margin: 0; font-size: 20px; }
                .title p { margin: 3px 0 0; font-size: 13px; font-weight: bold; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #6b7280; padding: 6px; font-size: 12px; }
                th { background: #e5e7eb; text-align: center; }
                .text-center { text-align: center; }
                .text-left { text-align: left; }
            </style>
        </head>
        <body>
            <div class="title">
                <h2>LAPORAN DIAGNOSIS LABORATORIUM</h2>
                <p>' . esc($subjudul) . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Kode ICD-10</th>
                        <th>Display</th>
                        <th>System</th>
                        <th style="width:100px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($rows as $row) {
            echo '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-left">' . esc($row["code"]) . '</td>
                        <td class="text-left">' . esc($row["display"]) . '</td>
                        <td class="text-left">' . esc($row["system"]) . '</td>
                        <td class="text-center">' . number_format($row["jumlah"]) . '</td>
                    </tr>';
            $no++;
        }

        echo '
                </tbody>
            </table>
        </body>
        </html>';
        exit;
    }

    if ($format_data === "PDF") {
        if (!class_exists("\\Mpdf\\Mpdf")) {
            outError("Pustaka mPDF belum terpasang. Jalankan: composer require mpdf/mpdf");
        }

        $html = '
            <style>
                body{font-family: sans-serif; font-size: 10px;}
                h3{margin:0 0 4px 0; text-align:center;}
                .sub{margin:0 0 12px 0; text-align:center;}
                table{border-collapse:collapse; width:100%;}
                th,td{border:1px solid #333; padding:4px; font-size:9px;}
                th{text-align:center; background:#f2f2f2;}
                td{text-align:center;}
                td.text-left{text-align:left;}
            </style>
            <h3>LAPORAN DIAGNOSIS LABORATORIUM</h3>
            <p class="sub">' . esc($subjudul) . '</p>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode ICD-10</th>
                        <th>Display</th>
                        <th>System</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
        ';

        $no = 1;
        foreach ($rows as $row) {
            $html .= '
                <tr>
                    <td>' . $no . '</td>
                    <td class="text-left">' . esc($row["code"]) . '</td>
                    <td class="text-left">' . esc($row["display"]) . '</td>
                    <td class="text-left">' . esc($row["system"]) . '</td>
                    <td>' . number_format($row["jumlah"]) . '</td>
                </tr>
            ';
            $no++;
        }

        $html .= '</tbody></table>';

        try {
            $mpdf = new Mpdf(["format" => "A4-L"]);
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename_base . ".pdf", "D");
        } catch (Throwable $e) {
            outError("Gagal membuat file PDF: " . $e->getMessage());
        }
        exit;
    }

    if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\Spreadsheet")) {
        outError("Pustaka PhpSpreadsheet belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet");
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Laporan Diagnosis");

    $sheet->mergeCells("A1:E1");
    $sheet->setCellValue("A1", "LAPORAN DIAGNOSIS LABORATORIUM");
    $sheet->mergeCells("A2:E2");
    $sheet->setCellValue("A2", $subjudul);
    $sheet->getStyle("A1:E2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A1:E2")->getFont()->setBold(true);

    $sheet->setCellValue("A4", "No");
    $sheet->setCellValue("B4", "Kode ICD-10");
    $sheet->setCellValue("C4", "Display");
    $sheet->setCellValue("D4", "System");
    $sheet->setCellValue("E4", "Jumlah");
    $sheet->getStyle("A4:E4")->getFont()->setBold(true);
    $sheet->getStyle("A4:E4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $rowIndex = 5;
    $no = 1;
    foreach ($rows as $row) {
        $sheet->setCellValue("A" . $rowIndex, $no);
        $sheet->setCellValue("B" . $rowIndex, $row["code"]);
        $sheet->setCellValue("C" . $rowIndex, $row["display"]);
        $sheet->setCellValue("D" . $rowIndex, $row["system"]);
        $sheet->setCellValue("E" . $rowIndex, $row["jumlah"]);
        $rowIndex++;
        $no++;
    }

    $lastRow = $rowIndex - 1;
    $sheet->getStyle("A4:E" . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle("A4:A" . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("E4:E" . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B5:D" . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    foreach (range("A", "E") as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment; filename="' . $filename_base . '.xlsx"');
    header("Cache-Control: max-age=0");

    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
?>
