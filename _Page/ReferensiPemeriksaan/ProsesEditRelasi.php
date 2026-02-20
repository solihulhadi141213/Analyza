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
    if (empty($_POST['id_referensi_pemeriksaan_relasi'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Relasi Referensi Pemeriksaan Tidak Boleh Kosong!'
        ]);
        exit;
    }
    if (empty($_POST['id_referensi_metode_pemeriksaan'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Metode Pemeriksaan Yang Digunakan Tidak Boleh Kosong!'
        ]);
        exit;
    }
    if (empty($_POST['id_referensi_jenis_spesimen'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Jenis Spesimen Yang Digunakan Tidak Boleh Kosong!'
        ]);
        exit;
    }
    if (empty($_POST['id_referensi_metode_sample'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Metode Spesimen Yang Digunakan Tidak Boleh Kosong!'
        ]);
        exit;
    }
    if (empty($_POST['id_referensi_container'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Referensi Kontainer Yang Digunakan Tidak Boleh Kosong!'
        ]);
        exit;
    }
    // Ambil data POST
    $id_referensi_pemeriksaan_relasi = (int)trim((string)($_POST['id_referensi_pemeriksaan_relasi'] ?? ''));
    $id_referensi_metode_pemeriksaan = (int)trim((string)($_POST['id_referensi_metode_pemeriksaan'] ?? ''));
    $id_referensi_jenis_spesimen     = (int)trim((string)($_POST['id_referensi_jenis_spesimen'] ?? ''));
    $id_referensi_metode_sample      = (int)trim((string)($_POST['id_referensi_metode_sample'] ?? ''));
    $id_referensi_container          = (int)trim((string)($_POST['id_referensi_container'] ?? ''));
    
    // 1. Siapkan Query dengan Placeholder (?)
    $sql = "UPDATE referensi_pemeriksaan_relasi SET 
                id_referensi_metode_pemeriksaan = ?,
                id_referensi_jenis_spesimen     = ?,
                id_referensi_metode_sample      = ?,
                id_referensi_container          = ?
            WHERE id_referensi_pemeriksaan_relasi = ?";

    // 2. Inisialisasi statement
    $stmt = mysqli_prepare($Conn, $sql);

    if ($stmt) {
        /* 3. Bind Parameter 
           "sssssss" berarti ada 7 parameter bertipe string (s). 
           Sesuaikan jika 'id_referensi_pemeriksaan' adalah integer (i).
        */
        mysqli_stmt_bind_param($stmt, "iiiii", 
            $id_referensi_metode_pemeriksaan, 
            $id_referensi_jenis_spesimen, 
            $id_referensi_metode_sample, 
            $id_referensi_container, 
            $id_referensi_pemeriksaan_relasi
        );

        // 4. Eksekusi
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Data referensi relasi berhasil disimpan'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . mysqli_stmt_error($stmt)
            ]);
        }

        // 5. Tutup statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query (System Error)'
        ]);
    }

    // Tutup koneksi (opsional, tergantung struktur aplikasi Anda)
    mysqli_close($Conn);
?>


