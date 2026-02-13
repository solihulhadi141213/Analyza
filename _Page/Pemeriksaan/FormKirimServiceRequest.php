<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";
    
    //Zona Waktu Pakai UTC
    date_default_timezone_set('UTC');
    $datetime_now = new DateTime();

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    //id_laboratorium_rincian wajib terisi
    if(empty($_POST['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Rincian Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_rincian' dan sanitasi
    $id_laboratorium_rincian = validateAndSanitizeInput($_POST['id_laboratorium_rincian']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
    $Qry->bind_param("i", $id_laboratorium_rincian);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari tabel laboratorium_rincian!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    if (empty($Data)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    $id_laboratorium            = $Data['id_laboratorium'];
    $id_referensi_pemeriksaan   = $Data['id_referensi_pemeriksaan'];
    $nama_pemeriksaan           = $Data['nama_pemeriksaan'];
    $category_pemeriksaan       = $Data['category_pemeriksaan'];
    $metode_pemeriksaan         = $Data['metode_pemeriksaan'];
    $metode_pemeriksaan_display = $Data['metode_pemeriksaan_display'];
    $metode_pemeriksaan_code    = $Data['metode_pemeriksaan_code'];
    $metode_pemeriksaan_system  = $Data['metode_pemeriksaan_system'];

    // Membuka Referensi Pemeriksaan
    $display_pemeriksaan = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'display_pemeriksaan');
    $code_pemeriksaan    = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'code_pemeriksaan');
    $system_pemeriksaan  = GetDetailData($Conn, 'referensi_pemeriksaan', 'id_referensi_pemeriksaan', $id_referensi_pemeriksaan, 'system_pemeriksaan');

    // Jika Referensi Pemeriksaan Tidak Ada
    if(empty($code_pemeriksaan)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Kode Referensi Pemeriksaan Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buka Data Laboratorium
    $Qry2 = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry2->bind_param("s", $id_laboratorium);
    if (!$Qry2->execute()) {
        $error2=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari tabel laboratorium!<br>Keterangan : '.$error2.'</small>
            </div>
        ';
        exit;
    }
    $Result2 = $Qry2->get_result();
    $Data2 = $Result2->fetch_assoc();
    $Qry2->close();

    if (empty($Data2)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    $ihs_pasien           = $Data2['ihs_pasien'];
    $id_encounter         = $Data2['id_encounter'];
    $nama                 = $Data2['nama'];
    $gender               = $Data2['gender'];
    $priority             = $Data2['priority'];
    $ihs_dokter_pengirim  = $Data2['ihs_dokter_pengirim'];
    $nama_dokter_pengirim = $Data2['nama_dokter_pengirim'];
    $diagnosis            = $Data2['diagnosis'];
    $datetime_diminta     = $Data2['datetime_diminta'];

    // Membentuk Kode Lokal
    $kode_lokal_1    = "RSES";
    $kode_lokal_2    = date('YmdHis', strtotime($datetime_diminta));
    $kode_lokal_3    = $id_laboratorium_rincian;
    $kode_lokal_full = "$kode_lokal_1-$kode_lokal_2-$kode_lokal_3";

    // Mengubah Format Waktu
    $datetime = new DateTime($datetime_diminta, new DateTimeZone('Asia/Jakarta'));
    $datetime->setTimezone(new DateTimeZone('UTC'));              // Ubah ke UTC
    $iso8601               = $datetime->format('Y-m-d\TH:i:sP');  // Format ISO 8601

    // Ekstract Diagnosis
    $DiagnosisArry = json_decode($diagnosis, true);
    $diagnosis_code    = $DiagnosisArry['code'] ?? '-';
    $diagnosis_display = $DiagnosisArry['display'] ?? '-';
    $diagnosis_system  = $DiagnosisArry['system'] ?? '-';

    // Membuka Pengaturan Koneksi Satusehat
    $status_active = 1;
    $stmt = $Conn->prepare("SELECT organization_id FROM connection_satu_sehat WHERE status_connection_satu_sehat = ?");
    $stmt->bind_param("i", $status_active);
    $stmt->execute();
    $result = $stmt->get_result();
    $config = $result->fetch_assoc();
    $stmt->close();

    if (!$config) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi SATUSEHAT Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }
    if(empty($config['organization_id'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Koneksi SATUSEHAT Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    $organization_id = $config['organization_id'];

    // Membuat Payload
    $payload = [
        "resourceType" => "ServiceRequest",
        "identifier"   => [
            [
                "system" => "http://sys-ids.kemkes.go.id/servicerequest/$organization_id",
                "value"  => $kode_lokal_full
            ]
        ],
        "status"   => "active",
        "intent"   => "original-order",
        "priority" => $priority,
        "category" => [
            [
                "coding" => [
                    [
                        "system"  => "http://snomed.info/sct",
                        "code"    => "108252007",
                        "display" => "Laboratory procedure",
                    ]
                ]
            ]
        ],
        "code" =>[
            "coding" => [
                [
                    "code"    => $code_pemeriksaan,
                    "display" => $display_pemeriksaan,
                    "system"  => $system_pemeriksaan
                ]
            ],
            "text" => $nama_pemeriksaan
        ],
        "subject" => [
            "reference" => "Patient/$ihs_pasien"
        ],
        "encounter" => [
            "reference" => "Encounter/$id_encounter"
        ],
        "occurrenceDateTime" => $datetime_now->format(DateTime::ATOM),
        "authoredOn" => $iso8601,
        "requester" => [
            "reference" => "Practitioner/$ihs_dokter_pengirim",
            "display" => "$nama_dokter_pengirim"
        ],
        "reasonCode" => [
            [
                "coding" => [
                    [
                        "code"    => $diagnosis_code,
                        "display" => $diagnosis_display,
                        "system"  => $diagnosis_system
                    ]
                ]
            ]
        ],
        "performer" => [
            [
                "reference" => "Organization/c0ed680d-af87-485e-90b3-ab22db118661",
                "display" => "Laboratorium RS"
            ]
        ]

    ];

    // Ubah Array Payload Menjadi Json
    $PayloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $PayloadJsonHtml = htmlspecialchars($PayloadJson, ENT_QUOTES, 'UTF-8');

    // Menampilkan Form
    echo '
        <div class="row mb-3">
            <div class="col-md-12">
                <b>Payload :</b>
                <pre class="bg-light border rounded p-3 mb-0" style="max-height: 320px; overflow: auto;"><code>'.$PayloadJsonHtml.'</code></pre>
            </div> 
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <input type="hidden" name="id_laboratorium_rincian" value="'.$id_laboratorium_rincian.'">
                <input type="hidden" name="payload" value="'.$PayloadJsonHtml.'">
            </div> 
        </div>
    ';
    
?>
