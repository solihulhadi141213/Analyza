<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Ambil data POST
    $id_referensi_pemeriksaan    = (int)trim((string)($_POST['id_referensi_pemeriksaan'] ?? ''));

    $id_metode_raw               = trim((string)($_POST['id_referensi_metode_pemeriksaan'] ?? ''));
    $nama_metode_pemeriksaan     = trim((string)($_POST['nama_metode_pemeriksaan'] ?? ''));
    $display_metode_pemeriksaan  = trim((string)($_POST['display_metode_pemeriksaan'] ?? ''));
    $code_metode_pemeriksaan     = trim((string)($_POST['code_metode_pemeriksaan'] ?? ''));
    $system_metode_pemeriksaan   = trim((string)($_POST['system_metode_pemeriksaan'] ?? ''));

    $id_spesimen_raw             = trim((string)($_POST['id_referensi_jenis_spesimen'] ?? ''));
    $nama_spesimen               = trim((string)($_POST['nama_spesimen'] ?? ''));
    $display_spesimen            = trim((string)($_POST['display_spesimen'] ?? ''));
    $code_spesimen               = trim((string)($_POST['code_spesimen'] ?? ''));
    $system_spesimen             = trim((string)($_POST['system_spesimen'] ?? ''));

    $id_metode_sample_raw        = trim((string)($_POST['id_referensi_metode_sample'] ?? ''));
    $nama_metode_sample          = trim((string)($_POST['nama_metode_sample'] ?? ''));
    $display_metode_sample       = trim((string)($_POST['display_metode_sample'] ?? ''));
    $code_metode_sample          = trim((string)($_POST['code_metode_sample'] ?? ''));
    $system_metode_sample        = trim((string)($_POST['system_metode_sample'] ?? ''));

    $id_container_raw            = trim((string)($_POST['id_referensi_container'] ?? ''));
    $nama_container              = trim((string)($_POST['nama_container'] ?? ''));
    $display_container           = trim((string)($_POST['display_container'] ?? ''));
    $code_container              = trim((string)($_POST['code_container'] ?? ''));
    $system_container            = trim((string)($_POST['system_container'] ?? ''));
    $kapasitas_container         = trim((string)($_POST['kapasitas_container'] ?? ''));
    $unit_container              = trim((string)($_POST['unit_container'] ?? ''));
    $code_unit_container         = trim((string)($_POST['code_unit_container'] ?? ''));
    $system_unit_container       = trim((string)($_POST['system_unit_container'] ?? ''));

    // Fallback nama jika user menambah item baru lewat tag select2
    if ($nama_metode_pemeriksaan === '' && $id_metode_raw !== '' && !ctype_digit($id_metode_raw)) {
        $nama_metode_pemeriksaan = $id_metode_raw;
    }
    if ($nama_spesimen === '' && $id_spesimen_raw !== '' && !ctype_digit($id_spesimen_raw)) {
        $nama_spesimen = $id_spesimen_raw;
    }
    if ($nama_metode_sample === '' && $id_metode_sample_raw !== '' && !ctype_digit($id_metode_sample_raw)) {
        $nama_metode_sample = $id_metode_sample_raw;
    }
    if ($nama_container === '' && $id_container_raw !== '' && !ctype_digit($id_container_raw)) {
        $nama_container = $id_container_raw;
    }

    // Daftar field wajib
    $requiredFields = [
        'id_referensi_pemeriksaan'   => [$id_referensi_pemeriksaan, 'ID Pemeriksaan Tidak Boleh Kosong!'],
        'nama_metode_pemeriksaan'    => [$nama_metode_pemeriksaan, 'Nama Metode Pemeriksaan Tidak Boleh Kosong!'],
        'display_metode_pemeriksaan' => [$display_metode_pemeriksaan, 'Display Metode Pemeriksaan Tidak Boleh Kosong!'],
        'code_metode_pemeriksaan'    => [$code_metode_pemeriksaan, 'Code Metode Pemeriksaan Tidak Boleh Kosong!'],
        'system_metode_pemeriksaan'  => [$system_metode_pemeriksaan, 'System Metode Pemeriksaan Tidak Boleh Kosong!'],
        'nama_spesimen'              => [$nama_spesimen, 'Nama Spesimen Tidak Boleh Kosong!'],
        'display_spesimen'           => [$display_spesimen, 'Display Spesimen Tidak Boleh Kosong!'],
        'code_spesimen'              => [$code_spesimen, 'Code Spesimen Tidak Boleh Kosong!'],
        'system_spesimen'            => [$system_spesimen, 'System Spesimen Tidak Boleh Kosong!'],
        'nama_metode_sample'         => [$nama_metode_sample, 'Nama Metode Sample Tidak Boleh Kosong!'],
        'display_metode_sample'      => [$display_metode_sample, 'Display Metode Sample Tidak Boleh Kosong!'],
        'code_metode_sample'         => [$code_metode_sample, 'Code Metode Sample Tidak Boleh Kosong!'],
        'system_metode_sample'       => [$system_metode_sample, 'System Metode Sample Tidak Boleh Kosong!'],
        'nama_container'             => [$nama_container, 'Nama Container Tidak Boleh Kosong!'],
        'display_container'          => [$display_container, 'Display Container Tidak Boleh Kosong!'],
        'code_container'             => [$code_container, 'Code Container Tidak Boleh Kosong!'],
        'system_container'           => [$system_container, 'System Container Tidak Boleh Kosong!'],
        'kapasitas_container'        => [$kapasitas_container, 'Kapasitas Container Tidak Boleh Kosong!'],
        'unit_container'             => [$unit_container, 'Unit Container Tidak Boleh Kosong!'],
        'code_unit_container'        => [$code_unit_container, 'Code Unit Container Tidak Boleh Kosong!'],
        'system_unit_container'      => [$system_unit_container, 'System Unit Container Tidak Boleh Kosong!']
    ];

    foreach ($requiredFields as $field => $config) {
        $value = $config[0];
        $message = $config[1];

        if ((string)$value === '' || (is_int($value) && $value <= 0)) {
            echo json_encode([
                'status'  => 'error',
                'message' => $message
            ]);
            exit;
        }
    }

    try {
        $Conn->begin_transaction();

        // Pastikan id_referensi_pemeriksaan valid
        $stmtPemeriksaan = $Conn->prepare("SELECT id_referensi_pemeriksaan FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ? LIMIT 1");
        if (!$stmtPemeriksaan) {
            throw new Exception('Gagal menyiapkan query validasi pemeriksaan.');
        }
        $stmtPemeriksaan->bind_param("i", $id_referensi_pemeriksaan);
        if (!$stmtPemeriksaan->execute()) {
            throw new Exception('Gagal memvalidasi data pemeriksaan.');
        }
        $rsPemeriksaan = $stmtPemeriksaan->get_result();
        if ($rsPemeriksaan->num_rows === 0) {
            throw new Exception('Data pemeriksaan tidak ditemukan.');
        }
        $stmtPemeriksaan->close();

        // 4. Metode Pemeriksaan
        if ($id_metode_raw !== '' && ctype_digit($id_metode_raw)) {
            $id_referensi_metode_pemeriksaan = (int)$id_metode_raw;
        } else {
            $stmtInsertMetode = $Conn->prepare("INSERT INTO referensi_metode_pemeriksaan (nama_metode_pemeriksaan, display_metode_pemeriksaan, code_metode_pemeriksaan, system_metode_pemeriksaan) VALUES (?,?,?,?)");
            if (!$stmtInsertMetode) {
                throw new Exception('Gagal menyiapkan insert metode pemeriksaan.');
            }
            $stmtInsertMetode->bind_param("ssss", $nama_metode_pemeriksaan, $display_metode_pemeriksaan, $code_metode_pemeriksaan, $system_metode_pemeriksaan);
            if (!$stmtInsertMetode->execute()) {
                throw new Exception('Gagal insert metode pemeriksaan.');
            }
            $id_referensi_metode_pemeriksaan = (int)$Conn->insert_id;
            $stmtInsertMetode->close();
        }

        // 5. Jenis Spesimen
        if ($id_spesimen_raw !== '' && ctype_digit($id_spesimen_raw)) {
            $id_referensi_jenis_spesimen = (int)$id_spesimen_raw;
        } else {
            $stmtInsertSpesimen = $Conn->prepare("INSERT INTO referensi_jenis_spesimen (nama_spesimen, display_spesimen, code_spesimen, system_spesimen) VALUES (?,?,?,?)");
            if (!$stmtInsertSpesimen) {
                throw new Exception('Gagal menyiapkan insert jenis spesimen.');
            }
            $stmtInsertSpesimen->bind_param("ssss", $nama_spesimen, $display_spesimen, $code_spesimen, $system_spesimen);
            if (!$stmtInsertSpesimen->execute()) {
                throw new Exception('Gagal insert jenis spesimen.');
            }
            $id_referensi_jenis_spesimen = (int)$Conn->insert_id;
            $stmtInsertSpesimen->close();
        }

        // 6. Metode Sample
        if ($id_metode_sample_raw !== '' && ctype_digit($id_metode_sample_raw)) {
            $id_referensi_metode_sample = (int)$id_metode_sample_raw;
        } else {
            $stmtInsertMetodeSample = $Conn->prepare("INSERT INTO referensi_metode_sample (nama_metode_sample, display_metode_sample, code_metode_sample, system_metode_sample) VALUES (?,?,?,?)");
            if (!$stmtInsertMetodeSample) {
                throw new Exception('Gagal menyiapkan insert metode sample.');
            }
            $stmtInsertMetodeSample->bind_param("ssss", $nama_metode_sample, $display_metode_sample, $code_metode_sample, $system_metode_sample);
            if (!$stmtInsertMetodeSample->execute()) {
                throw new Exception('Gagal insert metode sample.');
            }
            $id_referensi_metode_sample = (int)$Conn->insert_id;
            $stmtInsertMetodeSample->close();
        }

        // 7. Container
        if ($id_container_raw !== '' && ctype_digit($id_container_raw)) {
            $id_referensi_container = (int)$id_container_raw;
        } else {
            $stmtInsertContainer = $Conn->prepare("INSERT INTO referensi_container (nama_container, display_container, code_container, system_container, kapasitas_container, unit_container, code_unit_container, system_unit_container) VALUES (?,?,?,?,?,?,?,?)");
            if (!$stmtInsertContainer) {
                throw new Exception('Gagal menyiapkan insert container.');
            }
            $stmtInsertContainer->bind_param("ssssdsss", $nama_container, $display_container, $code_container, $system_container, $kapasitas_container, $unit_container, $code_unit_container, $system_unit_container);
            if (!$stmtInsertContainer->execute()) {
                throw new Exception('Gagal insert container.');
            }
            $id_referensi_container = (int)$Conn->insert_id;
            $stmtInsertContainer->close();
        }

        // 8. Sinkronisasi referensi_satuan berdasarkan code_unit_container
        $stmtFindSatuan = $Conn->prepare("SELECT id_referensi_satuan FROM referensi_satuan WHERE code_satuan = ? LIMIT 1");
        if (!$stmtFindSatuan) {
            throw new Exception('Gagal menyiapkan pencarian satuan.');
        }
        $stmtFindSatuan->bind_param("s", $code_unit_container);
        if (!$stmtFindSatuan->execute()) {
            throw new Exception('Gagal mencari data satuan.');
        }
        $rsSatuan = $stmtFindSatuan->get_result();
        $satuanAda = $rsSatuan->num_rows > 0;
        $stmtFindSatuan->close();

        if (!$satuanAda) {
            $nama_satuan = $unit_container;
            $unit_satuan = $unit_container;
            $stmtInsertSatuan = $Conn->prepare("INSERT INTO referensi_satuan (nama_satuan, unit_satuan, code_satuan, system_satuan) VALUES (?,?,?,?)");
            if (!$stmtInsertSatuan) {
                throw new Exception('Gagal menyiapkan insert satuan.');
            }
            $stmtInsertSatuan->bind_param("ssss", $nama_satuan, $unit_satuan, $code_unit_container, $system_unit_container);
            if (!$stmtInsertSatuan->execute()) {
                throw new Exception('Gagal insert data satuan.');
            }
            $stmtInsertSatuan->close();
        }

        // Cek duplikasi kombinasi relasi
        $stmtCekDuplikasi = $Conn->prepare("SELECT id_referensi_pemeriksaan_relasi FROM referensi_pemeriksaan_relasi WHERE id_referensi_pemeriksaan = ? AND id_referensi_metode_pemeriksaan = ? AND id_referensi_jenis_spesimen = ? AND id_referensi_metode_sample = ? AND id_referensi_container = ? LIMIT 1");
        if (!$stmtCekDuplikasi) {
            throw new Exception('Gagal menyiapkan pengecekan duplikasi relasi.');
        }
        $stmtCekDuplikasi->bind_param("iiiii", $id_referensi_pemeriksaan, $id_referensi_metode_pemeriksaan, $id_referensi_jenis_spesimen, $id_referensi_metode_sample, $id_referensi_container);
        if (!$stmtCekDuplikasi->execute()) {
            throw new Exception('Gagal melakukan pengecekan duplikasi relasi.');
        }
        $rsCekDuplikasi = $stmtCekDuplikasi->get_result();
        $duplikatRelasi = $rsCekDuplikasi->num_rows > 0;
        $stmtCekDuplikasi->close();

        if ($duplikatRelasi) {
            throw new Exception('Data relasi sudah ada.');
        }

        // 9. Insert relasi pemeriksaan
        $stmtInsertRelasi = $Conn->prepare("INSERT INTO referensi_pemeriksaan_relasi (id_referensi_pemeriksaan, id_referensi_metode_pemeriksaan, id_referensi_jenis_spesimen, id_referensi_metode_sample, id_referensi_container) VALUES (?,?,?,?,?)");
        if (!$stmtInsertRelasi) {
            throw new Exception('Gagal menyiapkan insert relasi pemeriksaan.');
        }
        $stmtInsertRelasi->bind_param("iiiii", $id_referensi_pemeriksaan, $id_referensi_metode_pemeriksaan, $id_referensi_jenis_spesimen, $id_referensi_metode_sample, $id_referensi_container);
        if (!$stmtInsertRelasi->execute()) {
            throw new Exception('Gagal insert relasi pemeriksaan.');
        }
        $stmtInsertRelasi->close();

        $Conn->commit();

        // 10. Response sukses
        echo json_encode([
            'status'  => 'success',
            'message' => 'Berhasil Insert relasi'
        ]);
        exit;

    } catch (Exception $e) {
        $Conn->rollback();

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
?>


