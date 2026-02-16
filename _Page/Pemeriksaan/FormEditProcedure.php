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

    //id_laboratorium_procedure wajib terisi
    if(empty($_POST['id_laboratorium_procedure'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Procedure Tidak Boleh Kosong!</small>
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
                <small>Data spesimen tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $id_laboratorium   = $Data['id_laboratorium'];
    if(empty($Data['id_procedure'])){
        $id_procedure      = '';
    }else{
        $id_procedure      = $Data['id_procedure'];
    }
    $id_laboratorium       = $Data['id_laboratorium'];
    $procedure_description = $Data['procedure_description'];
    $procedure_display     = $Data['procedure_display'];
    $procedure_code        = $Data['procedure_code'];
    $procedure_system      = $Data['procedure_system'];
    $datetime_start        = $Data['datetime_start'];
    $datetim_end           = $Data['datetim_end'];

    if(!empty($id_procedure)){
        $checked = "checked";
    }else{
        $checked = "";
    }

    // Buka Data Laboratorium
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

    // Baca referensi status puasa dari JSON
    $json_file = __DIR__ . '/status_puasa.json';
    if (!file_exists($json_file)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>File referensi status puasa tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    $json_content = file_get_contents($json_file);
    $status_puasa = json_decode($json_content, true);
    if (!is_array($status_puasa) || empty($status_puasa)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data referensi status puasa tidak valid atau kosong!</small>
            </div>
        ';
        exit;
    }

    echo '
        <input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">
        <input type="hidden" name="id_laboratorium_procedure" value="'.$id_laboratorium_procedure.'">
        <input type="hidden" name="id_procedure" value="'.$id_procedure.'">
    ';
?>
<div class="row mb-3">
    <div class="col-md-12">
        <small><b>A. Informasi pasien</b></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="nama_pasien_edit"><small>Nama Pasien</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="nama_pasien" id="nama_pasien_edit" class="form-control" value="<?php echo $nama; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="ihs_pasien_edit"><small>IHS Pasien</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="ihs_pasien" id="ihs_pasien_edit" class="form-control" value="<?php echo $ihs_pasien; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="id_encounter_edit"><small>ID Encounter</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="id_encounter" id="id_encounter_edit" class="form-control" value="<?php echo $id_encounter; ?>">
    </div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-md-12 mt-3">
        <small><b>B. Tanggl & Waktu</b></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="tanggal_mulai"><small>Tanggal/Waktu Mulai</small></label>
    </div>
    <div class="col-md-4">
        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo date('Y-m-d', strtotime($datetime_start)); ?>" required>
    </div>
    <div class="col-md-4">
        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="<?php echo date('H:i', strtotime($datetime_start)); ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="tanggal_selesai_edit"><small>Tanggal/Waktu Selesai</small></label>
    </div>
    <div class="col-md-4">
        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo date('Y-m-d', strtotime($datetim_end)); ?>" required>
    </div>
    <div class="col-md-4">
        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="<?php echo date('H:i', strtotime($datetim_end)); ?>" required>
    </div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-md-12 mt-3">
        <small><b>C. Dokter Penerima</b></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="ihs_dokter_penerima"><small>IHS Dokter</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" readonly name="ihs_dokter_penerima" id="ihs_dokter_penerima" class="form-control" value="<?php echo $ihs_dokter_penerima; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="nama_dokter_penerima_edit"><small>Nama Dokter</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" readonly name="nama_dokter_penerima" id="nama_dokter_penerima_edit" class="form-control" value="<?php echo $nama_dokter_penerima; ?>" required>
    </div>
</div>
<div class="row mb-3 mt-3">
    <div class="col-md-12 mt-3">
        <small><b>D. Procedure</b></small>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <td class="text-center"><b><i class="bi bi-check-circle"></i></b></td>
                        <td class="text-left"><b>Deskripsi</b></td>
                        <td class="text-left"><b><i>Display</i></b></td>
                        <td class="text-left"><b><i>Code</i></b></td>
                        <td class="text-left"><b><i>System</i></b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($status_puasa as $item) {
                            $description = htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8');
                            $display     = htmlspecialchars($item['display'] ?? '', ENT_QUOTES, 'UTF-8');
                            $code        = htmlspecialchars($item['code'] ?? '', ENT_QUOTES, 'UTF-8');
                            $system      = htmlspecialchars($item['system'] ?? '', ENT_QUOTES, 'UTF-8');
                            if($code==$procedure_code){
                                $checked_procedure = "checked";
                            }else{
                                $checked_procedure = "";
                            }
                            echo '
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input check_procedure_edit" type="radio" name="pilih_procedure" id="pilih_procedure'.$code.'" data-code="'.$code.'" data-description="'.$description.'" data-display="'.$display.'" data-system="'.$system.'" '.$checked_procedure.'>
                                    </td>
                                    <td class="text-left">
                                        <label for="pilih_procedure'.$code.'" class="mb-0">
                                            <small class="text text-grayish">'.$description.'</small>
                                        </label>
                                    </td>
                                    <td class="text-left"><small class="text text-grayish">'.$display.'</small></td>
                                    <td class="text-left"><small class="text text-grayish">'.$code.'</small></td>
                                    <td class="text-left"><small class="text text-grayish">'.$system.'</small></td>
                                </tr>
                            ';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="form-check">
            <input class="form-check-input" <?php echo $checked; ?>  type="checkbox" id="update_procedure" name="update_procedure" value="1">
            <label class="form-check-label" for="update_procedure">
                <small>Update <i>Procedure</i> Puasa Ke Resource SATUSEHAT</small>
            </label>
        </div>
    </div>
</div>
<?php
    echo '
        <input type="hidden" name="procedure_description" class="procedure_description_edit" value="'.$procedure_description.'">
        <input type="hidden" name="procedure_display" class="procedure_display_edit" value="'.$procedure_display.'">
        <input type="hidden" name="procedure_system" class="procedure_system_edit" value="'.$procedure_system.'">
        <input type="hidden" name="procedure_code" class="procedure_code_edit" value="'.$procedure_code.'">
    ';
?>

