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

function stopExportRincian($message)
{
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Export Rincian Diagnosis</title></head><body>';
    echo '<p style="color:#b91c1c;">' . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . '</p>';
    echo '</body></html>';
    exit;
}

function labelPriorityDiagnosis($priority)
{
    $priority = strtolower((string)$priority);
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
    stopExportRincian("Sesi Akses Sudah Berakhir! Silahkan Login Ulang!");
}

$code = isset($_GET["code"]) ? trim($_GET["code"]) : "";
$periode = isset($_GET["periode"]) ? trim($_GET["periode"]) : "";
$bulan = isset($_GET["bulan"]) ? preg_replace("/[^0-9]/", "", $_GET["bulan"]) : "";
$tahun = isset($_GET["tahun"]) ? preg_replace("/[^0-9]/", "", $_GET["tahun"]) : "";
$bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);

if ($code === "") {
    stopExportRincian("Kode diagnosis tidak boleh kosong.");
}
if ($periode === "") {
    stopExportRincian("Periode laporan tidak boleh kosong.");
}

if ($periode !== "Semua" && $periode !== "Tahunan" && $periode !== "Bulanan") {
    stopExportRincian("Periode laporan tidak valid.");
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
    stopExportRincian("Tahun laporan tidak valid.");
}

if ($periode === "Bulanan" && !array_key_exists($bulan, $nama_bulan)) {
    stopExportRincian("Bulan laporan tidak valid.");
}

if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\Spreadsheet")) {
    stopExportRincian("Pustaka PhpSpreadsheet belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet");
}

$where = " WHERE ld.icd_10_code = ? ";
$params = [$code];
$types = "s";

if ($periode === "Tahunan") {
    $where .= " AND YEAR(l.datetime_diminta) = ? ";
    $params[] = (int)$tahun;
    $types .= "i";
} elseif ($periode === "Bulanan") {
    $where .= " AND YEAR(l.datetime_diminta) = ? AND MONTH(l.datetime_diminta) = ? ";
    $params[] = (int)$tahun;
    $params[] = (int)$bulan;
    $types .= "ii";
}

$sql = "
    SELECT DISTINCT
        l.id_pasien,
        l.nama,
        l.gender,
        l.tujuan,
        l.pembayaran,
        l.priority,
        l.status,
        l.datetime_diminta,
        ld.icd_10_display
    FROM laboratorium l
    INNER JOIN laboratorium_diagnostic ld
        ON l.id_laboratorium = ld.id_laboratorium
    $where
    ORDER BY l.datetime_diminta DESC
";

$stmt = $Conn->prepare($sql);
if (!$stmt) {
    stopExportRincian("Gagal menyiapkan query export rincian.");
}

$stmt->bind_param($types, ...$params);
if (!$stmt->execute()) {
    $stmt->close();
    stopExportRincian("Gagal mengambil data export rincian.");
}

$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

if (count($rows) < 1) {
    stopExportRincian("Data rincian diagnosis tidak ditemukan untuk filter yang dipilih.");
}

$diagnosa = "-";
if (!empty($rows[0]["icd_10_display"])) {
    $diagnosa = (string)$rows[0]["icd_10_display"];
}

$label_periode = "Semua Periode";
if ($periode === "Tahunan") {
    $label_periode = "Periode Tahun " . $tahun;
} elseif ($periode === "Bulanan") {
    $label_periode = "Periode Bulan " . $nama_bulan[$bulan] . " " . $tahun;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Rincian Diagnosis");

$sheet->mergeCells("A1:J1");
$sheet->setCellValue("A1", "RINCIAN PELAYANAN DIAGNOSIS LABORATORIUM");
$sheet->mergeCells("A2:J2");
$sheet->setCellValue("A2", $code . " - " . $diagnosa);
$sheet->mergeCells("A3:J3");
$sheet->setCellValue("A3", $label_periode);

$sheet->setCellValue("A5", "No");
$sheet->setCellValue("B5", "Nama Pasien");
$sheet->setCellValue("C5", "No.RM");
$sheet->setCellValue("D5", "Gender");
$sheet->setCellValue("E5", "Tujuan");
$sheet->setCellValue("F5", "Pembayaran");
$sheet->setCellValue("G5", "Priority");
$sheet->setCellValue("H5", "Status");
$sheet->setCellValue("I5", "Tanggal/Jam Diminta");
$sheet->setCellValue("J5", "Diagnosa");

$rowIndex = 6;
$no = 1;
foreach ($rows as $row) {
    $label_datetime = "-";
    if (!empty($row["datetime_diminta"])) {
        $label_datetime = date("d/m/Y H:i", strtotime($row["datetime_diminta"]));
    }

    $sheet->setCellValue("A" . $rowIndex, $no);
    $sheet->setCellValue("B" . $rowIndex, (string)$row["nama"]);
    $sheet->setCellValueExplicit("C" . $rowIndex, (string)$row["id_pasien"], DataType::TYPE_STRING);
    $sheet->setCellValue("D" . $rowIndex, (string)$row["gender"]);
    $sheet->setCellValue("E" . $rowIndex, (string)$row["tujuan"]);
    $sheet->setCellValue("F" . $rowIndex, (string)$row["pembayaran"]);
    $sheet->setCellValue("G" . $rowIndex, labelPriorityDiagnosis($row["priority"]));
    $sheet->setCellValue("H" . $rowIndex, (string)$row["status"]);
    $sheet->setCellValue("I" . $rowIndex, $label_datetime);
    $sheet->setCellValue("J" . $rowIndex, (string)$row["icd_10_display"]);

    $rowIndex++;
    $no++;
}

$lastDataRow = $rowIndex - 1;
$lastRowForStyle = max($lastDataRow, 5);

$sheet->getStyle("A1:J3")->getFont()->setBold(true);
$sheet->getStyle("A1:J3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A5:J5")->getFont()->setBold(true);
$sheet->getStyle("A5:J5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A5:J" . $lastRowForStyle)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheet->getStyle("A5:J" . $lastRowForStyle)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

if ($lastDataRow >= 6) {
    $sheet->getStyle("A6:A" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C6:I" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B6:B" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("J6:J" . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
}

foreach (range("A", "J") as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$filename = "Rincian_Diagnosis_" . preg_replace("/[^A-Za-z0-9_-]/", "_", $code) . "_" . $periode;
if ($periode === "Tahunan") {
    $filename .= "_" . $tahun;
} elseif ($periode === "Bulanan") {
    $filename .= "_" . $tahun . "_" . $bulan;
}
$filename .= "_" . date("Ymd_His") . ".xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
