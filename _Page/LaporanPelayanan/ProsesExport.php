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

function formatDurasi($detik)
{
    $detik = (int)$detik;
    if ($detik <= 0) {
        return "-";
    }
    if ($detik < 60) {
        return $detik . " Detik";
    }
    if ($detik < 3600) {
        return round($detik / 60, 1) . " Menit";
    }
    if ($detik < 86400) {
        return round($detik / 3600, 1) . " Jam";
    }
    if ($detik < 2592000) {
        return round($detik / 86400, 1) . " Hari";
    }
    if ($detik < 31536000) {
        return round($detik / 2592000, 1) . " Bulan";
    }
    return round($detik / 31536000, 1) . " Tahun";
}

function getStatByKeyword($stmt, $keyword)
{
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return [
        "pemeriksaan" => (int)$row["pemeriksaan"],
        "rajal" => (int)$row["rajal"],
        "ranap" => (int)$row["ranap"],
        "umum" => (int)$row["umum"],
        "bpjs" => (int)$row["bpjs"],
        "biasa" => (int)$row["biasa"],
        "segera" => (int)$row["segera"],
        "gawat" => (int)$row["gawat"],
        "diminta" => (int)$row["diminta"],
        "ditolak" => (int)$row["ditolak"],
        "selesai" => (int)$row["selesai"],
        "rata_detik" => (int)$row["rata_detik"]
    ];
}

function buildRowsAndSummary($Conn, $periode, $tahun, $bulan, $nama_bulan)
{
    $stmt = $Conn->prepare("
        SELECT
            COUNT(id_laboratorium) AS pemeriksaan,
            SUM(CASE WHEN tujuan='Rajal' THEN 1 ELSE 0 END) AS rajal,
            SUM(CASE WHEN tujuan='Ranap' THEN 1 ELSE 0 END) AS ranap,
            SUM(CASE WHEN pembayaran='UMUM' THEN 1 ELSE 0 END) AS umum,
            SUM(CASE WHEN pembayaran!='UMUM' THEN 1 ELSE 0 END) AS bpjs,
            SUM(CASE WHEN priority='routine' THEN 1 ELSE 0 END) AS biasa,
            SUM(CASE WHEN priority='urgent' THEN 1 ELSE 0 END) AS segera,
            SUM(CASE WHEN priority='stat' THEN 1 ELSE 0 END) AS gawat,
            SUM(CASE WHEN status='Diminta' THEN 1 ELSE 0 END) AS diminta,
            SUM(CASE WHEN status='Ditolak' OR status='Dibatalkan' THEN 1 ELSE 0 END) AS ditolak,
            SUM(CASE WHEN status='Selesai' THEN 1 ELSE 0 END) AS selesai,
            AVG(TIMESTAMPDIFF(SECOND, datetime_diminta, datetime_hasil)) AS rata_detik
        FROM laboratorium
        WHERE datetime_diminta LIKE CONCAT(?, '%')
    ");

    if (!$stmt) {
        return [null, "Gagal menyiapkan query data laporan."];
    }

    $rows = [];
    $total = [
        "pemeriksaan" => 0, "rajal" => 0, "ranap" => 0, "umum" => 0, "bpjs" => 0,
        "biasa" => 0, "segera" => 0, "gawat" => 0, "diminta" => 0, "ditolak" => 0, "selesai" => 0
    ];
    $total_durasi_detik = 0;
    $jumlah_periode = 0;
    $jumlah_periode_berdata = 0;
    $no = 1;

    if ($periode === "Tahunan") {
        foreach ($nama_bulan as $key => $label_bulan) {
            $keyword = $tahun . "-" . $key;
            $stat = getStatByKeyword($stmt, $keyword);
            $rows[] = [
                "no" => $no++,
                "label" => $label_bulan . " " . $tahun,
                "keyword" => $keyword,
                "stat" => $stat
            ];

            foreach ($total as $field => $value) {
                $total[$field] += $stat[$field];
            }
            $jumlah_periode++;
            if ($stat["pemeriksaan"] > 0) {
                $total_durasi_detik += $stat["rata_detik"];
                $jumlah_periode_berdata++;
            }
        }

        $judul_periode = "Periode Tahun " . $tahun;
        $label_rata_durasi = "RATA-RATA DURASI TAHUNAN";
    } else {
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);
        for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
            $tanggal = str_pad((string)$hari, 2, "0", STR_PAD_LEFT);
            $keyword = $tahun . "-" . $bulan . "-" . $tanggal;
            $stat = getStatByKeyword($stmt, $keyword);
            $rows[] = [
                "no" => $no++,
                "label" => $tanggal . " " . $nama_bulan[$bulan] . " " . $tahun,
                "keyword" => $keyword,
                "stat" => $stat
            ];

            foreach ($total as $field => $value) {
                $total[$field] += $stat[$field];
            }
            $jumlah_periode++;
            if ($stat["pemeriksaan"] > 0) {
                $total_durasi_detik += $stat["rata_detik"];
                $jumlah_periode_berdata++;
            }
        }

        $judul_periode = "Periode Bulan " . $nama_bulan[$bulan] . " " . $tahun;
        $label_rata_durasi = "RATA-RATA DURASI BULANAN";
    }

    $stmt->close();

    $rata = [];
    foreach ($total as $field => $value) {
        $rata[$field] = $jumlah_periode > 0 ? round($value / $jumlah_periode, 1) : 0;
    }
    $rata_durasi_detik = $jumlah_periode_berdata > 0 ? ($total_durasi_detik / $jumlah_periode_berdata) : 0;

    return [[
        "rows" => $rows,
        "total" => $total,
        "rata" => $rata,
        "rata_durasi" => formatDurasi($rata_durasi_detik),
        "judul_periode" => $judul_periode,
        "label_rata_durasi" => $label_rata_durasi
    ], null];
}

