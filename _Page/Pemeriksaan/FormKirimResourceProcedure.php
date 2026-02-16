<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger text-center">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
            </div>
        ';
        exit;
    }

    //id_laboratorium_procedure wajib terisi
    if(empty($_POST['id_laboratorium_procedure'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium_procedure' dan sanitasi
    $id_laboratorium_procedure = validateAndSanitizeInput($_POST['id_laboratorium_procedure']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM laboratorium_procedure WHERE id_laboratorium_procedure = ?");
    $Qry->bind_param("i", $id_laboratorium_procedure);
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
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Ambil datetime default dari data laboratorium
    $id_laboratorium       = $Data['id_laboratorium'];
    $procedure_description = $Data['procedure_description'];
    $procedure_display     = $Data['procedure_display'];
    $procedure_code        = $Data['procedure_code'];
    $procedure_system      = $Data['procedure_system'];
    $datetime_start        = $Data['datetime_start'];
    $datetim_end           = $Data['datetim_end'];

    // Ubah datetime Ke Format ISO
    $datetime_start_Zone = new DateTime($datetime_start, new DateTimeZone('Asia/Jakarta'));
    $datetim_end_Zone    = new DateTime($datetim_end, new DateTimeZone('Asia/Jakarta'));
    
    // Ubah ke UTC
    $datetime_start_Zone->setTimezone(new DateTimeZone('UTC'));
    $datetim_end_Zone->setTimezone(new DateTimeZone('UTC'));

    // Format ISO 8601
    $performedPeriodStart = $datetime_start_Zone->format('Y-m-d\TH:i:sP');
    $performedPeriodEnd   = $datetim_end_Zone->format('Y-m-d\TH:i:sP');

    // Sekarang Buka id_laboratorium
    $Qry = $Conn->prepare("SELECT * FROM laboratorium WHERE id_laboratorium = ?");
    $Qry->bind_param("s", $id_laboratorium);
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
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Ambil datetime default dari data laboratorium
    $ihs_pasien           = $Data['ihs_pasien'];
    $nama                 = $Data['nama'];
    $id_encounter         = $Data['id_encounter'];
    $ihs_dokter_penerima  = $Data['ihs_dokter_penerima'];
    $nama_dokter_penerima = $Data['nama_dokter_penerima'];

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
                "display" => $nama
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
   
    echo '
        <input type="hidden" name="id_laboratorium_procedure" value="'.$id_laboratorium_procedure.'">
        <input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">
    ';
?>
<div class="row mb-3">
    <div class="col-12"><small><b>1. Informasi Pasien</b></small>></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>IHS Pasien</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $ihs_pasien; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>ID Encounter</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $id_encounter; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Nama Pasien</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $nama; ?></small></div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-12"><small><b>2. Dokter Penerima</b></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>IHS Dokter</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $ihs_dokter_penerima; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Nama Dokter</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $nama_dokter_penerima; ?></small></div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-12"><small><b>3. Referensi <i>Procedure</i></b></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Descryption</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $procedure_description; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Display</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $procedure_display; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Code</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $procedure_code; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>System</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $procedure_system; ?></small></div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-12"><small><b>4. <i>Performed Period</i></b></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Period Start</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $performedPeriodStart; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small><i>Period End</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $performedPeriodEnd; ?></small></div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-12 mb-2">
        <small><b>5. <i>Payload</i></b></small>
    </div>
    <div class="col-12 mb-2">
        <textarea name="payload_procedure" id="payload_procedure" class="form-control"><?php echo $PayloadJson; ?></textarea>
    </div>
</div>


