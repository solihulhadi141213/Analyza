<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    require '../../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\IOFactory;

    header('Content-Type: application/json');

    // ============================
    // VALIDASI SESSION
    // ============================
    if (empty($SessionIdAccess)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses telah berakhir'
        ]);
        exit;
    }

    // ============================
    // VALIDASI FILE
    // ============================
    if (empty($_FILES['file_import']['name'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'File import tidak ditemukan'
        ]);
        exit;
    }

    $file_tmp = $_FILES['file_import']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file_tmp);
    } catch (Exception $e) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'File excel tidak valid'
        ]);
        exit;
    }

    $sheet = $spreadsheet->getActiveSheet();
    $data  = $sheet->toArray();

    $totalRow = count($data);
    $log = [];

    // ============================
    // PROSES PER BARIS (SKIP HEADER)
    // ============================
    for ($i = 1; $i < $totalRow; $i++) {

        $nama_unit = trim($data[$i][1]);  // Kolom B
        $unit      = trim($data[$i][2]);  // Kolom C
        $code      = trim($data[$i][3]);  // Kolom D
        $system    = trim($data[$i][4]);  // Kolom E

        // Validasi wajib
        if (empty($unit) || empty($code)) {
            $log[] = [
                'status'  => 'error',
                'message' => "Baris ".($i+1)." : Unit atau Code kosong"
            ];
            continue;
        }

        // Escape string
        $nama_unit = mysqli_real_escape_string($Conn, $nama_unit);
        $unit      = mysqli_real_escape_string($Conn, $unit);
        $code      = mysqli_real_escape_string($Conn, $code);
        $system    = mysqli_real_escape_string($Conn, $system);

        // ============================
        // CEK DUPLIKAT CODE
        // ============================
        $cek = mysqli_query($Conn,"SELECT id_referensi_satuan FROM referensi_satuan WHERE code_satuan='$code'");

        if (mysqli_num_rows($cek) > 0) {

            // UPDATE DATA
            $update = mysqli_query($Conn, "UPDATE referensi_satuan SET
                    nama_satuan = '$nama_unit',
                    unit_satuan = '$unit',
                    system_satuan = '$system'
                WHERE code_satuan = '$code'
            ");

            if ($update) {
                $log[] = [
                    'status'  => 'success',
                    'message' => "Baris ".($i+1)." : Update berhasil ($code)"
                ];
            } else {
                $log[] = [
                    'status'  => 'error',
                    'message' => "Baris ".($i+1)." : Gagal update ($code)"
                ];
            }

        } else {

            // INSERT DATA
            $insert = mysqli_query($Conn, "INSERT INTO referensi_satuan (nama_satuan, unit_satuan, code_satuan, system_satuan) VALUES ('$nama_unit', '$unit', '$code', '$system')");

            if ($insert) {
                $log[] = [
                    'status'  => 'success',
                    'message' => "Baris ".($i+1)." : Insert berhasil ($code)"
                ];
            } else {
                $log[] = [
                    'status'  => 'error',
                    'message' => "Baris ".($i+1)." : Gagal insert ($code)"
                ];
            }
        }
    }

    // ============================
    // RESPONSE AKHIR
    // ============================
    echo json_encode([
        'status' => 'success',
        'log'    => $log
    ]);
    exit;

?>