if (empty($SessionIdAccess)) {
    echo "Sesi Akses Sudah Berakhir! Silahkan Login Ulang!";
    exit;
}

$periode = isset($_GET["periode"]) ? trim($_GET["periode"]) : "";
$format_data = isset($_GET["format_data"]) ? trim($_GET["format_data"]) : "";
$tahun = isset($_GET["tahun"]) ? preg_replace("/[^0-9]/", "", $_GET["tahun"]) : "";
$bulan = isset($_GET["bulan"]) ? preg_replace("/[^0-9]/", "", $_GET["bulan"]) : "";
$bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);

if ($periode !== "Tahunan" && $periode !== "Bulanan") {
    echo "Periode data tidak valid!";
    exit;
}
if ($format_data !== "PDF" && $format_data !== "Excel") {
    echo "Format data tidak valid!";
    exit;
}
if (empty($tahun)) {
    echo "Pilih Tahun Data Terlebih Dulu!";
    exit;
}

$nama_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April",
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus",
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

if ($periode === "Bulanan" && !array_key_exists($bulan, $nama_bulan)) {
    echo "Pilih Periode Bulan Terlebih Dulu!";
    exit;
}

list($laporan, $error) = buildRowsAndSummary($Conn, $periode, $tahun, $bulan, $nama_bulan);
if (!empty($error)) {
    echo $error;
    exit;
}

$filename_base = "Laporan_Pelayanan_" . $periode . "_" . $tahun;
if ($periode === "Bulanan") {
    $filename_base .= "_" . $bulan;
}
$filename_base .= "_" . date("Ymd_His");

