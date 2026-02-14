<?php
     //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
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

    //id_laboratorium_spesimen wajib terisi
    if(empty($_POST['id_laboratorium_spesimen'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Spesimen Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_spesimen' dan sanitasi
    $id_laboratorium_spesimen = validateAndSanitizeInput($_POST['id_laboratorium_spesimen']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_spesimen WHERE id_laboratorium_spesimen = ?");
    $Qry->bind_param("i", $id_laboratorium_spesimen);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
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
                <small>Data spesimen tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Validasi Apakah Spesimen Sudah Di Kirim Ke Satusehat
    if(!empty($Data['id_speciment'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Spesimen Sudah Dikirim Ke SATUSEHAT!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium       = $Data['id_laboratorium'];
    $datetime_spesimen     = $Data['datetime_spesimen'];
    $nama_spesimen         = $Data['nama_spesimen'];
    $display_spesimen      = $Data['display_spesimen'];
    $code_spesimen         = $Data['code_spesimen'];
    $system_spesimen       = $Data['system_spesimen'];
    $nama_metode_sample    = $Data['nama_metode_sample'];
    $display_metode_sample = $Data['display_metode_sample'];
    $code_metode_sample    = $Data['code_metode_sample'];
    $system_metode_sample  = $Data['system_metode_sample'];
    $bodysite_nama         = $Data['bodysite_nama'];
    $bodysite_display      = $Data['bodysite_display'];
    $bodysite_code         = $Data['bodysite_code'];
    $bodysite_system       = $Data['bodysite_system'];
    $nama_container        = $Data['nama_container'];
    $display_container     = $Data['display_container'];
    $code_container        = $Data['code_container'];
    $system_container      = $Data['system_container'];
    $quantity_value        = round((float) ($Data['quantity_value'] ?? 0), 2);
    $quantity_unit         = $Data['quantity_unit'];
    $quantity_code         = $Data['quantity_code'];
    $quantity_system       = $Data['quantity_system'];
    $collector_name        = $Data['collector_name'];
    $collector_ihs         = $Data['collector_ihs'];
    
    // Membuat Kode Lokal
    $KodeSpesimen = "LAB-SPC-DEV3-$id_laboratorium_spesimen";

    // Konversi Datetime
    $datetime = new DateTime($datetime_spesimen, new DateTimeZone('Asia/Jakarta'));
    $datetime->setTimezone(new DateTimeZone('UTC'));              // Ubah ke UTC
    $iso8601               = $datetime->format('Y-m-d\TH:i:sP');  // Format ISO 8601

    //----------------------------------------------------------------------------------------------------------------
    // Membuka Informasi Laboratorium
    //----------------------------------------------------------------------------------------------------------------
    $QryLab = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $QryLab->bind_param("s", $id_laboratorium);
    if (!$QryLab->execute()) {
        $ErroLab=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data Laboratorium!<br>Keterangan : '.$ErroLab.'</small>
            </div>
        ';
        exit;
    }
    $ResultLab = $QryLab->get_result();
    $DataLab = $ResultLab->fetch_assoc();
    $QryLab->close();
    if (empty($DataLab)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    $id_pasien  = $DataLab['id_pasien'];
    $ihs_pasien = $DataLab['ihs_pasien'];
    $nama       = $DataLab['nama'];
    $puasa      = $DataLab['puasa'];

    // Apabila IHS Pasien Kosong
    if(empty($DataLab['ihs_pasien'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>IHS pasien Tidak Boleh Kosong! Silahkan lengkapi data pasien terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    // Membuka Status Puasa
    if(empty($DataLab['puasa'])){
        $puasa_display = "Non-fasting";
        $puasa_code    = "NF";
        $puasa_system  = "http://terminology.hl7.org/CodeSystem/v2-0916";
    }else{
        $puasa_display = "Fasting";
        $puasa_code    = "F";
        $puasa_system  = "http://terminology.hl7.org/CodeSystem/v2-0916";
    }

    //----------------------------------------------------------------------------------------------------------------
    // Membuka Informasi Kontainer
    //----------------------------------------------------------------------------------------------------------------
    $QryKon = $Conn->prepare("SELECT * FROM referensi_container WHERE code_container = ?");
    $QryKon->bind_param("s", $code_container);
    if (!$QryKon->execute()) {
        $ErrorKon=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka tabel referensi_container!<br>Keterangan : '.$ErrorKon.'</small>
            </div>
        ';
        exit;
    }
    $ResultKon = $QryKon->get_result();
    $DataKon = $ResultKon->fetch_assoc();
    $QryKon->close();
    if (empty($DataKon)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data referensi_container tidak ditemukan!</small>
            </div>
        ';
        exit;
    }
    $id_referensi_container = $DataKon['id_referensi_container'];
    $kapasitas_container    = round((float) ($DataKon['kapasitas_container'] ?? 0), 2);
    $unit_container         = $DataKon['unit_container'];
    $code_unit_container    = $DataKon['code_unit_container'];
    $system_unit_container  = $DataKon['system_unit_container'];


    //----------------------------------------------------------------------------------------------------------------
    // Laboratorium Rincian
    //----------------------------------------------------------------------------------------------------------------
    $ServiceRequest = [];
    $stmtRincian = $Conn->prepare("
        SELECT id_service_request, nama_pemeriksaan
        FROM laboratorium_rincian
        WHERE id_laboratorium_spesimen = ?
    ");
    if (!$stmtRincian) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal menyiapkan query data rincian laboratorium</small>
            </div>
        ';
        exit;
    }
    $stmtRincian->bind_param("i", $id_laboratorium_spesimen);
    if (!$stmtRincian->execute()) {
        $stmtRincian->close();
        echo '
            <div class="alert alert-danger text-center">
                <small>Gagal membuka data rincian laboratorium</small>
            </div>
        ';
        exit;
    }
    $resultRincian = $stmtRincian->get_result();
    while ($data_rincian = $resultRincian->fetch_assoc()) {
        $id_service_request = trim((string) ($data_rincian['id_service_request'] ?? ''));
        if ($id_service_request !== '') {
            $nama_pemeriksaan = trim((string) ($data_rincian['nama_pemeriksaan'] ?? ''));
            if ($nama_pemeriksaan !== '' && stripos($nama_pemeriksaan, 'Pemeriksaan ') !== 0) {
                $nama_pemeriksaan = 'Pemeriksaan ' . $nama_pemeriksaan;
            }
            $ServiceRequest[] = [
                'reference' => 'ServiceRequest/' . $id_service_request,
                'display'   => $nama_pemeriksaan
            ];
        }
    }
    $stmtRincian->close();

    if (empty($ServiceRequest)) {
         echo '
            <div class="alert alert-danger text-center">
                <small>
                    Belum Ada Referensi <b>Service Request</b> Yang Digunakan!<br>
                    Silahkan Kirim Service Request Terlebih Dulu Ke SATUSEHAT
                </small>
            </div>
        ';
        exit;
    }
    //----------------------------------------------------------------------------------------------------------------
    // Membuka Pengaturan Koneksi Satusehat
    //----------------------------------------------------------------------------------------------------------------
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

    //----------------------------------------------------------------------------------------------------------------
    // Membuat Payload
    //----------------------------------------------------------------------------------------------------------------
    $payload = [
        "resourceType" => "Specimen",
        "identifier" =>[
            [
                "system" => "http://sys-ids.kemkes.go.id/specimen/$organization_id",
                "value" => $KodeSpesimen,
                "assigner" => [
                    "reference" => "Organization/$organization_id"
                ]
            ]
        ],
        "status" => "available",
        "type" => [
            "coding" => [
                [
                    "system" => $system_spesimen,
                    "code" => $code_spesimen,
                    "display" => $display_spesimen,
                ]
            ]
        ],
        "subject" => [
            "reference" => "Patient/$ihs_pasien",
            "display" => "$nama"
        ],
        "request" => $ServiceRequest,
        "receivedTime" => $iso8601,
        "collection" => [
            "collectedDateTime" => $iso8601,
            "collector" => [
                "reference" => "Practitioner/$collector_ihs",
                "display" => $collector_name
            ],
            "bodySite" => [
                "coding" => [
                    [
                        "display" => $bodysite_display,
                        "code"    => $bodysite_code,
                        "system"  => $bodysite_system
                    ]
                ]
            ],
            "quantity" => [
                "value" => $quantity_value,
                "code" => $quantity_code,
                "unit" => $quantity_unit,
                "system" => $quantity_system,
            ],
            "method" => [
                "coding" => [
                    [
                        "display" => $display_metode_sample,
                        "code"    => $code_metode_sample,
                        "system"  => $system_metode_sample
                    ]
                ]
            ],
            "fastingStatusCodeableConcept" => [
                "coding" => [
                    [
                        "display" => $puasa_display,
                        "code"    => $puasa_code,
                        "system"  => $puasa_system
                    ]
                ]
            ]
        ],
        "container" =>[
            [
                "type" => [
                    "coding" => [
                        [
                            "display" => $display_container,
                            "code"    => $code_container,
                            "system"  => $system_container
                        ]
                    ],
                    "text" => $nama_container
                ],
                "capacity" => [
                    "value" => $kapasitas_container,
                    "unit" => $unit_container,
                    "code" => $code_unit_container,
                    "system" => $system_unit_container
                ]
            ]
        ]
    ];

    // Ubah Array Payload Menjadi Json
    $PayloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $PayloadJsonHtml = htmlspecialchars($PayloadJson, ENT_QUOTES, 'UTF-8');

    //----------------------------------------------------------------------------------------------------------------
    // Menampilkan Form
    //----------------------------------------------------------------------------------------------------------------
    echo '
        <div class="row mb-3">
            <div class="col-md-12">
                <b>Payload :</b>
                <pre class="bg-light border rounded p-3 mb-0" style="max-height: 320px; overflow: auto;"><code>'.$PayloadJsonHtml.'</code></pre>
            </div> 
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <input type="hidden" name="id_laboratorium_spesimen" value="'.$id_laboratorium_spesimen.'">
                <input type="hidden" name="payload" value="'.$PayloadJsonHtml.'">
            </div> 
        </div>
    ';
?>
