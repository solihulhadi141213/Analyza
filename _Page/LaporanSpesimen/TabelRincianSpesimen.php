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
                <td colspan="8" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }
    if(empty($_POST['keyword'])){
        echo '
            <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Keyword Informasi Waktu Tidak Boleh Kosong!</small>
                </td>
            </tr>
        ';
        exit;
    }
    if(empty($_POST['code'])){
        echo '
            <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Tidak Ada Data Spesimen yang Dipilih!</small>
                </td>
            </tr>
        ';
        exit;
    }
    if(empty($_POST['periode'])){
        echo '
            <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Informasi Periode Tidak Boleh Kosong!</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Buat variabel dan sanitasi
    $periode = validateAndSanitizeInput($_POST["periode"]);
    $keyword = validateAndSanitizeInput($_POST["keyword"]);
    $code    = validateAndSanitizeInput($_POST["code"]);

    // Buka Nama Spesimen
    $nama_spesimen = GetDetailData($Conn, 'laboratorium_spesimen', 'code_spesimen', $code, 'nama_spesimen');

    // Menentukan Judul Berdasarkan Periode
    if($periode=="Bulan"){
        $no_bulan = date('m', strtotime($keyword));
        $no_tahun = date('Y', strtotime($keyword));
        $nama_bulan = getNamaBulanSingkatZeroPadding($no_bulan);
        $judul = '<b>RINCIAN LAPORAN SPESIMEN <i>'.$nama_spesimen.'</i></b><br>PERIODE '.$nama_bulan.' TAHUN '.$no_tahun.'';
    }else{
        $no_tahun = date('Y', strtotime($keyword));
        $judul = '<b>RINCIAN LAPORAN SPESIMEN <i>'.$nama_spesimen.'</i></b><br>PERIODE TAHUN '.$no_tahun.'';
    }

    // ================= FILTER PERIODE =================
    $filter_periode = "";
    if ($periode == "HariIni") {
        $filter_periode = "DATE(ls.datetime_spesimen) = CURDATE()";
    } elseif ($periode == "BulanIni") {
        $filter_periode = "MONTH(ls.datetime_spesimen) = MONTH(CURDATE()) 
                        AND YEAR(ls.datetime_spesimen) = YEAR(CURDATE())";
    } else {
        $filter_periode = "1=1";
    }

    // ================= QUERY UTAMA (JOIN, TANPA N+1) =================
    $sql = "
        SELECT 
            ls.id_laboratorium_spesimen,
            ls.nama_metode_sample,
            ls.bodysite_nama,
            ls.nama_container,
            ls.quantity_value,
            ls.quantity_unit,
            l.id_pasien,
            l.nama,
            l.datetime_diminta
        FROM laboratorium_spesimen ls
        INNER JOIN laboratorium l 
            ON ls.id_laboratorium = l.id_laboratorium
        WHERE ls.code_spesimen = ?
        AND ls.datetime_spesimen LIKE CONCAT('%', ?, '%')
        AND $filter_periode
        ORDER BY ls.datetime_spesimen DESC
    ";

    $stmt = $Conn->prepare($sql);
    $stmt->bind_param("ss", $code, $keyword);

    if (!$stmt->execute()) {
        echo '<tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">'.$Conn->error.'</small>
                </td>
            </tr>';
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '<tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Tidak Ada Data yang Ditampilkan</small>
                </td>
            </tr>';
        exit;
    }

    // ================= TAMPILKAN DATA =================
    $no = 1;
    while ($row = $result->fetch_assoc()) {

        $nama   = htmlspecialchars($row['nama']);
        $rm     = htmlspecialchars($row['id_pasien']);
        $tgl    = !empty($row['datetime_diminta']) 
                    ? date('d/m/Y H:i', strtotime($row['datetime_diminta'])) 
                    : '-';

        $metode = htmlspecialchars($row['nama_metode_sample']);
        $site   = htmlspecialchars($row['bodysite_nama']);
        $cont   = htmlspecialchars($row['nama_container']);
        $value  = htmlspecialchars($row['quantity_value'].' '.$row['quantity_unit']);

        echo "
            <tr>
                <td class='text-center'><small>{$no}</small></td>
                <td><small>{$nama}</small></td>
                <td><small>{$rm}</small></td>
                <td><small>{$tgl}</small></td>
                <td><small>{$metode}</small></td>
                <td><small>{$site}</small></td>
                <td><small>{$cont}</small></td>
                <td><small>{$value}</small></td>
            </tr>
        ";

        $no++;
    }

    $stmt->close();

    echo '
        <input type="hidden" name="periode" value="'.$periode.'">
        <input type="hidden" name="keyword" value="'.$keyword.'">
        <input type="hidden" name="code" value="'.$code.'">
        <script>
            $("#FormRincianSpesimen").html("'.$judul.'");
        </script>
    ';
?>