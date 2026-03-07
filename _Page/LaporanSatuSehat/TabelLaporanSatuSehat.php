<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    // Tangkap Tahun Dengan POST
    if(empty($_POST['tahun'])){
        $tahun = date('Y');
    }else{
        $tahun = $_POST['tahun'];
    }
?>
<tr>
    <td class="text-center">1</td>
    <td class="text-left">
        <small><b>Jumlah Total Pelayanan Laboratorium</b></small>
    </td>
    <?php
        // Looping 01 s/d 12
        $total_pelayanan_lab = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d",$i);
            $tahun_bulan = "$tahun-$bulan";
            $jumlah_pelayanan_lab = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE datetime_diminta LIKE '%$tahun_bulan%'"));
            $total_pelayanan_lab = $total_pelayanan_lab + $jumlah_pelayanan_lab;
            echo '
                <td class="text-center">
                    <small>'.$jumlah_pelayanan_lab.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_pelayanan_lab.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center"><small>2</small></td>
    <td class="text-left">
        <small>Jumlah Pasien Tanpa ID IHS</small>
    </td>
    <?php
        // Looping 01 s/d 12
        $total_tanpa_ihs = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d",$i);
            $tahun_bulan = "$tahun-$bulan";
            $jumlah_tanpa_ihs = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE datetime_diminta LIKE '%$tahun_bulan%' AND ihs_pasien=''"));
            $total_tanpa_ihs = $total_tanpa_ihs + $jumlah_tanpa_ihs;
            echo '
                <td class="text-center">
                    <small>'.$jumlah_tanpa_ihs.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_tanpa_ihs.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center"><small>3</small></td>
    <td class="text-left">
        <small>Jumlah Pasien Dengan ID IHS</small>
    </td>
    <?php
        // Looping 01 s/d 12
        $total_dengan_ihs = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d",$i);
            $tahun_bulan = "$tahun-$bulan";
            $jumlah_dengan_ihs = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE datetime_diminta LIKE '%$tahun_bulan%' AND ihs_pasien!=''"));
            $total_dengan_ihs = $total_dengan_ihs + $jumlah_dengan_ihs;
            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_ihs.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_ihs.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">4</td>
    <td class="text-left">
        <small>Jumlah Pasien Tanpa Encounter </small>
    </td>
    <?php
        // Looping 01 s/d 12
        $total_tanpa_encounter = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d",$i);
            $tahun_bulan = "$tahun-$bulan";
            $jumlah_tanpa_encounter = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE datetime_diminta LIKE '%$tahun_bulan%' AND id_encounter=''"));
            $total_tanpa_encounter = $total_tanpa_encounter + $jumlah_tanpa_encounter;
            echo '
                <td class="text-center">
                    <small>'.$jumlah_tanpa_encounter.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_tanpa_encounter.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">5</td>
    <td class="text-left">
        <small>Jumlah Pasien Dengan Encounter</small>
    </td>
    <?php
        // Looping 01 s/d 12
        $total_dengan_encounter = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d",$i);
            $tahun_bulan = "$tahun-$bulan";
            $jumlah_dengan_encounter = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE datetime_diminta LIKE '%$tahun_bulan%' AND id_encounter!=''"));
            $total_dengan_encounter = $total_dengan_encounter + $jumlah_dengan_encounter;
            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_encounter.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_encounter.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">6</td>
    <td class="text-left">
        <small>Jumlah Pasien Tanpa <i>Service Request</i></small>
    </td>
    <?php
        $total_tanpa_sr = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d", $i);
            $tahun_bulan = "$tahun-$bulan";

            // Menggunakan JOIN karena id_service_request ada di tabel rincian
            // dan datetime_diminta ada di tabel laboratorium
            $query = "SELECT a.id_laboratorium 
                      FROM laboratorium a
                      JOIN laboratorium_rincian b ON a.id_laboratorium = b.id_laboratorium
                      WHERE a.datetime_diminta LIKE '%$tahun_bulan%' 
                      AND (b.id_service_request = '' OR b.id_service_request IS NULL)";
            
            $sql_tanpa_sr = mysqli_query($Conn, $query);
            $jumlah_tanpa_sr = mysqli_num_rows($sql_tanpa_sr);
            
            $total_tanpa_sr += $jumlah_tanpa_sr;

            echo '
                <td class="text-center">
                    <small>'.$jumlah_tanpa_sr.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_tanpa_sr.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">7</td>
    <td class="text-left">
        <small>Jumlah Pasien Dengan <i>Service Request</i></small>
    </td>
    <?php
        $total_dengan_sr = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d", $i);
            $tahun_bulan = "$tahun-$bulan";

            // Menggunakan JOIN karena id_service_request ada di tabel rincian
            // dan datetime_diminta ada di tabel laboratorium
            $query = "SELECT a.id_laboratorium 
                      FROM laboratorium a
                      JOIN laboratorium_rincian b ON a.id_laboratorium = b.id_laboratorium
                      WHERE a.datetime_diminta LIKE '%$tahun_bulan%' 
                      AND (b.id_service_request = '' OR b.id_service_request IS NOT NULL)";
            
            $sql_dengan_sr = mysqli_query($Conn, $query);
            $jumlah_dengan_sr = mysqli_num_rows($sql_dengan_sr);
            
            $total_dengan_sr += $jumlah_dengan_sr;

            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_sr.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_sr.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">8</td>
    <td class="text-left">
        <small>Jumlah Pasien Dengan <i>Specimen</i></small>
    </td>
    <?php
        $total_dengan_spesimen = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d", $i);
            $tahun_bulan = "$tahun-$bulan";

            // Menggunakan JOIN karena id_service_request ada di tabel rincian
            // dan datetime_diminta ada di tabel laboratorium
            $query = "SELECT a.id_laboratorium 
                      FROM laboratorium a
                      JOIN laboratorium_spesimen b ON a.id_laboratorium = b.id_laboratorium
                      WHERE a.datetime_diminta LIKE '%$tahun_bulan%' 
                      AND (b.id_speciment = '' OR b.id_speciment IS NOT NULL)";
            
            $sql_dengan_spesimen = mysqli_query($Conn, $query);
            $jumlah_dengan_spesimen = mysqli_num_rows($sql_dengan_spesimen);
            
            $total_dengan_spesimen += $jumlah_dengan_spesimen;

            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_spesimen.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_spesimen.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">9</td>
    <td class="text-left">
        <small>Jumlah Pasien Tanpa <i>Specimen</i></small>
    </td>
    <?php
        $total_dengan_sr = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d", $i);
            $tahun_bulan = "$tahun-$bulan";

            // Menggunakan JOIN karena id_service_request ada di tabel rincian
            // dan datetime_diminta ada di tabel laboratorium
            $query = "SELECT a.id_laboratorium 
                      FROM laboratorium a
                      JOIN laboratorium_rincian b ON a.id_laboratorium = b.id_laboratorium
                      WHERE a.datetime_diminta LIKE '%$tahun_bulan%' 
                      AND (b.id_service_request = '' OR b.id_service_request IS NOT NULL)";
            
            $sql_dengan_sr = mysqli_query($Conn, $query);
            $jumlah_dengan_sr = mysqli_num_rows($sql_dengan_sr);
            
            $total_dengan_sr += $jumlah_dengan_sr;

            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_sr.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_sr.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">8</td>
    <td class="text-left">
        <small>Jumlah Pasien Dengan <i>Diagnostic Report</i></small>
    </td>
    <?php
        $total_dengan_spesimen = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d", $i);
            $tahun_bulan = "$tahun-$bulan";

            // Menggunakan JOIN karena id_service_request ada di tabel rincian
            // dan datetime_diminta ada di tabel laboratorium
            $query = "SELECT a.id_laboratorium 
                      FROM laboratorium a
                      JOIN laboratorium_diagnostic b ON a.id_laboratorium = b.id_laboratorium
                      WHERE a.datetime_diminta LIKE '%$tahun_bulan%' 
                      AND (b.id_laboratorium_diagnostic = '' OR b.id_laboratorium_diagnostic IS NOT NULL)";
            
            $sql_dengan_spesimen = mysqli_query($Conn, $query);
            $jumlah_dengan_spesimen = mysqli_num_rows($sql_dengan_spesimen);
            
            $total_dengan_spesimen += $jumlah_dengan_spesimen;

            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_spesimen.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_spesimen.'</small>
            </td>
        ';
    ?>
