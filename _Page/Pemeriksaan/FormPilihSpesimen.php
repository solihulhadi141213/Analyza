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

    //id_laboratorium_rincian wajib terisi
    if(empty($_POST['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>ID Rincian Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_laboratorium' dan sanitasi
    $id_laboratorium_rincian = validateAndSanitizeInput($_POST['id_laboratorium_rincian']);

    // Buka ID Lab Berdasarkan id_laboratorium_rincian
    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT id_laboratorium FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
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
    $id_laboratorium = $Data['id_laboratorium'];

?>
<input type="hidden" name="id_laboratorium_rincian" value="<?php echo $id_laboratorium_rincian; ?>">
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <td class="text-center">
                            <b><i class="bi bi-check-circle"></i></b>
                        </td>
                        <td><b>Kode</b></td>
                        <td><b>Nama Spesimen</b></td>
                        <td><b>Metode</b></td>
                        <td><b>Body Site</b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_spesimen FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium'"));
                        if(empty($jml_data)){
                            echo '
                                <tr class="table-danger">
                                    <td class="text-center" colspan="5">
                                        <small class="text-danger">Pemeriksaan Belum Memiliki Referensi Spesimen</small>
                                    </td>
                                </tr>
                            ';
                        }else{
                            $query = mysqli_query($Conn, "SELECT * FROM laboratorium_spesimen WHERE id_laboratorium='$id_laboratorium'");
                            while ($data = mysqli_fetch_array($query)) {
                                $id_laboratorium_spesimen = $data['id_laboratorium_spesimen'];
                                $nama_spesimen            = $data['nama_spesimen'];
                                $nama_metode_sample       = $data['nama_metode_sample'];
                                $bodysite_nama            = $data['bodysite_nama'];
                                echo '
                                    <tr class="pilih-spesimen-row" data-radio-id="id_laboratorium_spesimen'.$id_laboratorium_spesimen.'" style="cursor:pointer;">
                                        <td class="text-center">
                                            <input type="radio" name="id_laboratorium_spesimen" id="id_laboratorium_spesimen'.$id_laboratorium_spesimen.'" value="'.$id_laboratorium_spesimen.'">
                                        </td>
                                        <td><small>LAB-SPC-'.$id_laboratorium_spesimen.'</small></td>
                                        <td><small>'.$nama_spesimen.'</small></td>
                                        <td><small>'.$nama_metode_sample.'</small></td>
                                        <td><small>'.$bodysite_nama.'</small></td>
                                    </tr>
                                ';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).off('click', '#FormPilihSpesimen .pilih-spesimen-row').on('click', '#FormPilihSpesimen .pilih-spesimen-row', function (e) {
        if ($(e.target).is('input[type="radio"]')) {
            return;
        }
        let radioId = $(this).data('radio-id');
        let $radio = $('#' + radioId);
        if ($radio.length) {
            $radio.prop('checked', true).trigger('change');
        }
    });
</script>
