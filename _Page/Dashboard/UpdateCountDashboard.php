<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Jakarta');

    $jsonFile = __DIR__ . '/JumlahPelayanan.json';
    $now = date('Y-m-d H:i:s');
    $nowTs = time();

    $response = [
        'status' => 'Success',
        'updated' => false,
        'message' => 'Data masih valid, tidak perlu update.'
    ];

    // Nilai default jika file JSON belum ada / rusak
    $jsonData = [
        'metadata' => [
            'last_update' => null,
            'expired' => '1970-01-01 00:00:00'
        ],
        'dataset' => []
    ];

    if (is_file($jsonFile) && is_readable($jsonFile)) {
        $raw = file_get_contents($jsonFile);
        if ($raw !== false) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $jsonData = array_replace_recursive($jsonData, $decoded);
            }
        }
    }

    $expiredRaw = $jsonData['metadata']['expired'] ?? '';
    $expiredTs = strtotime((string) $expiredRaw);

    // Update jika expired tidak valid atau sudah lewat
    $shouldUpdate = ($expiredTs === false || $expiredTs < $nowTs);

    if ($shouldUpdate) {
        $query = "
            SELECT
                DATE(datetime_diminta) AS tanggal,
                COUNT(*) AS jumlah
            FROM laboratorium
            WHERE datetime_diminta IS NOT NULL

            GROUP BY DATE(datetime_diminta)
            ORDER BY DATE(datetime_diminta) ASC
        ";

        $result = $Conn->query($query);
        if ($result === false) {
            echo json_encode([
                'status' => 'Error',
                'updated' => false,
                'message' => 'Gagal mengambil data laboratorium: ' . $Conn->error
            ]);
            exit;
        }

        $dataset = [];
        while ($row = $result->fetch_assoc()) {
            $dataset[] = [
                'datetime' => $row['tanggal'],
                'y' => (int) $row['jumlah']
            ];
        }
        $result->free();

        $lastUpdate = $now;
        $expired = date('Y-m-d H:i:s', strtotime($lastUpdate . ' +1 hour'));

        $jsonData = [
            'metadata' => [
                'last_update' => $lastUpdate,
                'expired' => $expired
            ],
            'dataset' => $dataset
        ];

        $written = file_put_contents(
            $jsonFile,
            json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );

        if ($written === false) {
            echo json_encode([
                'status' => 'Error',
                'updated' => false,
                'message' => 'Gagal menyimpan file JumlahPelayanan.json'
            ]);
            exit;
        }

        $response['updated'] = true;
        $response['message'] = 'JumlahPelayanan.json berhasil di-update.';
    }

    $response['metadata'] = $jsonData['metadata'];
    $response['total_tanggal'] = count($jsonData['dataset']);

    echo json_encode($response);
?>
