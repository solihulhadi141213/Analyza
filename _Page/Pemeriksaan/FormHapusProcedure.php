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

    echo '
        <input type="hidden" name="id_laboratorium_procedure" value="'.$id_laboratorium_procedure.'">
        <input type="hidden" name="id_procedure" value="'.$id_procedure.'">
    ';
?>
<div class="row mb-2">
    <div class="col-4"><small><i>ID Procedure</i></small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-md-7"><small class="text text-grayish"><?php echo $id_procedure; ?></small></div>
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
<div class="row mb-3">
    <div class="col-md-12">
        <div class="form-check">
            <input class="form-check-input" <?php echo $checked; ?>  type="checkbox" id="update_procedure" name="update_procedure" value="1">
            <label class="form-check-label" for="update_procedure">
                <small>Hapus <i>Procedure</i> Puasa Dari Resource SATUSEHAT</small>
            </label>
        </div>
    </div>
</div>
 <div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>Apakah anda yakin ingin menghapus data ini?</small>
        </div>
    </div>
</div>
