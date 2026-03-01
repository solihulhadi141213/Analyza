<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    function escapeHtml($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
    }

    function outputError($message)
    {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Export Laporan Spesimen</title>
        </head>
        <body>
            <p style="color:#b91c1c;">' . escapeHtml($message) . '</p>
        </body>
        </html>';
        exit;
    }

    // Validasi akses
    if (empty($SessionIdAccess)) {
        outputError("Sesi Akses Sudah Berakhir! Silahkan Login Ulang!");
    }

    // Tangkap input
    $periode = isset($_POST["periode"]) ? validateAndSanitizeInput($_POST["periode"]) : "";
    $tahun_raw = isset($_POST["tahun"]) ? $_POST["tahun"] : "";
    $bulan_raw = isset($_POST["bulan"]) ? $_POST["bulan"] : "";
    $format_file = isset($_POST["format_file"]) ? validateAndSanitizeInput($_POST["format_file"]) : "";

    if ($periode !== "Tahun" && $periode !== "Bulan") {
        outputError("Periode data tidak valid.");
    }

    $tahun = preg_replace("/[^0-9]/", "", $tahun_raw);
    $tahun = substr($tahun, 0, 4);
    if (strlen($tahun) !== 4) {
        outputError("Periode tahun tidak valid.");
    }

    $nama_bulan = [
        "01" => "JANUARI",
        "02" => "FEBRUARI",
        "03" => "MARET",
        "04" => "APRIL",
        "05" => "MEI",
        "06" => "JUNI",
        "07" => "JULI",
        "08" => "AGUSTUS",
        "09" => "SEPTEMBER",
        "10" => "OKTOBER",
        "11" => "NOVEMBER",
        "12" => "DESEMBER"
    ];

    $keyword_periode = $tahun;
    $title_periode = "PERIODE TAHUN " . $tahun;
    if ($periode === "Bulan") {
        $bulan = preg_replace("/[^0-9]/", "", $bulan_raw);
        $bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);
        if (!array_key_exists($bulan, $nama_bulan)) {
            outputError("Periode bulan tidak valid.");
        }
        $keyword_periode = $tahun . "-" . $bulan;
        $title_periode = "PERIODE " . $nama_bulan[$bulan] . " " . $tahun;
    }

    if ($format_file !== "HTML" && $format_file !== "PDF" && $format_file !== "Excel") {
        outputError("Format file tidak valid.");
    }

    // Query tanpa paging, urut jumlah terbanyak
    $stmt = $Conn->prepare("
        SELECT
            nama_spesimen,
            display_spesimen,
            code_spesimen,
            system_spesimen,
            COUNT(*) AS jumlah_spesimen
        FROM laboratorium_spesimen
        WHERE datetime_spesimen LIKE CONCAT(?, '%')
        GROUP BY nama_spesimen, display_spesimen, code_spesimen, system_spesimen
        ORDER BY jumlah_spesimen DESC, nama_spesimen ASC, code_spesimen ASC
    ");

    if (!$stmt) {
        outputError("Gagal menyiapkan query export.");
    }

    $stmt->bind_param("s", $keyword_periode);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($data = mysqli_fetch_assoc($result)) {
        $rows[] = [
            "nama_spesimen" => $data["nama_spesimen"],
            "display_spesimen" => $data["display_spesimen"],
            "code_spesimen" => $data["code_spesimen"],
            "system_spesimen" => $data["system_spesimen"],
            "jumlah_spesimen" => (int)$data["jumlah_spesimen"]
        ];
    }
    $stmt->close();

    if (count($rows) < 1) {
        outputError("Tidak ada data spesimen untuk periode yang dipilih.");
    }

    $filename_base = "Laporan_Spesimen_" . $periode . "_" . $keyword_periode . "_" . date("Ymd_His");

    // Export CSV (Excel)
    if ($format_file === "Excel") {
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"" . $filename_base . ".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

        fputcsv($output, ["LAPORAN PELAYANAN BERDASARKAN SPESIMEN"]);
        fputcsv($output, [$title_periode]);
        fputcsv($output, []);
        fputcsv($output, ["No", "Nama Spesimen", "Display", "Code", "System", "Jumlah"]);

        $no = 1;
        foreach ($rows as $row) {
            fputcsv($output, [
                $no,
                $row["nama_spesimen"],
                $row["display_spesimen"],
                $row["code_spesimen"],
                $row["system_spesimen"],
                $row["jumlah_spesimen"]
            ]);
            $no++;
        }
        fclose($output);
        exit;
    }

    // HTML untuk tampilan langsung (dan mode PDF print-friendly)
    $is_pdf_mode = ($format_file === "PDF");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escapeHtml($filename_base); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #1f2937; }
        .title { text-align: center; margin-bottom: 14px; }
        .title h2 { margin: 0; font-size: 20px; }
        .title p { margin: 3px 0 0; font-size: 13px; font-weight: bold; }
        .note { margin-bottom: 12px; padding: 8px 10px; background: #fef3c7; border: 1px solid #f59e0b; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #6b7280; padding: 6px; font-size: 12px; }
        th { background: #e5e7eb; text-align: center; }
        td { vertical-align: top; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <?php if ($is_pdf_mode) { ?>
        <div class="note no-print">
            Mode PDF: gunakan menu Print browser lalu pilih <b>Save as PDF</b>.
        </div>
    <?php } ?>

    <div class="title">
        <h2>LAPORAN PELAYANAN BERDASARKAN SPESIMEN</h2>
        <p><?php echo escapeHtml($title_periode); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama Spesimen</th>
                <th>Display</th>
                <th>Code</th>
                <th>System</th>
                <th style="width: 100px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($rows as $row) {
                    echo '
                        <tr>
                            <td class="text-center">' . $no . '</td>
                            <td class="text-left">' . escapeHtml($row["nama_spesimen"]) . '</td>
                            <td class="text-left">' . escapeHtml($row["display_spesimen"]) . '</td>
                            <td class="text-left">' . escapeHtml($row["code_spesimen"]) . '</td>
                            <td class="text-left">' . escapeHtml($row["system_spesimen"]) . '</td>
                            <td class="text-center">' . $row["jumlah_spesimen"] . '</td>
                        </tr>
                    ';
                    $no++;
                }
            ?>
        </tbody>
    </table>

    <?php if ($is_pdf_mode) { ?>
    <script>
        window.print();
    </script>
    <?php } ?>
</body>
</html>
