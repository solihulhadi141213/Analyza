<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingGeneral.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['id_laboratorium'])){
        echo '
            <div class="alert alert-danger">
                <small>ID Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    $id_laboratorium = validateAndSanitizeInput($_POST['id_laboratorium']);
?>
<input type="hidden" name="id_laboratorium" value="<?php echo $id_laboratorium; ?>">
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <td class="text-center"><b><small>No</small></b></td>
                        <td class="text-left" colspan="2"><b><small>Nama Pemeriksaan</small></b></td>
                        <td class="text-left"><b><small>Display</small></b></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query = mysqli_query($Conn, "SELECT id_referensi_pemeriksaan, nama_pemeriksaan, display_pemeriksaan, code_pemeriksaan, unit, category_pemeriksaan
                            FROM referensi_pemeriksaan
                            ORDER BY category_pemeriksaan ASC, nama_pemeriksaan ASC
                        ");

                        if(!$query || mysqli_num_rows($query) === 0){
                            echo '
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <small class="text-danger">Tidak Ada Data Yang Ditemukan!</small>
                                    </td>
                                </tr>
                            ';
                        }else{
                            $no = 1;
                            $last_category = null;

                            while ($data = mysqli_fetch_assoc($query)) {
                                $category_pemeriksaan      = $data['category_pemeriksaan'] ?: '-';
                                $id_referensi_pemeriksaan  = $data['id_referensi_pemeriksaan'];
                                $nama_pemeriksaan          = $data['nama_pemeriksaan'];
                                $display_pemeriksaan       = $data['display_pemeriksaan'];
                                $code_pemeriksaan          = $data['code_pemeriksaan'];
                                $unit                      = $data['unit'];

                                // Cek Apakah Data Sudah Ada
                                $cek_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium_rincian FROM laboratorium_rincian WHERE id_laboratorium='$id_laboratorium' AND id_referensi_pemeriksaan='$id_referensi_pemeriksaan'"));

                                // Render header hanya jika kategori berubah
                                if ($last_category !== $category_pemeriksaan) {
                                    echo '
                                        <tr>
                                            <td class="text-center"><small><b>'.$no.'</b></small></td>
                                            <td class="text-left" colspan="5"><small><b>'.htmlspecialchars($category_pemeriksaan).'</b></small></td>
                                        </tr>
                                    ';
                                    $last_category = $category_pemeriksaan;
                                    $no++;
                                }

                                // Apabila Data Sudah ada
                                if(empty($cek_data)){
                                    $form_checklist = '
                                        <input type="checkbox" name="id_referensi_pemeriksaan[]" id="id_referensi_pemeriksaan_'.$id_referensi_pemeriksaan.'" value="'.(int)$id_referensi_pemeriksaan.'">
                                    ';
                                }else{
                                    $form_checklist = '
                                        <i class="bi bi-check-circle"></i>
                                    ';
                                }

                                echo '
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">'.$form_checklist.'</td>
                                        <td class="text-left">
                                            <label for="id_referensi_pemeriksaan_'.$id_referensi_pemeriksaan.'">
                                                <small>'.htmlspecialchars($nama_pemeriksaan).'</small>
                                            </label>
                                        </td>
                                        <td class="text-left"><small><i>'.htmlspecialchars($display_pemeriksaan).'</i></small></td>
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