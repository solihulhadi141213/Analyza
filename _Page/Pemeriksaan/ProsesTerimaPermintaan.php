<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    /**
     * Helper response error JSON
     */
    function fail($message) {
        echo json_encode([
            'status'  => 'error',
            'message' => $message
        ]);
        exit;
    }

    // Validasi sesi
    if (empty($SessionIdAccess)) {
        fail('Sesi Akses Sudah Berakhir! Silahkan Login Ulang!');
    }

    // Validasi input wajib umum
    if (empty($_POST['id_laboratorium'])) {
        fail('ID Pemeriksaan Tidak Boleh Kosong!');
    }
    if (empty($_POST['status'])) {
        fail('Status Pemeriksaan Tidak Boleh Kosong!');
    }

    // Sanitasi input umum
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);
    $status          = validateAndSanitizeInput($_POST['status']);

    // Validasi status yang boleh diproses dari modal ini
    $status_diizinkan = ['Diterima', 'Ditolak', 'Dibatalkan'];
    if (!in_array($status, $status_diizinkan, true)) {
        fail('Status pemeriksaan tidak valid');
    }

    // Cek data laboratorium + status saat ini
    $stmtCek = $Conn->prepare("
        SELECT status
        FROM laboratorium
        WHERE id_laboratorium = ?
        LIMIT 1
    ");
    if (!$stmtCek) {
        fail('Gagal menyiapkan query validasi data');
    }
    $stmtCek->bind_param("s", $id_laboratorium);
    if (!$stmtCek->execute()) {
        $stmtCek->close();
        fail('Gagal memvalidasi data laboratorium');
    }
    $dataLab = $stmtCek->get_result()->fetch_assoc();
    $stmtCek->close();

    if (empty($dataLab)) {
        fail('Data pemeriksaan laboratorium tidak ditemukan');
    }

    $status_lama = $dataLab['status'] ?? '';
    if ($status_lama !== 'Diminta') {
        fail('Pemeriksaan ini sudah diproses dengan status: ' . $status_lama);
    }

    try {
        $Conn->begin_transaction();

        // -----------------------------------------------------------------
        // STATUS DITERIMA -> wajib tanggal/jam + dokter penerima
        // -----------------------------------------------------------------
        if ($status === 'Diterima') {
            if (empty($_POST['tanggal_diterima'])) {
                throw new Exception('Tanggal diterima tidak boleh kosong');
            }
            if (empty($_POST['jam_diterima'])) {
                throw new Exception('Jam diterima tidak boleh kosong');
            }
            if (empty($_POST['nama_dokter_penerima'])) {
                throw new Exception('Dokter penerima tidak boleh kosong');
            }

            $tanggal_diterima = validateAndSanitizeInput($_POST['tanggal_diterima']);
            $jam_diterima     = validateAndSanitizeInput($_POST['jam_diterima']);

            $nama_dokter_penerima = validateAndSanitizeInput($_POST['nama_dokter_penerima']);
            $kode_dokter_penerima = validateAndSanitizeInput($_POST['kode_dokter_penerima'] ?? '');
            $ihs_dokter_penerima  = validateAndSanitizeInput($_POST['ihs_dokter_penerima'] ?? '');

            // Normalisasi HH:ii menjadi HH:ii:ss
            if (substr_count($jam_diterima, ':') === 1) {
                $jam_diterima .= ':00';
            }

            $datetime_diterima = $tanggal_diterima . ' ' . $jam_diterima;
            if (strtotime($datetime_diterima) === false) {
                throw new Exception('Format tanggal/jam diterima tidak valid');
            }

            $kode_dokter_penerima = ($kode_dokter_penerima === '') ? null : $kode_dokter_penerima;
            $ihs_dokter_penerima  = ($ihs_dokter_penerima === '') ? null : $ihs_dokter_penerima;

            $stmtUpdate = $Conn->prepare("
                UPDATE laboratorium
                SET
                    status = ?,
                    datetime_diterima = ?,
                    kode_dokter_penerima = ?,
                    ihs_dokter_penerima = ?,
                    nama_dokter_penerima = ?,
                    alasan = NULL
                WHERE id_laboratorium = ?
            ");
            if (!$stmtUpdate) {
                throw new Exception('Gagal menyiapkan query update pemeriksaan');
            }

            $stmtUpdate->bind_param(
                "ssssss",
                $status,
                $datetime_diterima,
                $kode_dokter_penerima,
                $ihs_dokter_penerima,
                $nama_dokter_penerima,
                $id_laboratorium
            );

            if (!$stmtUpdate->execute()) {
                $stmtUpdate->close();
                throw new Exception('Gagal menyimpan status penerimaan pemeriksaan');
            }
            $stmtUpdate->close();
        } else {
            // -------------------------------------------------------------
            // STATUS DITOLAK / DIBATALKAN -> wajib alasan
            // -------------------------------------------------------------
            if (empty($_POST['alasan'])) {
                throw new Exception('Alasan penolakan/pembatalan tidak boleh kosong');
            }

            $alasan = validateAndSanitizeInput($_POST['alasan']);

            $stmtUpdate = $Conn->prepare("
                UPDATE laboratorium
                SET
                    status = ?,
                    alasan = ?,
                    datetime_diterima = NULL,
                    kode_dokter_penerima = NULL,
                    ihs_dokter_penerima = NULL,
                    nama_dokter_penerima = NULL
                WHERE id_laboratorium = ?
            ");
            if (!$stmtUpdate) {
                throw new Exception('Gagal menyiapkan query update pemeriksaan');
            }

            $stmtUpdate->bind_param("sss", $status, $alasan, $id_laboratorium);
            if (!$stmtUpdate->execute()) {
                $stmtUpdate->close();
                throw new Exception('Gagal menyimpan status penolakan/pembatalan pemeriksaan');
            }
            $stmtUpdate->close();
        }

        $Conn->commit();

        echo json_encode([
            'status'  => 'success',
            'message' => 'Status pemeriksaan berhasil diperbarui'
        ]);
        exit;
    } catch (Exception $e) {
        $Conn->rollback();
        fail($e->getMessage());
    }
?>