if ($format_data === "PDF") {
    if (!class_exists("\\Mpdf\\Mpdf")) {
        echo "Pustaka mPDF belum terpasang. Jalankan: composer require mpdf/mpdf";
        exit;
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
            td.text-right{text-align:right;}
            tr.bg-light td{background:#f8f9fa;}
            tr.bg-warning td{background:#fff3cd;}
        </style>
        <h3>LAPORAN PELAYANAN</h3>
        <p class="sub">' . htmlspecialchars($laporan["judul_periode"]) . '</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Bulan / Tanggal</th>
                    <th>Pemeriksaan</th>
                    <th>Rajal</th>
                    <th>Ranap</th>
                    <th>UMUM</th>
                    <th>ASRN</th>
                    <th>Biasa</th>
                    <th>Segera</th>
                    <th>Gawat</th>
                    <th>Diminta</th>
                    <th>Batal</th>
                    <th>Selesai</th>
                    <th>Durasi</th>
                </tr>
            </thead>
            <tbody>
    ';

    foreach ($laporan["rows"] as $row) {
        $s = $row["stat"];
        $html .= '
            <tr>
                <td>' . $row["no"] . '</td>
                <td class="text-left">' . htmlspecialchars($row["label"]) . '</td>
                <td>' . $s["pemeriksaan"] . '</td>
                <td>' . $s["rajal"] . '</td>
                <td>' . $s["ranap"] . '</td>
                <td>' . $s["umum"] . '</td>
                <td>' . $s["bpjs"] . '</td>
                <td>' . $s["biasa"] . '</td>
                <td>' . $s["segera"] . '</td>
                <td>' . $s["gawat"] . '</td>
                <td>' . $s["diminta"] . '</td>
                <td>' . $s["ditolak"] . '</td>
                <td>' . $s["selesai"] . '</td>
                <td>' . formatDurasi($s["rata_detik"]) . '</td>
            </tr>
        ';
    }

    $html .= '
        <tr class="bg-light">
            <td colspan="2" class="text-right"><b>JUMLAH</b></td>
            <td><b>' . $laporan["total"]["pemeriksaan"] . '</b></td>
            <td><b>' . $laporan["total"]["rajal"] . '</b></td>
            <td><b>' . $laporan["total"]["ranap"] . '</b></td>
            <td><b>' . $laporan["total"]["umum"] . '</b></td>
            <td><b>' . $laporan["total"]["bpjs"] . '</b></td>
            <td><b>' . $laporan["total"]["biasa"] . '</b></td>
            <td><b>' . $laporan["total"]["segera"] . '</b></td>
            <td><b>' . $laporan["total"]["gawat"] . '</b></td>
            <td><b>' . $laporan["total"]["diminta"] . '</b></td>
            <td><b>' . $laporan["total"]["ditolak"] . '</b></td>
            <td><b>' . $laporan["total"]["selesai"] . '</b></td>
            <td><b>-</b></td>
        </tr>
        <tr class="bg-light">
            <td colspan="2" class="text-right"><b>RATA-RATA</b></td>
            <td><b>' . $laporan["rata"]["pemeriksaan"] . '</b></td>
            <td><b>' . $laporan["rata"]["rajal"] . '</b></td>
            <td><b>' . $laporan["rata"]["ranap"] . '</b></td>
            <td><b>' . $laporan["rata"]["umum"] . '</b></td>
            <td><b>' . $laporan["rata"]["bpjs"] . '</b></td>
            <td><b>' . $laporan["rata"]["biasa"] . '</b></td>
            <td><b>' . $laporan["rata"]["segera"] . '</b></td>
            <td><b>' . $laporan["rata"]["gawat"] . '</b></td>
            <td><b>' . $laporan["rata"]["diminta"] . '</b></td>
            <td><b>' . $laporan["rata"]["ditolak"] . '</b></td>
            <td><b>' . $laporan["rata"]["selesai"] . '</b></td>
            <td><b>-</b></td>
        </tr>
        <tr class="bg-warning">
            <td colspan="13" class="text-right"><b>' . $laporan["label_rata_durasi"] . '</b></td>
            <td><b>' . $laporan["rata_durasi"] . '</b></td>
        </tr>
        </tbody></table>
    ';

    try {
        $mpdf = new Mpdf(["format" => "A4-L"]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename_base . ".pdf", "D");
    } catch (Throwable $e) {
        echo "Gagal membuat file PDF: " . $e->getMessage();
    }
    exit;
}

if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\Spreadsheet")) {
    echo "Pustaka PhpSpreadsheet belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet";
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Laporan Pelayanan");

$sheet->mergeCells("A1:N1");
$sheet->setCellValue("A1", "LAPORAN PELAYANAN");
$sheet->mergeCells("A2:N2");
$sheet->setCellValue("A2", $laporan["judul_periode"]);
$sheet->getStyle("A1:N2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A1:N2")->getFont()->setBold(true);

$headers = [
    "A4" => "No", "B4" => "Bulan / Tanggal", "C4" => "Pemeriksaan", "D4" => "Rajal",
    "E4" => "Ranap", "F4" => "UMUM", "G4" => "ASRN", "H4" => "Biasa", "I4" => "Segera",
    "J4" => "Gawat", "K4" => "Diminta", "L4" => "Batal", "M4" => "Selesai", "N4" => "Durasi"
];
foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}
$sheet->getStyle("A4:N4")->getFont()->setBold(true);
$sheet->getStyle("A4:N4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$rowIndex = 5;
foreach ($laporan["rows"] as $row) {
    $s = $row["stat"];
    $sheet->setCellValue("A" . $rowIndex, $row["no"]);
    $sheet->setCellValue("B" . $rowIndex, $row["label"]);
    $sheet->setCellValue("C" . $rowIndex, $s["pemeriksaan"]);
    $sheet->setCellValue("D" . $rowIndex, $s["rajal"]);
    $sheet->setCellValue("E" . $rowIndex, $s["ranap"]);
    $sheet->setCellValue("F" . $rowIndex, $s["umum"]);
    $sheet->setCellValue("G" . $rowIndex, $s["bpjs"]);
    $sheet->setCellValue("H" . $rowIndex, $s["biasa"]);
    $sheet->setCellValue("I" . $rowIndex, $s["segera"]);
    $sheet->setCellValue("J" . $rowIndex, $s["gawat"]);
    $sheet->setCellValue("K" . $rowIndex, $s["diminta"]);
    $sheet->setCellValue("L" . $rowIndex, $s["ditolak"]);
    $sheet->setCellValue("M" . $rowIndex, $s["selesai"]);
    $sheet->setCellValue("N" . $rowIndex, formatDurasi($s["rata_detik"]));
    $rowIndex++;
}

$sheet->mergeCells("A" . $rowIndex . ":B" . $rowIndex);
$sheet->setCellValue("A" . $rowIndex, "JUMLAH");
$sheet->setCellValue("C" . $rowIndex, $laporan["total"]["pemeriksaan"]);
$sheet->setCellValue("D" . $rowIndex, $laporan["total"]["rajal"]);
$sheet->setCellValue("E" . $rowIndex, $laporan["total"]["ranap"]);
$sheet->setCellValue("F" . $rowIndex, $laporan["total"]["umum"]);
$sheet->setCellValue("G" . $rowIndex, $laporan["total"]["bpjs"]);
$sheet->setCellValue("H" . $rowIndex, $laporan["total"]["biasa"]);
$sheet->setCellValue("I" . $rowIndex, $laporan["total"]["segera"]);
$sheet->setCellValue("J" . $rowIndex, $laporan["total"]["gawat"]);
$sheet->setCellValue("K" . $rowIndex, $laporan["total"]["diminta"]);
$sheet->setCellValue("L" . $rowIndex, $laporan["total"]["ditolak"]);
$sheet->setCellValue("M" . $rowIndex, $laporan["total"]["selesai"]);
$sheet->setCellValue("N" . $rowIndex, "-");
$sheet->getStyle("A" . $rowIndex . ":N" . $rowIndex)->getFont()->setBold(true);
$rowIndex++;

$sheet->mergeCells("A" . $rowIndex . ":B" . $rowIndex);
$sheet->setCellValue("A" . $rowIndex, "RATA-RATA");
$sheet->setCellValue("C" . $rowIndex, $laporan["rata"]["pemeriksaan"]);
$sheet->setCellValue("D" . $rowIndex, $laporan["rata"]["rajal"]);
$sheet->setCellValue("E" . $rowIndex, $laporan["rata"]["ranap"]);
$sheet->setCellValue("F" . $rowIndex, $laporan["rata"]["umum"]);
$sheet->setCellValue("G" . $rowIndex, $laporan["rata"]["bpjs"]);
$sheet->setCellValue("H" . $rowIndex, $laporan["rata"]["biasa"]);
$sheet->setCellValue("I" . $rowIndex, $laporan["rata"]["segera"]);
$sheet->setCellValue("J" . $rowIndex, $laporan["rata"]["gawat"]);
$sheet->setCellValue("K" . $rowIndex, $laporan["rata"]["diminta"]);
$sheet->setCellValue("L" . $rowIndex, $laporan["rata"]["ditolak"]);
$sheet->setCellValue("M" . $rowIndex, $laporan["rata"]["selesai"]);
$sheet->setCellValue("N" . $rowIndex, "-");
$sheet->getStyle("A" . $rowIndex . ":N" . $rowIndex)->getFont()->setBold(true);
$rowIndex++;

$sheet->mergeCells("A" . $rowIndex . ":M" . $rowIndex);
$sheet->setCellValue("A" . $rowIndex, $laporan["label_rata_durasi"]);
$sheet->setCellValue("N" . $rowIndex, $laporan["rata_durasi"]);
$sheet->getStyle("A" . $rowIndex . ":N" . $rowIndex)->getFont()->setBold(true);

$sheet->getStyle("A4:N" . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("B5:B" . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle("A4:N" . $rowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

foreach (range("A", "N") as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Disposition: attachment; filename="' . $filename_base . '.xlsx"');
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