</tr>
<tr>
    <td class="text-center">9</td>
    <td class="text-left">
        <small>Jumlah Pasien Tanpa <i>Diagnostic Report</i></small>
    </td>
    <?php
        $total_dengan_sr = 0;
        for($i=1; $i<=12; $i++){
            $bulan       = sprintf("%02d", $i);
            $tahun_bulan = "$tahun-$bulan";

            // Menggunakan JOIN karena id_laboratorium_diagnostic ada di tabel rincian
            // dan datetime_diminta ada di tabel laboratorium
            $query = "SELECT a.id_laboratorium 
                      FROM laboratorium a
                      JOIN laboratorium_diagnostic b ON a.id_laboratorium = b.id_laboratorium
                      WHERE a.datetime_diminta LIKE '%$tahun_bulan%' 
                      AND (b.id_laboratorium_diagnostic = '' OR b.id_laboratorium_diagnostic IS NOT NULL)";
            
            $sql_dengan_sr = mysqli_query($Conn, $query);
            $jumlah_dengan_sr = mysqli_num_rows($sql_dengan_sr);
            
            $total_dengan_sr += $jumlah_dengan_sr;

            echo '
                <td class="text-center">
                    <small>'.$jumlah_dengan_sr.'</small>
                </td>
            ';
        }
        echo '
            <td class="text-center">
                <small>'.$total_dengan_sr.'</small>
            </td>
        ';
    ?>
</tr>
<script>
    var tahun = "<?php echo $tahun; ?>";
    $('#TitleLaporan').html(tahun);
</script>