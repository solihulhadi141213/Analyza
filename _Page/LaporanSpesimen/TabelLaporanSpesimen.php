<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    //Validasi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Menentukan limit per halaman
    $batas = 10;

    // Menangkap halaman
    $page = 1;
    if (!empty($_POST["page"])) {
        $page = (int)$_POST["page"];
        if ($page < 1) {
            $page = 1;
        }
    }
    $posisi = ($page - 1) * $batas;

    // Validasi periode
    if (empty($_POST["periode"])) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Periode Data Tidak Boleh Kosong!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    $periode = validateAndSanitizeInput($_POST["periode"]);
    if ($periode !== "Tahun" && $periode !== "Bulan") {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Periode tidak valid!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Validasi tahun
    if (empty($_POST["tahun"])) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Periode Tahun Tidak Boleh Kosong!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    $tahun = preg_replace("/[^0-9]/", "", $_POST["tahun"]);
    $tahun = substr($tahun, 0, 4);
    if (strlen($tahun) !== 4) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Format Tahun Tidak Valid!</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Bangun keyword periode (prefix datetime)
    $keyword_periode = $tahun;
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
    $title_periode = "PERIODE TAHUN " . $tahun;

    if ($periode === "Bulan") {
        if (empty($_POST["bulan"])) {
            echo '
                <tr>
                    <td colspan="6" class="text-center">
                        <small class="text-danger">Periode Bulan Tidak Boleh Kosong!</small>
                    </td>
                </tr>
                <script>
                    $("#page_info").html("0 / 0");
                    $("#prev_button").prop("disabled", true);
                    $("#next_button").prop("disabled", true);
                </script>
            ';
            exit;
        }

        $bulan = preg_replace("/[^0-9]/", "", $_POST["bulan"]);
        $bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);
        if ((int)$bulan < 1 || (int)$bulan > 12) {
            echo '
                <tr>
                    <td colspan="6" class="text-center">
                        <small class="text-danger">Format Bulan Tidak Valid!</small>
                    </td>
                </tr>
                <script>
                    $("#page_info").html("0 / 0");
                    $("#prev_button").prop("disabled", true);
                    $("#next_button").prop("disabled", true);
                </script>
            ';
            exit;
        }

        $keyword_periode = $tahun . "-" . $bulan;
        $title_periode = "PERIODE " . $nama_bulan[$bulan] . " " . $tahun;
    }

    // Hitung jumlah grup data (sesuai data tabel), bukan jumlah raw record
    $stmt_count = $Conn->prepare("
        SELECT COUNT(*) AS jml_data FROM (
            SELECT 1
            FROM laboratorium_spesimen
            WHERE datetime_spesimen LIKE CONCAT(?, '%')
            GROUP BY nama_spesimen, code_spesimen, display_spesimen, system_spesimen
        ) AS data_grup
    ");

    if (!$stmt_count) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Terjadi kesalahan saat menghitung data.</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    $stmt_count->bind_param("s", $keyword_periode);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $jml_data = (int)$row_count["jml_data"];
    $stmt_count->close();

    if ($jml_data < 1) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak Ada Data Spesimen Yang Ditemukan</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    $JmlHalaman = (int)ceil($jml_data / $batas);
    if ($page > $JmlHalaman) {
        $page = $JmlHalaman;
        $posisi = ($page - 1) * $batas;
    }

    // Ambil data per halaman dengan agregasi tunggal (hindari N+1)
    $stmt_data = $Conn->prepare("
        SELECT
            nama_spesimen,
            code_spesimen,
            display_spesimen,
            system_spesimen,
            COUNT(*) AS jumlah_spesimen
        FROM laboratorium_spesimen
        WHERE datetime_spesimen LIKE CONCAT(?, '%')
        GROUP BY nama_spesimen, code_spesimen, display_spesimen, system_spesimen
        ORDER BY jumlah_spesimen DESC, nama_spesimen ASC, code_spesimen ASC
        LIMIT ?, ?
    ");

    if (!$stmt_data) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Terjadi kesalahan saat menampilkan data.</small>
                </td>
            </tr>
            <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    $stmt_data->bind_param("sii", $keyword_periode, $posisi, $batas);
    $stmt_data->execute();
    $result_data = $stmt_data->get_result();

    $no = 1 + $posisi;
    while ($data = mysqli_fetch_assoc($result_data)) {
        $nama_spesimen    = htmlspecialchars($data["nama_spesimen"]);
        $code_spesimen    = htmlspecialchars($data["code_spesimen"]);
        $display_spesimen = htmlspecialchars($data["display_spesimen"]);
        $system_spesimen  = htmlspecialchars($data["system_spesimen"]);
        $jumlah_spesimen  = (int)$data["jumlah_spesimen"];

        echo '
            <tr class="modal_rincian_spesimen" data-periode="'.$periode.'" data-keyword="'.$keyword_periode.'" data-code_spesimen="'.$code_spesimen.'">
                <td align="center"><small>' . $no . '</small></td>
                <td align="left"><small>' . $nama_spesimen . '</small></td>
                <td align="left"><small><i>' . $display_spesimen . '</i></small></td>
                <td align="left"><small><i>' . $code_spesimen . '</i></small></td>
                <td align="left"><small><i>' . $system_spesimen . '</i></small></td>
                <td align="center"><small><i>' . $jumlah_spesimen . '</i></small></td>
            </tr>
        ';
        $no++;
    }
    $stmt_data->close();
?>
<script>
    var curent_page = <?php echo $page; ?>;
    var page_count = <?php echo $JmlHalaman; ?>;
    $('#page').val(curent_page);
    $('#page_info').html('' + curent_page + ' / ' + page_count + '');
    $('#title_report').html('<b>LAPORAN PELAYANAN BERDASARKAN SPESIMEN</b><br><small><?php echo $title_periode; ?></small>');
    $('#prev_button').prop('disabled', curent_page <= 1);
    $('#next_button').prop('disabled', curent_page >= page_count);
</script>
