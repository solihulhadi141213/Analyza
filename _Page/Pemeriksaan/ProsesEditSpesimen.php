<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

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

    // Wajib dari form
    if (empty($_POST['id_laboratorium_spesimen'])) {
        fail('ID Spesimen Tidak Boleh Kosong!');
    }
    if (empty($_POST['id_referensi_jenis_spesimen'])) {
        fail('Jenis Spesimen Tidak Boleh Kosong!');
    }
    if (empty($_POST['id_referensi_metode_sample'])) {
        fail('Metode Pengambilan Spesimen Tidak Boleh Kosong!');
    }
    if (empty($_POST['id_referensi_body_site'])) {
        fail('Sumber Spesimen lokasi tubuh Tidak Boleh Kosong!');
    }
    if (empty($_POST['id_referensi_container'])) {
        fail('Kemasan/Kontainer Spesimen Tidak Boleh Kosong!');
    }
    if (!isset($_POST['quantity_value']) || trim((string) $_POST['quantity_value']) === '') {
        fail('Informasi jumlah spesimen yang diambil Tidak Boleh Kosong!');
    }
    if (empty($_POST['collector_name'])) {
        fail('Nama Petugas Tidak Boleh Kosong!');
    }
    if (empty($_POST['collector_ihs'])) {
        fail('IHS Petugas Tidak Boleh Kosong!');
    }

    // Variabel input
    $id_laboratorium_spesimen    = (int) validateAndSanitizeInput($_POST['id_laboratorium_spesimen']);
    $id_referensi_jenis_spesimen = (int) validateAndSanitizeInput($_POST['id_referensi_jenis_spesimen']);
    $id_referensi_metode_sample  = (int) validateAndSanitizeInput($_POST['id_referensi_metode_sample']);
    $id_referensi_body_site      = (int) validateAndSanitizeInput($_POST['id_referensi_body_site']);
    $id_referensi_container      = (int) validateAndSanitizeInput($_POST['id_referensi_container']);
   

    $quantity_value_raw = str_replace(',', '.', (string) $_POST['quantity_value']);
    if (!is_numeric($quantity_value_raw)) {
        fail('Jumlah/volume spesimen harus berupa angka');
    }
    $quantity_value = number_format((float) $quantity_value_raw, 2, '.', '');
    if ((float) $quantity_value <= 0) {
        fail('Jumlah/volume spesimen harus lebih dari 0');
    }

    // Fallback aman jika field tidak terkirim dari form
    $tanggal_spesimen = trim((string) ($_POST['tanggal_spesimen'] ?? ''));
    if ($tanggal_spesimen === '') {
        $tanggal_spesimen = date('Y-m-d');
    }

    $jam_spesimen = trim((string) ($_POST['jam_spesimen'] ?? ''));
    if ($jam_spesimen === '') {
        $jam_spesimen = date('H:i:s');
    } elseif (substr_count($jam_spesimen, ':') === 1) {
        $jam_spesimen .= ':00';
    }

    $datetime_spesimen = $tanggal_spesimen . ' ' . $jam_spesimen;
    if (strtotime($datetime_spesimen) === false) {
        fail('Format tanggal/jam pengambilan spesimen tidak valid');
    }

    $collector_name = trim((string) ($_POST['collector_name'] ?? ''));
    $collector_ihs = trim((string) ($_POST['collector_ihs'] ?? ''));

    if ($id_laboratorium_spesimen <= 0) {
        fail('ID spesimen tidak valid');
    }

    // Ambil data spesimen yang akan diedit
    $stmtSpesimen = $Conn->prepare("
        SELECT id_laboratorium
        FROM laboratorium_spesimen
        WHERE id_laboratorium_spesimen = ?
        LIMIT 1
    ");
    if (!$stmtSpesimen) {
        fail('Gagal menyiapkan query validasi data spesimen');
    }
    $stmtSpesimen->bind_param("i", $id_laboratorium_spesimen);
    if (!$stmtSpesimen->execute()) {
        $stmtSpesimen->close();
        fail('Gagal validasi data spesimen');
    }
    $dataSpesimen = $stmtSpesimen->get_result()->fetch_assoc();
    $stmtSpesimen->close();
    if (empty($dataSpesimen)) {
        fail('Data spesimen tidak ditemukan');
    }
    $id_laboratorium = $dataSpesimen['id_laboratorium'] ?? '';

    // Validasi data laboratorium
    $stmtLab = $Conn->prepare("SELECT id_laboratorium FROM laboratorium WHERE id_laboratorium = ?");
    if (!$stmtLab) {
        fail('Gagal menyiapkan query validasi laboratorium');
    }
    $stmtLab->bind_param("s", $id_laboratorium);
    if (!$stmtLab->execute()) {
        $stmtLab->close();
        fail('Gagal validasi data laboratorium');
    }
    $rsLab = $stmtLab->get_result();
    if ($rsLab->num_rows === 0) {
        $stmtLab->close();
        fail('Data laboratorium tidak ditemukan');
    }
    $stmtLab->close();

    // Ambil referensi jenis spesimen
    $stmtJenis = $Conn->prepare("
        SELECT nama_spesimen, display_spesimen, code_spesimen, system_spesimen
        FROM referensi_jenis_spesimen
        WHERE id_referensi_jenis_spesimen = ?
        LIMIT 1
    ");
    if (!$stmtJenis) {
        fail('Gagal menyiapkan query referensi jenis spesimen');
    }
    $stmtJenis->bind_param("i", $id_referensi_jenis_spesimen);
    if (!$stmtJenis->execute()) {
        $stmtJenis->close();
        fail('Gagal membaca referensi jenis spesimen');
    }
    $dataJenis = $stmtJenis->get_result()->fetch_assoc();
    $stmtJenis->close();
    if (empty($dataJenis)) {
        fail('Jenis spesimen tidak ditemukan');
    }

    // Ambil referensi metode
    $stmtMetode = $Conn->prepare("
        SELECT nama_metode_sample, display_metode_sample, code_metode_sample, system_metode_sample
        FROM referensi_metode_sample
        WHERE id_referensi_metode_sample = ?
        LIMIT 1
    ");
    if (!$stmtMetode) {
        fail('Gagal menyiapkan query referensi metode spesimen');
    }
    $stmtMetode->bind_param("i", $id_referensi_metode_sample);
    if (!$stmtMetode->execute()) {
        $stmtMetode->close();
        fail('Gagal membaca referensi metode spesimen');
    }
    $dataMetode = $stmtMetode->get_result()->fetch_assoc();
    $stmtMetode->close();
    if (empty($dataMetode)) {
        fail('Metode spesimen tidak ditemukan');
    }

    // Ambil referensi body site
    $stmtBodySite = $Conn->prepare("
        SELECT body_site_nama, body_site_display, body_site_code, body_site_system
        FROM referensi_body_site
        WHERE id_referensi_body_site = ?
        LIMIT 1
    ");
    if (!$stmtBodySite) {
        fail('Gagal menyiapkan query referensi body site');
    }
    $stmtBodySite->bind_param("i", $id_referensi_body_site);
    if (!$stmtBodySite->execute()) {
        $stmtBodySite->close();
        fail('Gagal membaca referensi body site');
    }
    $dataBodySite = $stmtBodySite->get_result()->fetch_assoc();
    $stmtBodySite->close();
    if (empty($dataBodySite)) {
        fail('Lokasi Tubuh (Body Site) tidak ditemukan '.$id_referensi_body_site.'');
    }

    // Ambil referensi kontainer + unit
    $stmtContainer = $Conn->prepare("
        SELECT 
            nama_container,
            display_container,
            code_container,
            system_container,
            unit_container,
            code_unit_container,
            system_unit_container
        FROM referensi_container
        WHERE id_referensi_container = ?
        LIMIT 1
    ");
    if (!$stmtContainer) {
        fail('Gagal menyiapkan query referensi kontainer');
    }
    $stmtContainer->bind_param("i", $id_referensi_container);
    if (!$stmtContainer->execute()) {
        $stmtContainer->close();
        fail('Gagal membaca referensi kontainer');
    }
    $dataContainer = $stmtContainer->get_result()->fetch_assoc();
    $stmtContainer->close();
    if (empty($dataContainer)) {
        fail('Kemasan / Kontainer tidak ditemukan');
    }

    // Nilai payload untuk tabel laboratorium_spesimen
    $nama_spesimen    = $dataJenis['nama_spesimen'] ?? '';
    $display_spesimen = $dataJenis['display_spesimen'] ?? '';
    $code_spesimen    = $dataJenis['code_spesimen'] ?? '';
    $system_spesimen  = $dataJenis['system_spesimen'] ?? '';

    $nama_metode_sample    = $dataMetode['nama_metode_sample'] ?? '';
    $display_metode_sample = $dataMetode['display_metode_sample'] ?? '';
    $code_metode_sample    = $dataMetode['code_metode_sample'] ?? '';
    $system_metode_sample  = $dataMetode['system_metode_sample'] ?? '';

    $bodysite_nama    = $dataBodySite['body_site_nama'] ?? '';
    $bodysite_display = $dataBodySite['body_site_display'] ?? '';
    $bodysite_code    = $dataBodySite['body_site_code'] ?? '';
    $bodysite_system  = $dataBodySite['body_site_system'] ?? '';

    $nama_container    = $dataContainer['nama_container'] ?? '';
    $display_container = $dataContainer['display_container'] ?? '';
    $code_container    = $dataContainer['code_container'] ?? '';
    $system_container  = $dataContainer['system_container'] ?? '';
    $quantity_unit     = $dataContainer['unit_container'] ?? '';
    $quantity_code     = $dataContainer['code_unit_container'] ?? '';
    $quantity_system   = $dataContainer['system_unit_container'] ?? '';

    try {
        $Conn->begin_transaction();

        $stmtUpdate = $Conn->prepare("
            UPDATE laboratorium_spesimen
            SET
                datetime_spesimen = ?,
                nama_spesimen = ?,
                display_spesimen = ?,
                code_spesimen = ?,
                system_spesimen = ?,
                nama_metode_sample = ?,
                display_metode_sample = ?,
                code_metode_sample = ?,
                system_metode_sample = ?,
                bodysite_nama = ?,
                bodysite_display = ?,
                bodysite_code = ?,
                bodysite_system = ?,
                nama_container = ?,
                display_container = ?,
                code_container = ?,
                system_container = ?,
                quantity_value = ?,
                quantity_unit = ?,
                quantity_code = ?,
                quantity_system = ?,
                collector_name = ?,
                collector_ihs = ?
            WHERE id_laboratorium_spesimen = ?
        ");
        if (!$stmtUpdate) {
            throw new Exception('Gagal menyiapkan query update spesimen');
        }
        $stmtUpdate->bind_param(
            "sssssssssssssssssssssssi",
            $datetime_spesimen,
            $nama_spesimen,
            $display_spesimen,
            $code_spesimen,
            $system_spesimen,
            $nama_metode_sample,
            $display_metode_sample,
            $code_metode_sample,
            $system_metode_sample,
            $bodysite_nama,
            $bodysite_display,
            $bodysite_code,
            $bodysite_system,
            $nama_container,
            $display_container,
            $code_container,
            $system_container,
            $quantity_value,
            $quantity_unit,
            $quantity_code,
            $quantity_system,
            $collector_name,
            $collector_ihs,
            $id_laboratorium_spesimen
        );
        if (!$stmtUpdate->execute()) {
            throw new Exception('Gagal memperbarui data spesimen');
        }
        $stmtUpdate->close();

        // Sinkronkan tanggal/jam spesimen di header laboratorium
        $stmtUpdateLab = $Conn->prepare("
            UPDATE laboratorium
            SET datetime_spesimen = ?
            WHERE id_laboratorium = ?
        ");
        if (!$stmtUpdateLab) {
            throw new Exception('Gagal menyiapkan query update laboratorium');
        }
        $stmtUpdateLab->bind_param("ss", $datetime_spesimen, $id_laboratorium);
        if (!$stmtUpdateLab->execute()) {
            throw new Exception('Gagal update tanggal/jam spesimen pada data laboratorium');
        }
        $stmtUpdateLab->close();

        $Conn->commit();
    } catch (Exception $e) {
        $Conn->rollback();
        fail($e->getMessage());
    }

    echo json_encode(['status'  => 'success','message' => 'Update Spesimen Berhasil!']);
    exit; 
?>
