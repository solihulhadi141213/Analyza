<?php
    /* Header JSON */
    header('Content-Type: application/json');

    /* Koneksi Database */
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    /* Response default */
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan sistem'
    ];

    // Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang!'
        ]);
        exit;
    }

    // Validasi ID
    if (empty($_POST['id_referensi_metode_pemeriksaan'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Referensi Metode Pemeriksaan Tidak Boleh Kosong!'
        ]);
        exit;
    }

    // Validasi Data Wajib (Mandatory)
    if (empty($_POST['nama_metode_pemeriksaan'])) {
        echo json_encode(['status'  => 'error', 'message' => 'Nama Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if (empty($_POST['display_metode_pemeriksaan'])) {
        echo json_encode(['status'  => 'error', 'message' => 'Referensi Display Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if (empty($_POST['code_metode_pemeriksaan'])) {
        echo json_encode(['status'  => 'error', 'message' => 'Referensi Kode Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    if (empty($_POST['system_metode_pemeriksaan'])) {
        echo json_encode(['status'  => 'error', 'message' => 'Referensi System Metode Pemeriksaan Tidak Boleh Kosong!']);
        exit;
    }

    // Buat Variabel
    $id_referensi_metode_pemeriksaan = $_POST['id_referensi_metode_pemeriksaan'];
    $nama_metode_pemeriksaan         = $_POST['nama_metode_pemeriksaan'];
    $display_metode_pemeriksaan      = $_POST['display_metode_pemeriksaan'];
    $code_metode_pemeriksaan         = $_POST['code_metode_pemeriksaan'];
    $system_metode_pemeriksaan       = $_POST['system_metode_pemeriksaan'];

    // Ambil data lama untuk validasi perubahan code
    $query_old = $Conn->prepare("
        SELECT code_metode_pemeriksaan
        FROM referensi_metode_pemeriksaan
        WHERE id_referensi_metode_pemeriksaan = ?
    ");
    $query_old->bind_param("i", $id_referensi_metode_pemeriksaan);
    if (!$query_old->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem']);
        exit;
    }
    $result_old = $query_old->get_result();
    if ($result_old->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        exit;
    }
    $row_old = $result_old->fetch_assoc();
    $query_old->close();

    $code_metode_pemeriksaan_lama = $row_old['code_metode_pemeriksaan'];

    // Validasi Duplikat Data Jika Code Berubah
    if ($code_metode_pemeriksaan !== $code_metode_pemeriksaan_lama) {
        $validasi_duplikat_data = GetDetailData(
            $Conn,
            'referensi_metode_pemeriksaan',
            'code_metode_pemeriksaan',
            $code_metode_pemeriksaan,
            'id_referensi_metode_pemeriksaan'
        );
        if (!empty($validasi_duplikat_data)) {
            echo json_encode(['status'  => 'error', 'message' => 'Kode Metode Pemeriksaan Yang Anda Gunakan Sudah Terdaftar']);
            exit;
        }
    }

    // Simpan Perubahan Ke Database
    $query = $Conn->prepare("
        UPDATE referensi_metode_pemeriksaan SET
            nama_metode_pemeriksaan = ?,
            display_metode_pemeriksaan = ?,
            code_metode_pemeriksaan = ?,
            system_metode_pemeriksaan = ?
        WHERE id_referensi_metode_pemeriksaan = ?
    ");

    $query->bind_param(
        "ssssi",
        $nama_metode_pemeriksaan,
        $display_metode_pemeriksaan,
        $code_metode_pemeriksaan,
        $system_metode_pemeriksaan,
        $id_referensi_metode_pemeriksaan
    );

    if ($query->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Data referensi Metode Pemeriksaan berhasil diperbarui'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan data'
        ]);
    }

    $query->close();
    $Conn->close();
?>
