<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    $bulan = "";
    $tahun = "";
    // Validasi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    if (empty($_POST["periode"])) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-dark">Tidak ada data yang ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }

    $periode = $_POST["periode"];
    if ($periode !== "Tahunan" && $periode !== "Bulanan" && $periode !== "Semua") {
        echo '
            <tr>
                <td colspan="5" class="text-center">
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
                    <td colspan="5" class="text-center">
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
                    <td colspan="5" class="text-center">
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

    /* ===============================
    FILTER TANGGAL
    ================================= */

    $where = " WHERE ld.icd_10_code IS NOT NULL ";
    $params = [];
    $types  = "";

    if ($periode == "Tahunan") {

        $where .= " AND YEAR(l.datetime_diminta) = ? ";
        $params[] = $tahun;
        $types .= "i";

    }
    elseif ($periode == "Bulanan") {

        $where .= " AND YEAR(l.datetime_diminta) = ? ";
        $where .= " AND MONTH(l.datetime_diminta) = ? ";
        $params[] = $tahun;
        $params[] = intval($bulan);
        $types .= "ii";
    }

    /* ===============================
    QUERY UTAMA
    ================================= */

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

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    /* ===============================
    OUTPUT TABEL
    ================================= */

    if ($result->num_rows == 0) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small>Tidak ada data yang ditemukan</small>
                </td>
            </tr>
        ';
        exit;
    }

    $no = 1;
    while ($row = $result->fetch_assoc()) {

        $code    = htmlspecialchars($row['icd_10_code']);
        $display = htmlspecialchars($row['icd_10_display']);
        $system  = htmlspecialchars($row['icd_10_system']);
        $jumlah  = number_format($row['jumlah']);
        echo '
            <tr class="modal_rincian_diagnosis" data-diagnosa="'.$display.'" data-code="'.$code.'" data-periode="'.$periode.'" data-bulan="'.$bulan.'" data-tahun="'.$tahun.'">
                <td class="text-center">'.$no.'</td>
                <td class="text-left">'.$code.'</td>
                <td class="text-left">'.$display.'</td>
                <td class="text-left">'.$system.'</td>
                <td class="text-center">'.$jumlah.'</td>
            </tr>
        ';

        $no++;
    }

    $stmt->close();

    $judul = "<b>LAPORAN DIAGNOSIS LABORATORIUM</b><br>";
    $sub   = "";

    if ($periode == "Semua") {
        $sub = "<small>Semua Periode</small>";
    }

    elseif ($periode == "Tahunan") {
        $sub = "<small>Periode Tahun $tahun</small>";
    }

    elseif ($periode == "Bulanan") {
        $namaBulanFix = $nama_bulan[$bulan] ?? "-";
        $sub = "<small>Periode Bulan $namaBulanFix $tahun</small>";
    }

    echo '
    <script>
        $("#TitleLaporan").html("'.$judul.$sub.'");
    </script>
    ';
    
?>
