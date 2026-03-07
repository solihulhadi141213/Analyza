<?php
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";
require "../../vendor/autoload.php";

use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set("Asia/Jakarta");

function outError($message)
{
    echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>Export Laporan SATUSEHAT</title></head><body>";
    echo "<p style=\"color:#b91c1c;font-family:Arial,sans-serif;\">" . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . "</p>";
    echo "</body></html>";
    exit;
}

function esc($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, "UTF-8");
}

function getCount(mysqli $Conn, $query, $tahunBulan)
{
    $stmt = $Conn->prepare($query);
    if (!$stmt) {
        throw new RuntimeException("Gagal menyiapkan query.");
    }
    $stmt->bind_param("s", $tahunBulan);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new RuntimeException("Gagal mengeksekusi query.");
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return (int) ($row["jumlah"] ?? 0);
}

function buildRows(mysqli $Conn, $tahun)
{
    $months = [
        "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr",
        "05" => "Mei", "06" => "Jun", "07" => "Jul", "08" => "Agu",
        "09" => "Sep", "10" => "Okt", "11" => "Nov", "12" => "Des"
    ];

    $metrics = [
        [
            "no" => 1,
            "label" => "Jumlah Total Pelayanan Laboratorium",
            "query" => "SELECT COUNT(*) AS jumlah
                        FROM laboratorium a
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')",
        ],
        [
            "no" => 2,
            "label" => "Jumlah Pasien Tanpa ID IHS",
            "query" => "SELECT COUNT(*) AS jumlah
                        FROM laboratorium a
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(a.ihs_pasien, '') = ''",
        ],
        [
            "no" => 3,
            "label" => "Jumlah Pasien Dengan ID IHS",
            "query" => "SELECT COUNT(*) AS jumlah
                        FROM laboratorium a
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(a.ihs_pasien, '') <> ''",
        ],
        [
            "no" => 4,
            "label" => "Jumlah Pasien Tanpa Encounter",
            "query" => "SELECT COUNT(*) AS jumlah
                        FROM laboratorium a
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(a.id_encounter, '') = ''",
        ],
        [
            "no" => 5,
            "label" => "Jumlah Pasien Dengan Encounter",
            "query" => "SELECT COUNT(*) AS jumlah
                        FROM laboratorium a
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(a.id_encounter, '') <> ''",
        ],
        [
            "no" => 6,
            "label" => "Jumlah Pasien Tanpa Service Request",
            "query" => "SELECT COUNT(DISTINCT a.id_laboratorium) AS jumlah
                        FROM laboratorium a
                        LEFT JOIN laboratorium_rincian b ON a.id_laboratorium = b.id_laboratorium
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(b.id_service_request, '') = ''",
        ],
        [
            "no" => 7,
            "label" => "Jumlah Pasien Dengan Service Request",
            "query" => "SELECT COUNT(DISTINCT a.id_laboratorium) AS jumlah
                        FROM laboratorium a
                        JOIN laboratorium_rincian b ON a.id_laboratorium = b.id_laboratorium
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(b.id_service_request, '') <> ''",
        ],
        [
            "no" => 8,
            "label" => "Jumlah Pasien Dengan Specimen",
            "query" => "SELECT COUNT(DISTINCT a.id_laboratorium) AS jumlah
                        FROM laboratorium a
                        JOIN laboratorium_spesimen b ON a.id_laboratorium = b.id_laboratorium
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(b.id_speciment, '') <> ''",
        ],
        [
            "no" => 9,
            "label" => "Jumlah Pasien Tanpa Specimen",
            "query" => "SELECT COUNT(DISTINCT a.id_laboratorium) AS jumlah
                        FROM laboratorium a
                        LEFT JOIN laboratorium_spesimen b ON a.id_laboratorium = b.id_laboratorium
                        WHERE a.datetime_diminta LIKE CONCAT(?, '%')
                          AND COALESCE(b.id_speciment, '') = ''",
        ],
    ];

    $rows = [];
    foreach ($metrics as $metric) {
        $item = [
            "no" => $metric["no"],
            "label" => $metric["label"],
            "months" => [],
            "total" => 0,
        ];

        foreach ($months as $monthNumber => $monthLabel) {
            $tahunBulan = $tahun . "-" . $monthNumber;
            $jumlah = getCount($Conn, $metric["query"], $tahunBulan);
            $item["months"][$monthNumber] = $jumlah;
            $item["total"] += $jumlah;
        }

        $rows[] = $item;
    }

    return [$rows, $months];
}

if (empty($SessionIdAccess)) {
    outError("Sesi Akses Sudah Berakhir! Silahkan Login Ulang!");
}

$tahun = isset($_GET["tahun"]) ? preg_replace("/[^0-9]/", "", $_GET["tahun"]) : date("Y");
$format = isset($_GET["format_data"]) ? trim($_GET["format_data"]) : "HTML";

if (strlen($tahun) !== 4) {
    outError("Tahun tidak valid.");
}

if (!in_array($format, ["HTML", "PDF", "Excel"], true)) {
    outError("Format data tidak valid.");
}

try {
    list($rows, $months) = buildRows($Conn, $tahun);
} catch (Throwable $e) {
    outError("Gagal mengambil data export: " . $e->getMessage());
}

$filenameBase = "Laporan_SATUSEHAT_" . $tahun . "_" . date("Ymd_His");
$title = "LAPORAN SATUSEHAT";
$subtitle = "PERIODE TAHUN " . $tahun;

