<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

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

    //id_laboratorium wajib terisi
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Spesimen Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium' dan sanitasi
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    // membua Laboratorium Diagsnotic
    $Qry             = $Conn->prepare("SELECT * FROM laboratorium_diagnostic WHERE id_laboratorium = ?");
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
    $Data   = $Result->fetch_assoc();
    $Qry->close();
    if (empty($Data)) {
        $id_laboratorium_diagnostic = "";
        $conclusion                 = "";
        $clinical                   = "";
        $icd_10_code                = "";
        $icd_10_display             = "";
        $icd_10_system              = "";
    }else{
        // Buat Variabel
        $id_laboratorium_diagnostic = $Data['id_laboratorium_diagnostic'] ?? '';
        $conclusion                 = $Data['conclusion'] ?? '';
        $clinical                   = $Data['clinical'] ?? '';
        $icd_10_code                = $Data['icd_10_code'];
        $icd_10_display             = $Data['icd_10_display'];
        $icd_10_system              = $Data['icd_10_system'];
    }
    
?>
<input type="hidden" name="id_laboratorium" value="<?php echo $id_laboratorium; ?>">
<input type="hidden" name="id_laboratorium_diagnostic" value="<?php echo $id_laboratorium_diagnostic; ?>">

<div class="row mb-3">
    <div class="col-12 mb-2">
        <small>
            <b># <i>Diagnostic Report</i></b>
        </small>
    </div>
    <div class="col-12 mb-2">
        <div class="row mb-3">
            <div class="col-4"><label for="diagnostic_report_conclusion"><small>Kesimpulan (<i>Conclusion</i>)</small></label></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <textarea name="conclusion" id="diagnostic_report_conclusion" class="form-control"><?php echo $conclusion; ?></textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><label for="diagnostic_report_clinical"><small>Klinis (<i>Clinical</i>)</small></label></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <textarea name="clinical" id="diagnostic_report_clinical" class="form-control"><?php echo $clinical; ?></textarea>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12 mb-2">
        <small>
            <b># Referensi Diagnosis <i>ICD10 (<i>Diagnostic By ICD10</i>)</i></b>
        </small>
    </div>
    <div class="col-12 mb-2">
        <div class="row mb-3">
            <div class="col-4"><label for="id_icd_10"><small>Cari & Pilih ICD10</small></label></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <select name="id_icd_10" id="id_icd_10" class="form-control">
                    <option value="">Pilih</option>
                    <?php
                        if(!empty($icd_10_code)){
                            echo '
                                <option selected value="'.$icd_10_code.'">'.$icd_10_code.'-'.$icd_10_display.'</option>
                            ';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><label for="icd_10_code"><small><i>Diagnostic Code</i></small></label></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="icd_10_code" id="icd_10_code" class="form-control" value="<?php echo $icd_10_code; ?>">
            </div>
        </div>
         <div class="row mb-3">
            <div class="col-4"><label for="icd_10_display"><small><i>Diagnostic Display</i></small></label></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="text" name="icd_10_display" id="icd_10_display" class="form-control" value="<?php echo $icd_10_display; ?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><label for="icd_10_system"><small><i>Diagnostic System</i></small></label></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <input type="url" name="icd_10_system" id="icd_10_system" class="form-control" value="<?php echo $icd_10_system; ?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><small><i>Pernytaan Petugas Laboratorium</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <div class="row">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pernyataan_petugas" id="pernyataan_petugas" value="1" checked="">
                            <label class="form-check-label" for="pernyataan_petugas">
                                <small class="text text-dark">
                                    Dengan ini saya menyatakan bahwa DATA dan INFORMASI yang terkandung dalam dokumen <i>Diagnostic Report</i> ini telah melalui tahapan validasi dan memperoleh persetujuan dokter penerima pemeriksaan.
                                </small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


