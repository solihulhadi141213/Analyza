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
                <small>Data Rincian Pemeriksaan Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Apabila sudah ada id_observation
    if(!empty($Data['id_observation'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Observation Sudah Ada!</small>
            </div>
        ';
        exit;
    }

    // Jika id_service_request belum ada
    if(empty($Data['id_service_request'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID ServiceRequest Belum Ada!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium          = $Data['id_laboratorium'];
    $id_referensi_pemeriksaan = $Data['id_referensi_pemeriksaan'];
    $id_laboratorium_spesimen = $Data['id_laboratorium_spesimen'];
    $id_service_request       = $Data['id_service_request'];
    $id_referensi_category    = $Data['id_referensi_category'];
    $id_referensi_range       = $Data['id_referensi_range'];
    $nama_pemeriksaan         = $Data['nama_pemeriksaan'];
    $category_pemeriksaan     = $Data['category_pemeriksaan'];
    $metode_pemeriksaan       = $Data['metode_pemeriksaan'];

    // Buka Tabel laboratorium
    $QryLaboratorium = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $QryLaboratorium->bind_param("s", $id_laboratorium);
    if (!$QryLaboratorium->execute()) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$Conn->error.'</small>
            </div>
        ';
        exit;
    }

    $ResultLaboratorium = $QryLaboratorium->get_result();
    $DataLaboratorium   = $ResultLaboratorium->fetch_assoc();
    $QryLaboratorium->close();

    if (empty($DataLaboratorium)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $ihs_pasien        = $DataLaboratorium['ihs_pasien'] ?? '';
    $id_encounter      = $DataLaboratorium['id_encounter'] ?? '';
    $nama              = $DataLaboratorium['nama'] ?? '';

    // Validasi Dokter Penerima Tidak Boleh Kosong
    if(empty($DataLaboratorium['ihs_dokter_penerima'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Informasi IHS Dokter Penerima Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    $ihs_dokter_penerima  = $DataLaboratorium['ihs_dokter_penerima'];
    $nama_dokter_penerima = $DataLaboratorium['nama_dokter_penerima'];

    // Buka Referensi Pemeriksaan
    $QryReferensiPemeriksaan = $Conn->prepare("SELECT * FROM referensi_pemeriksaan WHERE id_referensi_pemeriksaan = ?");
    $QryReferensiPemeriksaan->bind_param("i", $id_referensi_pemeriksaan);
    if (!$QryReferensiPemeriksaan->execute()) {
        $ErrorReferensiPemeriksaan=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>Terjadi kesalahan pada saat membuka tabel Referensi Pemeriksaan!<br>Keterangan : '.$ErrorReferensiPemeriksaan.'</small>
            </div>
        ';
        exit;
    }
    $ResultReferensiPemeriksaan = $QryReferensiPemeriksaan->get_result();
    $DataReferensiPemeriksaan = $ResultReferensiPemeriksaan->fetch_assoc();
    $QryReferensiPemeriksaan->close();
    $result_type                = $DataReferensiPemeriksaan['result_type'];
    $result_interpertation_type = $DataReferensiPemeriksaan['result_interpertation_type'];

    // Apabila Waktu Spesimen dan hasil Belum Ada
    if(empty($DataLaboratorium['datetime_spesimen'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Informasi Waktu Spesimen Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    if(empty($DataLaboratorium['datetime_hasil'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Informasi Waktu Keluar Hasil Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }
    $datetime_spesimen = $DataLaboratorium['datetime_spesimen'];
    $datetime_hasil    = $DataLaboratorium['datetime_hasil'];

    // Menetapkan Zona Waktu
    $effectiveDateTimeZone = new DateTime($datetime_spesimen, new DateTimeZone('Asia/Jakarta'));
    $issuedTimeZone        = new DateTime($datetime_hasil, new DateTimeZone('Asia/Jakarta'));

    // Ubah ke UTC
    $effectiveDateTimeZone->setTimezone(new DateTimeZone('UTC'));
    $issuedTimeZone->setTimezone(new DateTimeZone('UTC'));

    // Format ISO 8601
    $effectiveDateTime = $effectiveDateTimeZone->format('Y-m-d\TH:i:sP');
    $issued            = $issuedTimeZone->format('Y-m-d\TH:i:sP');

    // Membuka Koneksi Satu Sehat
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
    $id_organization = $config['organization_id'];
    
    // Membuka id_specimen berdasarkan $id_laboratorium_spesimen
    $id_speciment = GetDetailData($Conn, 'laboratorium_spesimen', 'id_laboratorium_spesimen', $id_laboratorium_spesimen, 'id_speciment');
    if(empty($id_speciment)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Hasil Pemeriksaan Belum Dilengkapi ID Speciment Dari SATUSEHAT</small>
            </div>
        ';
        exit;
    }

    // Field dinamis untuk payload Observation
    $valueQuantity        = null;
    $interpretation       = null;
    $valueCodeableConcept = null;
    $valueString          = null;

    // Apabila $result_type adalah decimal dan numeric, sisipkan valueQuantity
    if($result_type=="Numeric" || $result_type=="Decimal"){
        $valueQuantity = [
            "value" => (float) $Data['hasil'],
            "unit" => $DataReferensiPemeriksaan['unit'],
            "system" => $DataReferensiPemeriksaan['unit_system'],
            "code" => $DataReferensiPemeriksaan['unit_code'],
        ];
    }
    if($result_type=="Text"){
        $valueString = $Data['hasil'];
    }

    if($result_interpertation_type=="Range"){
        // Buka id_referensi_range
        $stmt_range = $Conn->prepare("SELECT * FROM referensi_range WHERE id_referensi_range = ?");
        $stmt_range->bind_param("i", $id_referensi_range);
        $stmt_range->execute();
        $result_range = $stmt_range->get_result();
        $data_range = $result_range->fetch_assoc();
        $stmt_range->close();

        if(!empty($data_range)){
            $interpretation = [
                [
                    "coding" => [
                        [
                            "system"  => $data_range['fhir_system'],
                            "code"    => $data_range['fhir_code'],
                            "display" => $data_range['fhir_display'],
                        ]
                    ]
                ]
            ];
        }
    }

    if($result_interpertation_type=="Category"){
        // Buka id_referensi_category
        $stmt_category = $Conn->prepare("SELECT * FROM referensi_category WHERE id_referensi_category = ?");
        $stmt_category->bind_param("i", $id_referensi_category);
        $stmt_category->execute();
        $result_category = $stmt_category->get_result();
        $data_category = $result_category->fetch_assoc();
        $stmt_category->close();

        if(!empty($data_category)){
            $valueCodeableConcept = [
                "coding" => [
                    [
                        "system"  => $data_category['fhir_system'],
                        "code"    => $data_category['fhir_code'],
                        "display" => $data_category['fhir_display'],
                    ]
                ]
            ];
        }
    }

    // Payload
    $payload = [
        "resourceType" => "Observation",
        "status"       => "final",
        "category"     => [
            [
                "coding" => [
                    [
                        "system"  => "http://terminology.hl7.org/CodeSystem/observation-category",
                        "code"    => "laboratory",
                        "display" => "Laboratory",
                    ]
                ]
            ]
        ],
        "code" => [
            "coding" => [
                [
                    "system"  => $DataReferensiPemeriksaan['system_pemeriksaan'],
                    "code"    => $DataReferensiPemeriksaan['code_pemeriksaan'],
                    "display" => $DataReferensiPemeriksaan['display_pemeriksaan'],
                ]
            ],
            "text" => $Data['nama_pemeriksaan'],
        ],
        "subject" => [
            "reference" => "Patient/$ihs_pasien",
            "display"   => $nama,
        ],
        "encounter" => [
            "reference" => "Encounter/$id_encounter"
        ],
        "effectiveDateTime" => $effectiveDateTime,
        "issued"            => $issued,
        "performer" => [
            [
                "reference" => "Practitioner/$ihs_dokter_penerima",
                "display" => $nama_dokter_penerima,
            ],
            [
                "reference" => "Organization/$id_organization",
                "display" => "RSU EL-SYIFA KUNINGAN",
            ]
        ],
        "specimen" => [
            "reference" => "Specimen/$id_speciment"
        ],
        "method" => [
            "coding" => [
                [
                    "system"  => $Data['metode_pemeriksaan_system'],
                    "code"    => $Data['metode_pemeriksaan_code'],
                    "display" => $Data['metode_pemeriksaan_display'],
                ]
            ]
        ],
        "basedOn" => [
            [
                "reference" => "ServiceRequest/$id_service_request"
            ]
        ]
    ];

    // Sisipkan key dinamis bila ada nilainya
    if(!empty($valueQuantity)){
        $payload['valueQuantity'] = $valueQuantity;
    }
    if(!empty($interpretation)){
        $payload['interpretation'] = $interpretation;
    }
    if(!empty($valueCodeableConcept)){
        $payload['valueCodeableConcept'] = $valueCodeableConcept;
    }
    if(!empty($valueString)){
        $payload['valueString'] = $valueString;
    }

    // Ubah Array Payload Menjadi Json
    $PayloadJson     = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
                <input type="hidden" name="id_laboratorium_rincian" value="'.$id_laboratorium_rincian.'">
                <input type="hidden" name="payload" value="'.$PayloadJsonHtml.'">
            </div> 
        </div>
    ';
?>
