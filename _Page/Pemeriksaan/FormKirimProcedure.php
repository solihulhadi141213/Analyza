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

    //id_laboratorium wajib terisi
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium' dan sanitasi
    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);

    //Buka Detail Koneksi Dengan Prepared Statment
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
    $datetime_diminta     = $Data['datetime_diminta'] ?? date('Y-m-d H:i:s');
    $tanggal_default      = date('Y-m-d', strtotime($datetime_diminta));
    $jam_default          = date('H:i', strtotime($datetime_diminta));

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

    // Nilai default radio: item pertama
    $default_item        = $status_puasa[0];
    $default_description = htmlspecialchars($default_item['description'] ?? '', ENT_QUOTES, 'UTF-8');
    $default_display     = htmlspecialchars($default_item['display'] ?? '', ENT_QUOTES, 'UTF-8');
    $default_code        = htmlspecialchars($default_item['code'] ?? '', ENT_QUOTES, 'UTF-8');
    $default_system      = htmlspecialchars($default_item['system'] ?? '', ENT_QUOTES, 'UTF-8');

    echo '
        <input type="hidden" name="id_laboratorium" value="'.$id_laboratorium.'">
        <input type="hidden" name="procedure_description" id="procedure_description" value="'.$default_description.'">
        <input type="hidden" name="procedure_display" id="procedure_display" value="'.$default_display.'">
        <input type="hidden" name="procedure_system" id="procedure_system" value="'.$default_system.'">
    ';
?>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="nama_pasien"><small>Nama Pasien</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" value="<?php echo $nama; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="ihs_pasien"><small>IHS Pasien</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="ihs_pasien" id="ihs_pasien" class="form-control" value="<?php echo $ihs_pasien; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="id_encounter"><small>ID Encounter</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="id_encounter" id="id_encounter" class="form-control" value="<?php echo $id_encounter; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="tanggal_mulai"><small>Tanggal/Waktu Mulai</small></label>
    </div>
    <div class="col-md-4">
        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo $tanggal_default; ?>" required>
    </div>
    <div class="col-md-4">
        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="<?php echo $jam_default; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="tanggal_selesai"><small>Tanggal/Waktu Selesai</small></label>
    </div>
    <div class="col-md-4">
        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo $tanggal_default; ?>" required>
    </div>
    <div class="col-md-4">
        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="<?php echo $jam_default; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="ihs_dokter_penerima"><small>IHS Dokter</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="ihs_dokter_penerima" id="ihs_dokter_penerima" class="form-control" value="<?php echo $ihs_dokter_penerima; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="nama_dokter_penerima"><small>Nama Dokter</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="nama_dokter_penerima" id="nama_dokter_penerima" class="form-control" value="<?php echo $nama_dokter_penerima; ?>" required>
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
                        foreach ($status_puasa as $index => $item) {
                            $description = htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8');
                            $display     = htmlspecialchars($item['display'] ?? '', ENT_QUOTES, 'UTF-8');
                            $code        = htmlspecialchars($item['code'] ?? '', ENT_QUOTES, 'UTF-8');
                            $system      = htmlspecialchars($item['system'] ?? '', ENT_QUOTES, 'UTF-8');
                            $id_radio    = 'procedure_code_' . ($index + 1);
                            $checked     = ($index === 0) ? 'checked' : '';

                            echo '
                                <tr>
                                    <td class="text-center">
                                        <input
                                            class="form-check-input radio-status-puasa"
                                            type="radio"
                                            name="procedure_code"
                                            id="'.$id_radio.'"
                                            value="'.$code.'"
                                            data-description="'.$description.'"
                                            data-display="'.$display.'"
                                            data-system="'.$system.'"
                                            '.$checked.'
                                        >
                                    </td>
                                    <td class="text-left">
                                        <label for="'.$id_radio.'" class="mb-0">
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
            <input class="form-check-input" checked type="checkbox" id="kirim_procedure" name="kirim_procedure" value="1">
            <label class="form-check-label" for="kirim_procedure">
                <small>Kirim <i>Procedure</i> Puasa Ke Resource SATUSEHAT</small>
            </label>
        </div>
    </div>
</div>
<script>
    (function () {
        var radios = document.querySelectorAll('.radio-status-puasa');
        var elDescription = document.getElementById('procedure_description');
        var elDisplay = document.getElementById('procedure_display');
        var elSystem = document.getElementById('procedure_system');

        function syncHidden(target) {
            if (!target) return;
            if (elDescription) elDescription.value = target.dataset.description || '';
            if (elDisplay) elDisplay.value = target.dataset.display || '';
            if (elSystem) elSystem.value = target.dataset.system || '';
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                syncHidden(radio);
            });
            if (radio.checked) {
                syncHidden(radio);
            }
        });
    })();
</script>
