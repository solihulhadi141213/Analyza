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

    // Validasi Data Wajib (Mandatory)
    if(empty($_POST['id_laboratorium'])){
        echo json_encode(['status'  => 'error','message' => 'ID Laboratorium Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['nama_pasien'])){
        echo json_encode(['status'  => 'error','message' => 'Nama Pasien Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['tanggal_mulai'])){
        echo json_encode(['status'  => 'error','message' => 'Tanggal Mulai Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['jam_mulai'])){
        echo json_encode(['status'  => 'error','message' => 'Jam Mulai Data Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['tanggal_selesai'])){
        echo json_encode(['status'  => 'error','message' => 'Tanggal Selesai Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['jam_selesai'])){
        echo json_encode(['status'  => 'error','message' => 'Jam Selesai Data Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['nama_dokter_penerima'])){
        echo json_encode(['status'  => 'error','message' => 'Dokter Penerima Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['procedure_description'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Procedure (Descryption) Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['procedure_display'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Procedure (Display) Tidak Boleh Kosong!']);
        exit;
    }
    if(empty($_POST['procedure_system'])){
        echo json_encode(['status'  => 'error','message' => 'Referensi Procedure (System) Tidak Boleh Kosong!']);
        exit;
    }
    $id_laboratorium       = $_POST['id_laboratorium'];
    $nama_pasien           = $_POST['nama_pasien'];
    $tanggal_mulai         = $_POST['tanggal_mulai'];
    $jam_mulai             = $_POST['jam_mulai'];
    $tanggal_selesai       = $_POST['tanggal_selesai'];
    $jam_selesai           = $_POST['jam_selesai'];
    $nama_dokter_penerima  = $_POST['nama_dokter_penerima'];
    $procedure_description = $_POST['procedure_description'];
    $procedure_display     = $_POST['procedure_display'];
    $procedure_code        = $_POST['procedure_code'];
    $procedure_system      = $_POST['procedure_system'];

    // Menangkap Data Yang Tidak Wajib
    if(empty($_POST['ihs_pasien'])){
        $ihs_pasien = "";
    }else{
        $ihs_pasien = $_POST['ihs_pasien'];
    }
    if(empty($_POST['id_encounter'])){
        $id_encounter = "";
    }else{
        $id_encounter = $_POST['id_encounter'];
    }
    if(empty($_POST['ihs_dokter_penerima'])){
        $ihs_dokter_penerima = "";
    }else{
        $ihs_dokter_penerima = $_POST['ihs_dokter_penerima'];
    }
    if(empty($_POST['kirim_procedure'])){
        $kirim_procedure = 0;
    }else{
        $kirim_procedure = $_POST['kirim_procedure'];
    }

    // Inisialisasi Status Puasa
    if($procedure_code=="313304003"){
        $status_puasa_baru = 0;
    }else{
        $status_puasa_baru = 1;
    }

    // Menyusun datetime
    $datetime_start = "$tanggal_mulai $jam_mulai";
    $datetim_end    = "$tanggal_selesai $jam_selesai";

    // Default id_procedure
    $id_procedure = "";

    // Apabila User Memutuskan Untuk Mengirim Resource Satu Sehat
    if($kirim_procedure==1){
        // Lakukan Validasi Lanjutan
        if(empty($ihs_pasien)){
            echo json_encode(['status'  => 'error','message' => 'Untuk Pengiriman Resource <i>Procedure</i> SATUSEHAT, maka IHS Pasien Tidak Boleh Kosong!']);
            exit;
        }
        if(empty($id_encounter)){
            echo json_encode(['status'  => 'error','message' => 'Untuk Pengiriman Resource <i>Procedure</i> SATUSEHAT, maka ID Encounter Tidak Boleh Kosong!']);
            exit;
        }
        if(empty($ihs_dokter_penerima)){
            echo json_encode(['status'  => 'error','message' => 'Untuk Pengiriman Resource <i>Procedure</i> SATUSEHAT, maka IHS Dokter Tidak Boleh Kosong!']);
            exit;
        }

        // Ubah datetime Ke Format ISO
        $datetime_start_Zone = new DateTime($datetime_start, new DateTimeZone('Asia/Jakarta'));
        $datetim_end_Zone    = new DateTime($datetim_end, new DateTimeZone('Asia/Jakarta'));
        
        // Ubah ke UTC
        $datetime_start_Zone->setTimezone(new DateTimeZone('UTC'));
        $datetim_end_Zone->setTimezone(new DateTimeZone('UTC'));

        // Format ISO 8601
        $performedPeriodStart = $datetime_start_Zone->format('Y-m-d\TH:i:sP');
        $performedPeriodEnd   = $datetim_end_Zone->format('Y-m-d\TH:i:sP');

        // Buat Payload
        $payload = [
            "resourceType" => "Procedure",
            "status" => "not-done",
            "category" => [
                "coding" =>[
                    [
                        "display" => "Diagnostic procedure",
                        "code" => "TK000028",
                        "system" => "http://terminology.kemkes.go.id",
                    ]
                ],
                "text" => "Prosedur diagnostik"
            ],
            "code" => [
                "coding" =>[
                    [
                        "display" => $procedure_display,
                        "code" => $procedure_code,
                        "system" => $procedure_system,
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/$ihs_pasien",
                "display" => $nama_pasien
            ],
            "encounter" => [
                "reference" => "Encounter/$id_encounter"
            ],
            "performedPeriod" => [
                "start" => $performedPeriodStart,
                "end" => $performedPeriodEnd,
            ],
            "performer" => [
                [
                    "actor" => [
                        "reference" => "Practitioner/$ihs_dokter_penerima",
                        "display" => $nama_dokter_penerima
                    ]
                ]
            ],
            "note" => [
                [
                    "text" => $procedure_display
                ]
            ]
        ];

        // Payload To PayloadJson
        $PayloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        // Membuka Pengaturan Koneksi SATUSEHAT
        $status_active = 1;
        $stmt = $Conn->prepare("SELECT * FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
        $stmt->bind_param("i", $status_active);
        $stmt->execute();
        $result = $stmt->get_result();
        $config = $result->fetch_assoc();
        $stmt->close();

        if (!$config) {
            echo json_encode(['status'  => 'error','message' => 'Gagal Membuka Koneksi Satusehat!']);
            exit;
        }
        if(empty($config['organization_id'])){
        echo json_encode(['status'  => 'error','message' => 'Koneksi Satusehat Tidak Ditemukan']);
            exit;
        }

        // GENERATE TOKEN SATUSEHAT
        $tokenResult = generateTokenSatuSehat($Conn);

        if ($tokenResult['status'] !== 'success') {
            echo json_encode([
                'status'  => 'error',
                'message' => $tokenResult['message']
            ]);
            exit;
        }

        $token = $tokenResult['token'];

        // Menentukan URL
        $url_api = rtrim($config['url_connection_satu_sehat'], '/');
        $url_api = $url_api . '/fhir-r4/v1/Procedure';

        // KIRIM KE SATUSEHAT
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $PayloadJson,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        // HANDLE ERROR CURL
        if ($curl_error) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'cURL Error: ' . $curl_error,
                'payload' => $PayloadJson
            ]);
            exit;
        }

        // DECODE RESPONSE
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Response bukan JSON valid.',
                'response_raw' => substr($response, 0, 300),
                'payload' => $PayloadJson
            ]);
            exit;
        }

        // VALIDASI RESPONSE SATUSEHAT
        if ($http_code !== 201) {
            $msg = 'Gagal mengirim ServiceRequest ke SATUSEHAT';

            if (($result['resourceType'] ?? '') === 'OperationOutcome') {
                $msg = $result['issue'][0]['details']['text']
                    ?? $result['issue'][0]['diagnostics']
                    ?? $msg;
            }

            echo json_encode([
                'status'  => 'error',
                'message' => $msg,
                'http_code' => $http_code,
                'payload' => $PayloadJson
            ]);
            exit;
        }

        //id_procedure
        $id_procedure = $result['id'] ?? null;

        if(empty($id_procedure)){
            echo json_encode(['status'  => 'error','message' => 'Tidak Ada Response ID procedure yang diterima!']);
            exit;
        }
       
    }

    // Simpan Ke Database
    $query = $Conn->prepare("
        INSERT INTO laboratorium_procedure (
            id_laboratorium,
            id_procedure,
            procedure_description,
            procedure_display,
            procedure_code,
            procedure_system,
            datetime_start,
            datetim_end
        ) VALUES (?,?,?,?,?,?,?,?)
    ");

    $query->bind_param(
        "ssssssss",
        $id_laboratorium,
        $id_procedure,
        $procedure_description,
        $procedure_display,
        $procedure_code,
        $procedure_system,
        $datetime_start,
        $datetim_end
    );

    // ======================================================
    // EKSEKUSI
    // ======================================================
    if (!$query->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan data procedure'
        ]);
    }
    $query->close();
    
    // Update Ke Database Laboratorium
    $query2 = $Conn->prepare("UPDATE laboratorium SET puasa = ? WHERE id_laboratorium = ?");
    if (!$query2) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan query update'
        ]);
        exit;
    }

    $query2->bind_param(
        "si",
        $status_puasa_baru,
        $id_laboratorium
    );
    // Eksekusi
    if (!$query2->execute()) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data laboratorium'
        ]);
    }else{
        echo json_encode([
            'status'  => 'success',
            'message' => 'Procedure Berhasil Diperbaharui'
        ]);
    } 
    $query2->close();
    $Conn->close();
?>