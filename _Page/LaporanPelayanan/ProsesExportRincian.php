<?php
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";
require "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set("Asia/Jakarta");

function formatDurasiRincian($detik)
{
    $detik = (int)$detik;
    if ($detik < 0) {
        $detik = 0;
    }

    if ($detik < 60) {
        return $detik . " Detik";
    }
    if ($detik < 3600) {
        return round($detik / 60, 2) . " Menit";
    }
    if ($detik < 86400) {
        return round($detik / 3600, 2) . " Jam";
    }
    if ($detik < 2592000) {
        return round($detik / 86400, 2) . " Hari";
    }
    if ($detik < 31536000) {
        return round($detik / 2592000, 2) . " Bulan";
    }
    return round($detik / 31536000, 2) . " Tahun";
}

function labelPeriodeRincian($periode, $keyword)
{
    if ($periode === "Tahunan") {
        return date("F Y", strtotime($keyword));
    }
    if ($periode === "Bulanan") {
        return date("d F Y", strtotime($keyword));
    }
    return $keyword;
}

function labelPriorityRincian($priority)
{
    if ($priority === "routine") {
        return "Biasa";
    }
    if ($priority === "urgent") {
        return "Segera";
    }
    if ($priority === "stat") {
        return "Gawat";
    }
    return "None";
}

if (empty($SessionIdAccess)) {
    echo "Sesi Akses Sudah Berakhir! Silahkan Login Ulang!";
    exit;
}

$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$periode = isset($_GET["periode"]) ? trim($_GET["periode"]) : "";

if ($keyword === "") {
    echo "Kata kunci rincian laporan tidak boleh kosong!";
    exit;
}
if ($periode === "") {
    echo "Periode laporan tidak boleh kosong!";
    exit;
}

if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\Spreadsheet")) {
    echo "Pustaka PhpSpreadsheet belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet";
    exit;
}

$stmt = $Conn->prepare("
    SELECT id_pasien, nama, gender, datetime_diminta, datetime_hasil, tujuan, pembayaran, priority, status
    FROM laboratorium
    WHERE datetime_diminta LIKE CONCAT('%', ?, '%')
    ORDER BY datetime_diminta ASC
");
if (!$stmt) {
    echo "Gagal menyiapkan query data rincian laporan.";
    exit;
}

$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Rincian Laporan");

$sheet->mergeCells("A1:K1");
$sheet->setCellValue("A1", "RINCIAN LAPORAN PELAYANAN");
$sheet->mergeCells("A2:K2");
$sheet->setCellValue("A2", "Periode " . labelPeriodeRincian($periode, $keyword));

$sheet->mergeCells("A4:A5");
$sheet->mergeCells("B4:B5");
$sheet->mergeCells("C4:C5");
$sheet->mergeCells("D4:D5");
$sheet->mergeCells("E4:E5");
$sheet->mergeCells("F4:F5");
$sheet->mergeCells("G4:G5");
$sheet->mergeCells("H4:H5");
$sheet->mergeCells("I4:J4");
$sheet->mergeCells("K4:K5");

$sheet->setCellValue("A4", "No");
$sheet->setCellValue("B4", "Nama Pasien");
$sheet->setCellValue("C4", "No.RM");
$sheet->setCellValue("D4", "Gender");
$sheet->setCellValue("E4", "Tujuan");
$sheet->setCellValue("F4", "Pembayaran");
$sheet->setCellValue("G4", "Priority");
$sheet->setCellValue("H4", "Status");
$sheet->setCellValue("I4", "Tanggal/Jam");
$sheet->setCellValue("I5", "Diminta");
$sheet->setCellValue("J5", "Selesai");
$sheet->setCellValue("K4", "Durasi");

$rowIndex = 6;
$no = 1;
while ($row = $result->fetch_assoc()) {
    $datetime_diminta = $row["datetime_diminta"];
    $datetime_hasil = $row["datetime_hasil"];

    $label_datetime_diminta = "-";
    if (!empty($datetime_diminta)) {
        $label_datetime_diminta = date("d/m/Y H:i", strtotime($datetime_diminta));
    }

    $label_datetime_hasil = "-";
    if (!empty($datetime_hasil)) {
        $label_datetime_hasil = date("d/m/Y H:i", strtotime($datetime_hasil));
    }

    $durasi = "-";
    if (!empty($datetime_diminta) && !empty($datetime_hasil)) {
        $mulai = strtotime($datetime_diminta);
        $selesai = strtotime($datetime_hasil);
        if ($mulai !== false && $selesai !== false) {
            $durasi = formatDurasiRincian($selesai - $mulai);
        }
    }

    $sheet->setCellValue("A" . $rowIndex, $no);
    $sheet->setCellValue("B" . $rowIndex, $row["nama"]);
    $sheet->setCellValueExplicit("C" . $rowIndex, (string)$row["id_pasien"], DataType::TYPE_STRING);
    $sheet->setCellValue("D" . $rowIndex, $row["gender"]);
    $sheet->setCellValue("E" . $rowIndex, $row["tujuan"]);
    $sheet->setCellValue("F" . $rowIndex, $row["pembayaran"]);
    $sheet->setCellValue("G" . $rowIndex, labelPriorityRincian($row["priority"]));
    $sheet->setCellValue("H" . $rowIndex, $row["status"]);
    $sheet->setCellValue("I" . $rowIndex, $label_datetime_diminta);
    $sheet->setCellValue("J" . $rowIndex, $label_datetime_hasil);
    $sheet->setCellValue("K" . $rowIndex, $durasi);

    $rowIndex++;
    $no++;
}

$stmt->close();

$lastDataRow = max($rowIndex - 1, 5);
$lastRowForStyle = max($lastDataRow, 5);

$sheet->getStyle("A1:K2")->getFont()->setBold(true);
$sheet->getStyle("A1:K2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A4:K5")->getFont()->setBold(true);
$sheet->getStyle("A4:K5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A4:K" . $lastRowForStyle)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle("A6:A" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("C6:C" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("D6:H" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("I6:K" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->getStyle("A4:K" . $lastRowForStyle)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

foreach (range("A", "K") as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$filename = "Rincian_Laporan_Pelayanan_" . preg_replace("/[^A-Za-z0-9_-]/", "_", $keyword) . "_" . date("Ymd_His") . ".xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
