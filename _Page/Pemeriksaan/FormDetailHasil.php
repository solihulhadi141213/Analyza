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
                <small>ID Rincian Pemeriksaan Laboratorium Tidak Boleh Kosong!</small>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_speciment' dan sanitasi
    $id_laboratorium_rincian = validateAndSanitizeInput($_POST['id_laboratorium_rincian']);

    // Membuka Pengaturan Koneksi Satusehat
    $stmt = $Conn->prepare("SELECT * FROM laboratorium_rincian WHERE id_laboratorium_rincian = ?");
    $stmt->bind_param("i", $id_laboratorium_rincian);
    $stmt->execute();
    $result = $stmt->get_result();
    $Data = $result->fetch_assoc();
    $stmt->close();

    if (!$Data) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data Rincian Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }
    if(empty($Data['id_laboratorium_rincian'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>Data Rincian Tidak Ditemukan!</small>
            </div>
        ';
        exit;
    }
    $id_referensi_pemeriksaan   = $Data['id_referensi_pemeriksaan'];
    $id_referensi_category      = $Data['id_referensi_category'];
    $id_referensi_range         = $Data['id_referensi_range'];
    $category_pemeriksaan       = $Data['category_pemeriksaan'];
    $nama_pemeriksaan           = trim((string)($Data['nama_pemeriksaan'] ?? ''));
    $metode_pemeriksaan         = trim((string)($Data['metode_pemeriksaan'] ?? ''));
    $metode_pemeriksaan_display = trim((string)($Data['metode_pemeriksaan_display'] ?? ''));
    $metode_pemeriksaan_code    = trim((string)($Data['metode_pemeriksaan_code'] ?? ''));
    $metode_pemeriksaan_system  = trim((string)($Data['metode_pemeriksaan_system'] ?? ''));
    $interpertasi               = trim((string)($Data['interpertasi'] ?? ''));
    $conclusion                 = trim((string)($Data['conclusion'] ?? ''));
    $keterangan                 = trim((string)($Data['keterangan'] ?? ''));
    
    // Nullable
    if($metode_pemeriksaan === ''){
        $metode_pemeriksaan = '-';
    }
    if($metode_pemeriksaan_display === ''){
        $metode_pemeriksaan_display = '-';
    }
    if($metode_pemeriksaan_code === ''){
        $metode_pemeriksaan_code = '-';
    }
    if($metode_pemeriksaan_system === ''){
        $metode_pemeriksaan_system = '-';
    }
    if($interpertasi === ''){
        $interpertasi = '-';
    }
    if($conclusion === ''){
        $conclusion = '-';
    }
    if($keterangan === ''){
        $keterangan = '-';
    }

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

    if (empty($DataReferensiPemeriksaan)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>Data pemeriksaan laboratorium tidak ditemukan!</small>
            </div>
        ';
        exit;
    }

    echo '
        <div class="row mb-2">
            <div class="col-12"><small><b>A. Referensi Pemeriksaan</b></small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kategori Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$category_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Pemeriksaan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$nama_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Display</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$DataReferensiPemeriksaan['code_pemeriksaan'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$DataReferensiPemeriksaan['display_pemeriksaan'].'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>System</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$DataReferensiPemeriksaan['system_pemeriksaan'].'</small>
            </div>
        </div>
    ';

    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12 mt-3"><small><b>B. Metode Pemeriksaan</b></small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Nama Metode</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$metode_pemeriksaan.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Display</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$metode_pemeriksaan_display.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>Code</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$metode_pemeriksaan_code.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small><i>System</i></small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$metode_pemeriksaan_system.'</small>
            </div>
        </div>
    ';
    echo '
        <div class="row mb-2 mt-3">
            <div class="col-12 mt-3"><small><b>C. Interpertasi Hasil</b></small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Interpertasi</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$interpertasi.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kesimpulan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$conclusion.'</small>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Keterangan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7">
                <small class="text text-grayish">'.$keterangan.'</small>
            </div>
        </div>
    ';

    if($DataReferensiPemeriksaan['result_interpertation_type']=="Range"){
        if(!empty($id_referensi_range)){
            
            // Buka Referensi Range
            $QryRange = $Conn->prepare("SELECT * FROM referensi_range WHERE id_referensi_range = ?");
            $QryRange->bind_param("i", $id_referensi_range);
            if (!$QryRange->execute()) {
                $ErrorReferensiRange=$Conn->error;
                echo '
                    <div class="alert alert-danger text-center">
                        <small>Terjadi kesalahan pada saat membuka tabel Referensi Pemeriksaan!<br>Keterangan : '.$ErrorReferensiRange.'</small>
                    </div>
                ';
                exit;
            }
            $ResultReferensiRange = $QryRange->get_result();
            $DataReferensiRange = $ResultReferensiRange->fetch_assoc();
            $QryRange->close();

            if (empty($DataReferensiRange)) {
                echo '
                    <div class="alert alert-danger text-center">
                        <small>Data Referensi Range Tidak Ditemukan!</small>
                    </div>
                ';
                exit;
            }
            if($DataReferensiRange['operator']=="More"){
                $value_range = '>= '.$DataReferensiRange['nilai_min'].' '.$DataReferensiPemeriksaan['unit_display'].'';
            }
            if($DataReferensiRange['operator']=="Between"){
                $value_range = ''.$DataReferensiRange['nilai_min'].' - '.$DataReferensiRange['nilai_max'].' '.$DataReferensiPemeriksaan['unit_display'].'';
            }
            echo '
                <div class="row mb-2 mt-3">
                    <div class="col-12 mt-3"><small><b><i>D. Code Concept (Range)</i></b></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Interpertasi Label</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$DataReferensiRange['label'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>Value Range</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$value_range.'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>Display</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$DataReferensiRange['fhir_display'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>Code</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$DataReferensiRange['fhir_code'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>System</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$DataReferensiRange['fhir_system'].'</small>
                    </div>
                </div>
            ';
        }
    }

    if($DataReferensiPemeriksaan['result_interpertation_type']=="Category"){
        if(!empty($id_referensi_category)){
            
            // Buka Referensi category
            $QryCategory = $Conn->prepare("SELECT * FROM referensi_category WHERE id_referensi_category = ?");
            $QryCategory->bind_param("i", $id_referensi_category);
            if (!$QryCategory->execute()) {
                $ErrorCategory=$Conn->error;
                echo '
                    <div class="alert alert-danger text-center">
                        <small>Terjadi kesalahan pada saat membuka tabel Referensi Pemeriksaan!<br>Keterangan : '.$ErrorCategory.'</small>
                    </div>
                ';
                exit;
            }
            $ResultCategory = $QryCategory->get_result();
            $Datacategory = $ResultCategory->fetch_assoc();
            $QryCategory->close();

            if (empty($Datacategory)) {
                echo '
                    <div class="alert alert-danger text-center">
                        <small>Data Referensi Range Tidak Ditemukan!</small>
                    </div>
                ';
                exit;
            }
            echo '
                <div class="row mb-2 mt-3">
                    <div class="col-12 mt-3"><small><b><i>D. Code Concept (Category)</i></b></small></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small>Interpertasi Label</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$Datacategory['label'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>Value</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$Datacategory['nilai_hasil'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>Display</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$Datacategory['fhir_display'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>Code</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$Datacategory['fhir_code'].'</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><small><i>System</i></small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$Datacategory['fhir_system'].'</small>
                    </div>
                </div>
            ';
        }
    }
?>
