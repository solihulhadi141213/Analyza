<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Validasi POST
    if (!isset($_POST['periode']) || empty($_POST['periode'])) {
        exit;
    }

    $periode = $_POST['periode'];

    // Ambil daftar tahun (dipakai Tahunan & Bulanan)
    $tahun_list = "";
    $query = mysqli_query($Conn, "
        SELECT DISTINCT YEAR(datetime_diminta) AS tahun
        FROM laboratorium
        WHERE datetime_diminta IS NOT NULL
        ORDER BY tahun DESC
    ");

    if ($query) {
        while ($data = mysqli_fetch_assoc($query)) {
            $tahun = htmlspecialchars($data['tahun']);
            $tahun_list .= "<option value=\"$tahun\">$tahun</option>";
        }
    }

    // ======================
    // PERIODE TAHUNAN
    // ======================
    if ($periode == "Tahunan") {

        echo '
            <div class="row mb-3">
                <div class="col-12">
                    <label for="tahun"><small>Tahun</small></label>
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="">Pilih</option>
                        '.$tahun_list.'
                    </select>
                </div>
            </div>
        ';
    }

    // ======================
    // PERIODE BULANAN
    // ======================
    elseif ($periode == "Bulanan") {

        // Daftar bulan
        $bulan = [
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

        $bulan_list = "";
        foreach ($bulan as $key => $nama) {
            $bulan_list .= "<option value=\"$key\">$nama</option>";
        }

        echo '
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tahun"><small>Tahun</small></label>
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="">Pilih</option>
                        '.$tahun_list.'
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="bulan"><small>Bulan</small></label>
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="">Pilih Bulan</option>
                        '.$bulan_list.'
                    </select>
                </div>
            </div>
        ';
    }
?>