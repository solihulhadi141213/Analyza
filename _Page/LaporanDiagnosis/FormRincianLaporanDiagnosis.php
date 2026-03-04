<?php
    // koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    function esc($text)
    {
        return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
    }

    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    if (empty($_POST['code'])) {
        echo '
            <div class="alert alert-danger">
                <small>Kode Diagnosis Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    if (empty($_POST['periode'])) {
        echo '
            <div class="alert alert-danger">
                <small>Periode Laporan Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $code = trim($_POST['code']);
    $diagnosa = trim($_POST['diagnosa'] ?? '');
    $periode = trim($_POST['periode']);
    $bulan = preg_replace("/[^0-9]/", "", $_POST['bulan'] ?? '');
    $tahun = preg_replace("/[^0-9]/", "", $_POST['tahun'] ?? '');
    $bulan = str_pad(substr($bulan, 0, 2), 2, "0", STR_PAD_LEFT);

    if ($periode !== "Semua" && $periode !== "Tahunan" && $periode !== "Bulanan") {
        echo '
            <div class="alert alert-danger">
                <small>Periode laporan tidak valid.</small>
            </div>
        ';
        exit;
    }

    if (($periode === "Tahunan" || $periode === "Bulanan") && strlen($tahun) !== 4) {
        echo '
            <div class="alert alert-danger">
                <small>Tahun laporan tidak valid.</small>
            </div>
        ';
        exit;
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

    if ($periode === "Bulanan" && !array_key_exists($bulan, $nama_bulan)) {
        echo '
            <div class="alert alert-danger">
                <small>Bulan laporan tidak valid.</small>
            </div>
        ';
        exit;
    }

    $label_periode = "Semua Periode";
    if ($periode === "Tahunan") {
        $label_periode = "Periode Tahun " . $tahun;
    } elseif ($periode === "Bulanan") {
        $label_periode = "Periode Bulan " . $nama_bulan[$bulan] . " " . $tahun;
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
            l.id_laboratorium,
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
        echo '
            <div class="alert alert-danger">
                <small>Gagal menyiapkan query rincian.</small>
            </div>
        ';
        exit;
    }

    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        $stmt->close();
        echo '
            <div class="alert alert-danger">
                <small>Gagal mengambil data rincian.</small>
            </div>
        ';
        exit;
    }

    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
?>
<input type="hidden" name="code" value="<?php echo esc($code); ?>">
<input type="hidden" name="periode" value="<?php echo esc($periode); ?>">
<input type="hidden" name="bulan" value="<?php echo esc($bulan); ?>">
<input type="hidden" name="tahun" value="<?php echo esc($tahun); ?>">
<div class="row mb-2">
    <div class="col-12 text-center">
        <h4><b>RINCIAN PELAYANAN DIAGNOSIS LABORATORIUM</b></h4>
        <span class="text text-grayish"><?php echo esc($code . ' - ' . ($diagnosa !== '' ? $diagnosa : '-')); ?></span><br>
        <small><i><?php echo esc($label_periode); ?></i></small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-center" valign="middle"><b>No</b></td>
                        <td class="text-center" valign="middle"><b>Nama Pasien</b></td>
                        <td class="text-center" valign="middle"><b>No.RM</b></td>
                        <td class="text-center" valign="middle"><b>Gender</b></td>
                        <td class="text-center" valign="middle"><b>Tujuan</b></td>
                        <td class="text-center" valign="middle"><b>Pembayaran</b></td>
                        <td class="text-center" valign="middle"><b>Priority</b></td>
                        <td class="text-center" valign="middle"><b>Status</b></td>
                        <td class="text-center" valign="middle"><b>Tanggal/Jam Diminta</b></td>
                        <td class="text-center" valign="middle"><b>Diagnosa</b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (count($rows) < 1) {
                            echo '
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <small class="text-danger">Data rincian pelayanan tidak ditemukan.</small>
                                    </td>
                                </tr>
                            ';
                        } else {
                            $no = 1;
                            foreach ($rows as $row) {
                                $priority = strtolower((string)$row['priority']);
                                $label_priority = '<span class="text text-grayish">Biasa</span>';
                                if ($priority === 'urgent') {
                                    $label_priority = '<span class="text text-info">Segera</span>';
                                } elseif ($priority === 'stat') {
                                    $label_priority = '<span class="text text-warning">Gawat</span>';
                                } elseif ($priority !== 'routine') {
                                    $label_priority = '<span class="text text-danger">None</span>';
                                }

                                $label_datetime = "-";
                                if (!empty($row['datetime_diminta'])) {
                                    $label_datetime = date('d/m/Y H:i', strtotime($row['datetime_diminta']));
                                }

                                echo '
                                    <tr>
                                        <td class="text-center"><small>' . $no . '</small></td>
                                        <td class="text-left"><small>' . esc($row['nama']) . '</small></td>
                                        <td class="text-center"><small>' . esc($row['id_pasien']) . '</small></td>
                                        <td class="text-center"><small>' . esc($row['gender']) . '</small></td>
                                        <td class="text-center"><small>' . esc($row['tujuan']) . '</small></td>
                                        <td class="text-center"><small>' . esc($row['pembayaran']) . '</small></td>
                                        <td class="text-center"><small>' . $label_priority . '</small></td>
                                        <td class="text-center"><small>' . esc($row['status']) . '</small></td>
                                        <td class="text-center"><small>' . esc($label_datetime) . '</small></td>
                                        <td class="text-left"><small>' . esc($row['icd_10_display']) . '</small></td>
                                    </tr>
                                ';
                                $no++;
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
