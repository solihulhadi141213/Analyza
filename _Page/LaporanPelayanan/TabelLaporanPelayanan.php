<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    // helper format durasi
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

    function tampilkanBarisRingkasan($label, $data, $durasi = "-")
    {
        echo '
            <tr class="bg-light">
                <td colspan="2" class="text-right"><b>' . $label . '</b></td>
                <td class="text-center"><b>' . $data["pemeriksaan"] . '</b></td>
                <td class="text-center"><b>' . $data["rajal"] . '</b></td>
                <td class="text-center"><b>' . $data["ranap"] . '</b></td>
                <td class="text-center"><b>' . $data["umum"] . '</b></td>
                <td class="text-center"><b>' . $data["bpjs"] . '</b></td>
                <td class="text-center"><b>' . $data["biasa"] . '</b></td>
                <td class="text-center"><b>' . $data["segera"] . '</b></td>
                <td class="text-center"><b>' . $data["gawat"] . '</b></td>
                <td class="text-center"><b>' . $data["diminta"] . '</b></td>
                <td class="text-center"><b>' . $data["ditolak"] . '</b></td>
                <td class="text-center"><b>' . $data["selesai"] . '</b></td>
                <td class="text-center"><b>' . $durasi . '</b></td>
            </tr>
        ';
    }

    // Validasi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <tr>
                <td colspan="14" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    if (empty($_POST["periode"])) {
        echo '
            <tr>
                <td colspan="14" class="text-center">
                    <small class="text-dark">Tidak ada data yang ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }

    $periode = $_POST["periode"];
    if ($periode !== "Tahunan" && $periode !== "Bulanan") {
        echo '
            <tr>
                <td colspan="14" class="text-center">
                    <small class="text-dark">Periode tidak valid.</small>
                </td>
            </tr>
        ';
        exit;
    }

    if ($periode == "Tahunan" || $periode == "Bulanan") {
        if (empty($_POST["tahun"])) {
            echo '
                <tr>
                    <td colspan="14" class="text-center">
                        <small class="text-dark">Pilih Periode <b>Tahun</b> Terlebih Dulu!</small>
                    </td>
                </tr>
            ';
            exit;
        }
        $tahun = preg_replace("/[^0-9]/", "", $_POST["tahun"]);
    }

    if ($periode == "Bulanan") {
        if (empty($_POST["bulan"])) {
            echo '
                <tr>
                    <td colspan="14" class="text-center">
                        <small class="text-dark">Pilih Periode <b>Bulan</b> Terlebih Dulu!</small>
                    </td>
                </tr>
            ';
            exit;
        }
        $bulan = preg_replace("/[^0-9]/", "", $_POST["bulan"]);
        $bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);
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
        echo '
            <tr>
                <td colspan="14" class="text-center">
                    <small class="text-danger">Gagal memproses data laporan.</small>
                </td>
            </tr>
        ';
        exit;
    }

    $total = [
        "pemeriksaan" => 0,
        "rajal" => 0,
        "ranap" => 0,
        "umum" => 0,
        "bpjs" => 0,
        "biasa" => 0,
        "segera" => 0,
        "gawat" => 0,
        "diminta" => 0,
        "ditolak" => 0,
        "selesai" => 0
    ];
    $total_durasi_detik = 0;
    $jumlah_periode = 0;
    $jumlah_periode_berdata = 0;
    $no = 1;

    if ($periode == "Tahunan") {
        foreach ($nama_bulan as $key => $label_bulan) {
            $keyword = $tahun . "-" . $key;
            $stat = getStatByKeyword($stmt, $keyword);

            $total["pemeriksaan"] += $stat["pemeriksaan"];
            $total["rajal"] += $stat["rajal"];
            $total["ranap"] += $stat["ranap"];
            $total["umum"] += $stat["umum"];
            $total["bpjs"] += $stat["bpjs"];
            $total["biasa"] += $stat["biasa"];
            $total["segera"] += $stat["segera"];
            $total["gawat"] += $stat["gawat"];
            $total["diminta"] += $stat["diminta"];
            $total["ditolak"] += $stat["ditolak"];
            $total["selesai"] += $stat["selesai"];
            $jumlah_periode++;
            if ($stat["pemeriksaan"] > 0) {
                $total_durasi_detik += $stat["rata_detik"];
                $jumlah_periode_berdata++;
            }

            echo '
                <tr class="modal_detail_laporan" data-keyword="' . $keyword . '" data-periode="' . $periode . '">
                    <td class="text-center"><small>' . $no . '</small></td>
                    <td class="text-left"><small>' . $label_bulan . ' ' . $tahun . '</small></td>
                    <td class="text-center"><small>' . $stat["pemeriksaan"] . '</small></td>
                    <td class="text-center"><small>' . $stat["rajal"] . '</small></td>
                    <td class="text-center"><small>' . $stat["ranap"] . '</small></td>
                    <td class="text-center"><small>' . $stat["umum"] . '</small></td>
                    <td class="text-center"><small>' . $stat["bpjs"] . '</small></td>
                    <td class="text-center"><small>' . $stat["biasa"] . '</small></td>
                    <td class="text-center"><small>' . $stat["segera"] . '</small></td>
                    <td class="text-center"><small>' . $stat["gawat"] . '</small></td>
                    <td class="text-center"><small>' . $stat["diminta"] . '</small></td>
                    <td class="text-center"><small>' . $stat["ditolak"] . '</small></td>
                    <td class="text-center"><small>' . $stat["selesai"] . '</small></td>
                    <td class="text-center"><small>' . formatDurasi($stat["rata_detik"]) . '</small></td>
                </tr>
            ';

            $no++;
        }

        tampilkanBarisRingkasan("JUMLAH", $total, "-");

        $rata = [
            "pemeriksaan" => round($total["pemeriksaan"] / $jumlah_periode, 1),
            "rajal" => round($total["rajal"] / $jumlah_periode, 1),
            "ranap" => round($total["ranap"] / $jumlah_periode, 1),
            "umum" => round($total["umum"] / $jumlah_periode, 1),
            "bpjs" => round($total["bpjs"] / $jumlah_periode, 1),
            "biasa" => round($total["biasa"] / $jumlah_periode, 1),
            "segera" => round($total["segera"] / $jumlah_periode, 1),
            "gawat" => round($total["gawat"] / $jumlah_periode, 1),
            "diminta" => round($total["diminta"] / $jumlah_periode, 1),
            "ditolak" => round($total["ditolak"] / $jumlah_periode, 1),
            "selesai" => round($total["selesai"] / $jumlah_periode, 1)
        ];

        tampilkanBarisRingkasan("RATA-RATA", $rata, "-");

        $rata_tahun_detik = $jumlah_periode_berdata > 0 ? ($total_durasi_detik / $jumlah_periode_berdata) : 0;
        echo '
            <tr class="bg-warning">
                <td colspan="13" class="text-right"><b>RATA-RATA DURASI TAHUNAN</b></td>
                <td class="text-center"><b>' . formatDurasi($rata_tahun_detik) . '</b></td>
            </tr>
        ';

        echo '
            <script>
                $("#TitleLaporan").html("<b>LAPORAN PELAYANAN</b><br><small>Periode Tahun ' . $tahun . '</small>");
            </script>
        ';
    }

    if ($periode == "Bulanan") {
        if (!array_key_exists($bulan, $nama_bulan)) {
            echo '
                <tr>
                    <td colspan="14" class="text-center">
                        <small class="text-dark">Periode <b>Bulan</b> tidak valid.</small>
                    </td>
                </tr>
            ';
            $stmt->close();
            exit;
        }

        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);

        for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
            $tanggal = str_pad((string)$hari, 2, "0", STR_PAD_LEFT);
            $keyword = $tahun . "-" . $bulan . "-" . $tanggal;
            $stat = getStatByKeyword($stmt, $keyword);

            $total["pemeriksaan"] += $stat["pemeriksaan"];
            $total["rajal"] += $stat["rajal"];
            $total["ranap"] += $stat["ranap"];
            $total["umum"] += $stat["umum"];
            $total["bpjs"] += $stat["bpjs"];
            $total["biasa"] += $stat["biasa"];
            $total["segera"] += $stat["segera"];
            $total["gawat"] += $stat["gawat"];
            $total["diminta"] += $stat["diminta"];
            $total["ditolak"] += $stat["ditolak"];
            $total["selesai"] += $stat["selesai"];
            $jumlah_periode++;
            if ($stat["pemeriksaan"] > 0) {
                $total_durasi_detik += $stat["rata_detik"];
                $jumlah_periode_berdata++;
            }

            echo '
                <tr class="modal_detail_laporan" data-keyword="' . $keyword . '" data-periode="' . $periode . '">
                    <td class="text-center"><small>' . $no . '</small></td>
                    <td class="text-left"><small>' . $tanggal . " " . $nama_bulan[$bulan] . ' ' . $tahun . '</small></td>
                    <td class="text-center"><small>' . $stat["pemeriksaan"] . '</small></td>
                    <td class="text-center"><small>' . $stat["rajal"] . '</small></td>
                    <td class="text-center"><small>' . $stat["ranap"] . '</small></td>
                    <td class="text-center"><small>' . $stat["umum"] . '</small></td>
                    <td class="text-center"><small>' . $stat["bpjs"] . '</small></td>
                    <td class="text-center"><small>' . $stat["biasa"] . '</small></td>
                    <td class="text-center"><small>' . $stat["segera"] . '</small></td>
                    <td class="text-center"><small>' . $stat["gawat"] . '</small></td>
                    <td class="text-center"><small>' . $stat["diminta"] . '</small></td>
                    <td class="text-center"><small>' . $stat["ditolak"] . '</small></td>
                    <td class="text-center"><small>' . $stat["selesai"] . '</small></td>
                    <td class="text-center"><small>' . formatDurasi($stat["rata_detik"]) . '</small></td>
                </tr>
            ';
            $no++;
        }

        tampilkanBarisRingkasan("JUMLAH", $total, "-");

        $rata = [
            "pemeriksaan" => round($total["pemeriksaan"] / $jumlah_periode, 1),
            "rajal" => round($total["rajal"] / $jumlah_periode, 1),
            "ranap" => round($total["ranap"] / $jumlah_periode, 1),
            "umum" => round($total["umum"] / $jumlah_periode, 1),
            "bpjs" => round($total["bpjs"] / $jumlah_periode, 1),
            "biasa" => round($total["biasa"] / $jumlah_periode, 1),
            "segera" => round($total["segera"] / $jumlah_periode, 1),
            "gawat" => round($total["gawat"] / $jumlah_periode, 1),
            "diminta" => round($total["diminta"] / $jumlah_periode, 1),
            "ditolak" => round($total["ditolak"] / $jumlah_periode, 1),
            "selesai" => round($total["selesai"] / $jumlah_periode, 1)
        ];

        tampilkanBarisRingkasan("RATA-RATA", $rata, "-");

        $rata_bulan_detik = $jumlah_periode_berdata > 0 ? ($total_durasi_detik / $jumlah_periode_berdata) : 0;
        echo '
            <tr class="bg-warning">
                <td colspan="13" class="text-right"><b>RATA-RATA DURASI BULANAN</b></td>
                <td class="text-center"><b>' . formatDurasi($rata_bulan_detik) . '</b></td>
            </tr>
        ';

        echo '
            <script>
                $("#TitleLaporan").html("<b>LAPORAN PELAYANAN</b><br><small>Periode Bulan ' . $nama_bulan[$bulan] . " " . $tahun . '</small>");
            </script>
        ';
    }

    $stmt->close();
?>
