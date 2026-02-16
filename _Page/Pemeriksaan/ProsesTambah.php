<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    /**
     * Helper kirim response JSON lalu hentikan proses
     */
    function fail($message) {
        echo json_encode([
            'status'  => 'error',
            'message' => $message
        ]);
        exit;
    }

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        fail('Sesi Akses Sudah Berakhir! Silahkan Login Ulang!');
    }

    // Validasi Data Wajib (Mandatory)
    $mandatory_fields = [
        'id_pasien'                => 'ID pasien Tidak Boleh Kosong!',
        'nama_pasien'              => 'Nama pasien Tidak Boleh Kosong!',
        'tanggal_lahir'            => 'Tanggal Lahir Tidak Boleh Kosong!',
        'gender'                   => 'Gender Tidak Boleh Kosong!',
        'id_kunjungan'             => 'ID Kunjungan Tidak Boleh Kosong!',
        'tujuan'                   => 'Tujuan Kunjungan Tidak Boleh Kosong!',
        'pembayaran'               => 'Pembayaran Tidak Boleh Kosong!',
        'fakses'                   => 'Faskes Tidak Boleh Kosong!',
        'unit'                     => 'Nama Unit/Instalasi Tidak Boleh Kosong!',
        'tanggal_diminta'          => 'Tanggal permintaan Tidak Boleh Kosong!',
        'jam_diminta'              => 'Waktu Permintaan Tidak Boleh Kosong!',
        'priority'                 => 'Tingkat Prioritas Tidak Boleh Kosong!',
        'nama_dokter_pengirim'     => 'Nama Dokter Pengirim Tidak Boleh Kosong!',
        'kode_dokter_pengirim'     => 'Kode Dokter Pengirim Tidak Boleh Kosong!',
        'diagnosis_display'        => 'Diagnosis (Reson Code) Tidak Boleh Kosong!',
        'diagnosis_code'           => 'Kode Diagnosis (Reson Code) Tidak Boleh Kosong!',
        'diagnosis_system'         => 'Referensi System Diagnosis (Reson Code) Tidak Boleh Kosong!',
        'id_referensi_pemeriksaan' => 'Permintaan Pemeriksaan Tidak Boleh Kosong!'
    ];

    foreach ($mandatory_fields as $field => $message) {
        $value = $_POST[$field] ?? null;

        if (is_array($value)) {
            if (count(array_filter($value, function ($item) {
                return trim((string)$item) !== '';
            })) === 0) {
                fail($message);
            }
            continue;
        }

        if ($value === null || trim((string)$value) === '') {
            fail($message);
        }
    }

    // Membuat Variabel Dan Sanitasi
    $id_pasien            = validateAndSanitizeInput($_POST['id_pasien']);
    $ihs_pasien           = validateAndSanitizeInput($_POST['ihs_pasien'] ?? '');
    $nama_pasien          = validateAndSanitizeInput($_POST['nama_pasien']);
    $tanggal_lahir        = validateAndSanitizeInput($_POST['tanggal_lahir']);
    $gender               = validateAndSanitizeInput($_POST['gender']);
    $id_kunjungan         = validateAndSanitizeInput($_POST['id_kunjungan']);
    $id_encounter         = validateAndSanitizeInput($_POST['id_encounter'] ?? '');
    $tujuan               = validateAndSanitizeInput($_POST['tujuan']);
    $pembayaran           = validateAndSanitizeInput($_POST['pembayaran']);
    $fakses               = validateAndSanitizeInput($_POST['fakses']);
    $unit                 = validateAndSanitizeInput($_POST['unit']);
    $tanggal_diminta      = validateAndSanitizeInput($_POST['tanggal_diminta']);
    $jam_diminta          = validateAndSanitizeInput($_POST['jam_diminta']);
    $priority             = validateAndSanitizeInput($_POST['priority']);
    $puasa                = validateAndSanitizeInput($_POST['puasa'] ?? 0);
    $nama_dokter_pengirim = validateAndSanitizeInput($_POST['nama_dokter_pengirim']);
    $kode_dokter_pengirim = validateAndSanitizeInput($_POST['kode_dokter_pengirim']);
    $ihs_dokter_pengirim  = validateAndSanitizeInput($_POST['ihs_dokter_pengirim'] ?? '');
    $diagnosis_display    = validateAndSanitizeInput($_POST['diagnosis_display'] ?? '');
    $diagnosis_code       = validateAndSanitizeInput($_POST['diagnosis_code'] ?? '');
    $diagnosis_system     = validateAndSanitizeInput($_POST['diagnosis_system'] ?? '');

    // Jika ada keterangan
    if(!empty($_POST['keterangan'])){
        $keterangan     = validateAndSanitizeInput($_POST['keterangan']);
    }else{
        $keterangan     = "";
    }
    
    $id_referensi_pemeriksaan_raw = $_POST['id_referensi_pemeriksaan'] ?? [];
    if (!is_array($id_referensi_pemeriksaan_raw)) {
        $id_referensi_pemeriksaan_raw = [$id_referensi_pemeriksaan_raw];
    }

    $id_referensi_pemeriksaan = [];
    foreach ($id_referensi_pemeriksaan_raw as $id_ref) {
        $id_ref = (int)trim((string)$id_ref);
        if ($id_ref > 0) {
            $id_referensi_pemeriksaan[] = $id_ref;
        }
    }
    $id_referensi_pemeriksaan = array_values(array_unique($id_referensi_pemeriksaan));

    if (empty($id_referensi_pemeriksaan)) {
        fail('Permintaan Pemeriksaan Tidak Boleh Kosong!');
    }

    // Validasi enum sederhana agar tidak gagal insert di level DB
    if (!in_array($gender, ['Laki-laki', 'Perempuan'], true)) {
        fail('Gender tidak valid');
    }
    if (!in_array($tujuan, ['Rajal', 'Ranap'], true)) {
        fail('Tujuan kunjungan tidak valid');
    }
    if (!in_array($priority, ['routine', 'urgent', 'stat'], true)) {
        fail('Priority tidak valid');
    }

    $puasa = ((string)$puasa === '1') ? 1 : 0;

    $id_pasien_int    = (int)$id_pasien;
    $id_kunjungan_int = (int)$id_kunjungan;
    if ($id_pasien_int <= 0) {
        fail('ID pasien tidak valid');
    }
    if ($id_kunjungan_int <= 0) {
        fail('ID kunjungan tidak valid');
    }

    // Format datetime permintaan (YYYY-mm-dd HH:ii:ss)
    $jam_diminta_normalized = (substr_count($jam_diminta, ':') === 1) ? ($jam_diminta . ':00') : $jam_diminta;
    $datetime_diminta = $tanggal_diminta . ' ' . $jam_diminta_normalized;
    if (strtotime($datetime_diminta) === false) {
        fail('Format tanggal/jam permintaan tidak valid');
    }

    // Kolom JSON diagnosis wajib NOT NULL di tabel laboratorium
    $diagnosis = json_encode([
        'system'  => $diagnosis_system,
        'code'    => $diagnosis_code,
        'display' => $diagnosis_display
    ]);

    // Kolom wajib lain yang belum dideklarasikan di form
    $status      = 'Diminta';

    // ID master laboratorium
    $id_laboratorium = generateUuidV1();
    $form_system = "Analyza";

    try {
        $Conn->begin_transaction();

        // Insert header pemeriksaan ke tabel laboratorium
        $stmtLaboratorium = $Conn->prepare("
            INSERT INTO laboratorium (
                id_laboratorium,
                id_pasien,
                id_kunjungan,
                ihs_pasien,
                id_encounter,
                nama,
                gender,
                tanggal_lahir,
                tujuan,
                pembayaran,
                fakses,
                unit,
                priority,
                kode_dokter_pengirim,
                ihs_dokter_pengirim,
                nama_dokter_pengirim,
                diagnosis,
                puasa,
                status,
                datetime_diminta,
                keterangan,
                form_system
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        if (!$stmtLaboratorium) {
            throw new Exception('Gagal menyiapkan query insert laboratorium');
        }

        $stmtLaboratorium->bind_param(
            "siissssssssssssssissss",
            $id_laboratorium,
            $id_pasien_int,
            $id_kunjungan_int,
            $ihs_pasien,
            $id_encounter,
            $nama_pasien,
            $gender,
            $tanggal_lahir,
            $tujuan,
            $pembayaran,
            $fakses,
            $unit,
            $priority,
            $kode_dokter_pengirim,
            $ihs_dokter_pengirim,
            $nama_dokter_pengirim,
            $diagnosis,
            $puasa,
            $status,
            $datetime_diminta,
            $keterangan,
            $form_system
        );

        if (!$stmtLaboratorium->execute()) {
            throw new Exception('Gagal menyimpan data laboratorium');
        }
        $stmtLaboratorium->close();

        // Prepare statement lookup referensi pemeriksaan
        $stmtRef = $Conn->prepare("
            SELECT 
                nama_pemeriksaan,
                category_pemeriksaan
            FROM referensi_pemeriksaan
            WHERE id_referensi_pemeriksaan = ?
            LIMIT 1
        ");
        if (!$stmtRef) {
            throw new Exception('Gagal menyiapkan query referensi pemeriksaan');
        }

        // Prepare insert laboratorium_rincian
        $stmtRincian = $Conn->prepare("
            INSERT INTO laboratorium_rincian (
                id_laboratorium,
                id_referensi_pemeriksaan,
                nama_pemeriksaan,
                category_pemeriksaan
            ) VALUES (?,?,?,?)
        ");
        if (!$stmtRincian) {
            throw new Exception('Gagal menyiapkan query insert laboratorium rincian');
        }

        // Multiple insert detail pemeriksaan
        foreach ($id_referensi_pemeriksaan as $id_ref) {
            $stmtRef->bind_param("i", $id_ref);
            if (!$stmtRef->execute()) {
                throw new Exception('Gagal mengambil referensi pemeriksaan');
            }

            $rsRef = $stmtRef->get_result();
            if ($rsRef->num_rows === 0) {
                throw new Exception('Referensi pemeriksaan dengan ID ' . $id_ref . ' tidak ditemukan');
            }

            $dataRef = $rsRef->fetch_assoc();
            $nama_pemeriksaan_ref     = $dataRef['nama_pemeriksaan'] ?? '';
            $category_pemeriksaan_ref = $dataRef['category_pemeriksaan'] ?? '';

            $stmtRincian->bind_param(
                "siss",
                $id_laboratorium,
                $id_ref,
                $nama_pemeriksaan_ref,
                $category_pemeriksaan_ref
            );

            if (!$stmtRincian->execute()) {
                throw new Exception('Gagal menyimpan rincian pemeriksaan');
            }
        }

        $stmtRef->close();
        $stmtRincian->close();

        $Conn->commit();

        echo json_encode([
            'status'  => 'success',
            'message' => 'Data pemeriksaan berhasil disimpan'
        ]);
        exit;
    } catch (Exception $e) {
        $Conn->rollback();
        fail($e->getMessage());
    }
?>
