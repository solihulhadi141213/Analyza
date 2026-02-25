<?php
    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    function renderSection($Conn, $sectionCode, $sectionTitle, $groupColumn, $whereClause = '') {
        echo '
            <tr>
                <td class="text-center"><b>' . $sectionCode . '</b></td>
                <td class="text-left" colspan="9"><b>' . $sectionTitle . '</b></td>
            </tr>
        ';

        $query = "
            SELECT
                $groupColumn AS nama_group,
                COALESCE(SUM(CASE WHEN tujuan = 'Rajal' THEN 1 ELSE 0 END), 0) AS jumlah_rajal,
                COALESCE(SUM(CASE WHEN tujuan = 'Ranap' THEN 1 ELSE 0 END), 0) AS jumlah_ranap,
                COALESCE(SUM(CASE WHEN pembayaran = 'Umum' THEN 1 ELSE 0 END), 0) AS jumlah_umum,
                COALESCE(SUM(CASE WHEN pembayaran <> 'Umum' THEN 1 ELSE 0 END), 0) AS jumlah_asuransi,
                COALESCE(SUM(CASE WHEN priority = 'routine' THEN 1 ELSE 0 END), 0) AS jumlah_routine,
                COALESCE(SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END), 0) AS jumlah_urgent,
                COALESCE(SUM(CASE WHEN priority = 'stat' THEN 1 ELSE 0 END), 0) AS jumlah_stat
            FROM laboratorium
            $whereClause
            GROUP BY $groupColumn
            ORDER BY $groupColumn ASC
        ";

        $result = mysqli_query($Conn, $query);
        if (!$result) {
            return;
        }

        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $namaGroup = isset($row['nama_group']) ? $row['nama_group'] : '';
            $namaGroup = htmlspecialchars((string) $namaGroup, ENT_QUOTES, 'UTF-8');

            echo '
                <tr>
                    <td class="text-center"></td>
                    <td class="text-center"><small class="text text-dark">' . $no . '</small></td>
                    <td class="text-left"><small class="text text-dark">' . $namaGroup . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_rajal'] . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_ranap'] . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_umum'] . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_asuransi'] . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_routine'] . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_urgent'] . '</small></td>
                    <td class="text-center"><small class="text text-dark">' . (int) $row['jumlah_stat'] . '</small></td>
                </tr>
            ';
            $no++;
        }

        mysqli_free_result($result);
    }

    // A. PERMINTAAN PEMERIKSAAN
    renderSection($Conn, 'A', 'Permintaan Pemeriksaan', 'status');

    // B. DOKTER PENGIRIM
    renderSection($Conn, 'B', 'Dokter Pengirim', 'nama_dokter_pengirim');

    // C. DOKTER PENERIMA
    renderSection(
        $Conn,
        'C',
        'Dokter Penerima',
        'nama_dokter_penerima',
        "WHERE nama_dokter_penerima IS NOT NULL AND nama_dokter_penerima <> ''"
    );

    // D. PETUGAS LABORATORIUM
    renderSection(
        $Conn,
        'D',
        'Petugas Laboratorium',
        'nama_petugas',
        "WHERE nama_petugas IS NOT NULL AND nama_petugas <> ''"
    );
?>