if ($format === "HTML") {
    echo "<!DOCTYPE html>
    <html lang=\"id\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>" . esc($filenameBase) . "</title>
        <style>
            body{font-family:Arial,sans-serif;margin:20px;color:#111827;}
            h2{margin:0 0 4px 0;text-align:center;}
            p.sub{margin:0 0 12px 0;text-align:center;font-weight:bold;}
            table{width:100%;border-collapse:collapse;}
            th,td{border:1px solid #374151;padding:6px;font-size:12px;}
            th{background:#e5e7eb;text-align:center;}
            td.text-left{text-align:left;}
            td.text-center{text-align:center;}
        </style>
    </head>
    <body>
        <h2>" . esc($title) . "</h2>
        <p class=\"sub\">" . esc($subtitle) . "</p>
        <table>
            <thead>
                <tr>
                    <th rowspan=\"2\" style=\"width:50px;\">No</th>
                    <th rowspan=\"2\">Resource SATUSEHAT</th>
                    <th colspan=\"12\">PERIODE TAHUN " . esc($tahun) . "</th>
                    <th rowspan=\"2\" style=\"width:90px;\">Jumlah</th>
                </tr>
                <tr>";

    foreach ($months as $monthLabel) {
        echo "<th>" . esc($monthLabel) . "</th>";
    }

    echo "</tr>
            </thead>
            <tbody>";

    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td class=\"text-center\">" . (int) $row["no"] . "</td>";
        echo "<td class=\"text-left\">" . esc($row["label"]) . "</td>";

        foreach ($row["months"] as $jumlah) {
            echo "<td class=\"text-center\">" . number_format((int) $jumlah) . "</td>";
        }

        echo "<td class=\"text-center\"><b>" . number_format((int) $row["total"]) . "</b></td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>
    </body>
    </html>";
    exit;
}

if ($format === "PDF") {
    if (!class_exists("\\Mpdf\\Mpdf")) {
        outError("Pustaka mPDF belum terpasang. Jalankan: composer require mpdf/mpdf");
    }

    $pdfHtml = "
        <style>
            body{font-family:sans-serif;font-size:10px;}
            h3{margin:0 0 4px 0;text-align:center;}
            .sub{margin:0 0 10px 0;text-align:center;}
            table{width:100%;border-collapse:collapse;}
            th,td{border:1px solid #333;padding:4px;font-size:9px;}
            th{text-align:center;background:#f2f2f2;}
            td{text-align:center;}
            td.text-left{text-align:left;}
        </style>
        <h3>" . esc($title) . "</h3>
        <p class=\"sub\">" . esc($subtitle) . "</p>
        <table>
            <thead>
                <tr>
                    <th rowspan=\"2\">No</th>
                    <th rowspan=\"2\">Resource SATUSEHAT</th>
                    <th colspan=\"12\">PERIODE TAHUN " . esc($tahun) . "</th>
                    <th rowspan=\"2\">Jumlah</th>
                </tr>
                <tr>";

    foreach ($months as $monthLabel) {
        $pdfHtml .= "<th>" . esc($monthLabel) . "</th>";
    }

    $pdfHtml .= "</tr></thead><tbody>";

    foreach ($rows as $row) {
        $pdfHtml .= "<tr>";
        $pdfHtml .= "<td>" . (int) $row["no"] . "</td>";
        $pdfHtml .= "<td class=\"text-left\">" . esc($row["label"]) . "</td>";
        foreach ($row["months"] as $jumlah) {
            $pdfHtml .= "<td>" . number_format((int) $jumlah) . "</td>";
        }
        $pdfHtml .= "<td><b>" . number_format((int) $row["total"]) . "</b></td>";
        $pdfHtml .= "</tr>";
    }

    $pdfHtml .= "</tbody></table>";

    try {
        $mpdf = new Mpdf(["format" => "A4-L"]);
        $mpdf->WriteHTML($pdfHtml);
        $mpdf->Output($filenameBase . ".pdf", "D");
    } catch (Throwable $e) {
        outError("Gagal membuat file PDF: " . $e->getMessage());
    }
    exit;
}

if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\Spreadsheet")) {
    outError("Pustaka PhpSpreadsheet belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet");
}

try {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("SATUSEHAT " . $tahun);

    $sheet->setCellValue("A1", "No");
    $sheet->setCellValue("B1", "Resource SATUSEHAT");
    $sheet->setCellValue("C1", "PERIODE TAHUN " . $tahun);
    $sheet->setCellValue("O1", "Jumlah");

    $sheet->mergeCells("A1:A2");
    $sheet->mergeCells("B1:B2");
    $sheet->mergeCells("C1:N1");
    $sheet->mergeCells("O1:O2");

    $monthColumns = ["C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N"];
    $i = 0;
    foreach ($months as $monthLabel) {
        $sheet->setCellValue($monthColumns[$i] . "2", $monthLabel);
        $i++;
    }

    $rowNumber = 3;
    foreach ($rows as $row) {
        $sheet->setCellValue("A" . $rowNumber, (int) $row["no"]);
        $sheet->setCellValue("B" . $rowNumber, $row["label"]);

        $colIndex = 0;
        foreach ($row["months"] as $jumlah) {
            $sheet->setCellValue($monthColumns[$colIndex] . $rowNumber, (int) $jumlah);
            $colIndex++;
        }

        $sheet->setCellValue("O" . $rowNumber, (int) $row["total"]);
        $rowNumber++;
    }

    $lastRow = $rowNumber - 1;
    $sheet->getStyle("A1:O2")->getFont()->setBold(true);
    $sheet->getStyle("A1:O2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A1:O2")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle("A1:O2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB("E5E7EB");

    $sheet->getStyle("A3:A" . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3:B" . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("C3:O" . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A1:O" . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    foreach (range("A", "O") as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=\"" . $filenameBase . ".xlsx\"");
    header("Cache-Control: max-age=0");
    $writer->save("php://output");
} catch (Throwable $e) {
    outError("Gagal membuat file Excel: " . $e->getMessage());
}
exit;
