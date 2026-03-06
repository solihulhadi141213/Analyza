<?php
    include "_Page/Logout/ModalLogout.php";
    include "_Page/Dashboard/ModalDashboard.php";
    if(!empty($_GET['Page'])){
        $Page=$_GET['Page'];
        
        // Daftar halaman dan modal yang terkait
        $modals = [
            "MyProfile"                      => "_Page/MyProfile/ModalMyProfile.php",
            "AksesFitur"                     => "_Page/AksesFitur/ModalAksesFitur.php",
            "AksesEntitas"                   => "_Page/AksesEntitas/ModalAksesEntitas.php",
            "Akses"                          => "_Page/Akses/ModalAkses.php",
            "SettingEmail"                   => "_Page/SettingEmail/ModalSettingEmail.php",
            "SettingSimrs"                   => "_Page/SettingSimrs/ModalSettingSimrs.php",
            "SettingSatuSehat"               => "_Page/SettingSatuSehat/ModalSettingSatuSehat.php",
            "ApiKey"                         => "_Page/ApiKey/ModalApiKey.php",
            "GoogleCredential"               => "_Page/GoogleCredential/ModalGoogleCredential.php",
            "ReferensiPemeriksaan"           => "_Page/ReferensiPemeriksaan/ModalReferensiPemeriksaan.php",
            "ReferensiMetodePemeriksaan"     => "_Page/ReferensiMetodePemeriksaan/ModalReferensiMetodePemeriksaan.php",
            "ReferensiJenisSpesimen"         => "_Page/ReferensiJenisSpesimen/ModalReferensiJenisSpesimen.php",
            "ReferensiBodySite"              => "_Page/ReferensiBodySite/ModalReferensiBodySite.php",
            "ReferensiCaraPengambilanSample" => "_Page/ReferensiCaraPengambilanSample/ModalReferensiCaraPengambilanSample.php",
            "ReferensiKemasanSample"         => "_Page/ReferensiKemasanSample/ModalReferensiKemasanSample.php",
            "ReferensiSatuan"                => "_Page/ReferensiSatuan/ModalReferensiSatuan.php",
            "TandaTangan"                    => "_Page/TandaTangan/ModalTandaTangan.php",
            "Pemeriksaan"                    => "_Page/Pemeriksaan/ModalPemeriksaan.php",
            "LaporanPelayanan"               => "_Page/LaporanPelayanan/ModalLaporanPelayanan.php",
            "LaporanSpesimen"                => "_Page/LaporanSpesimen/ModalLaporanSpesimen.php",
            "LaporanDiagnosis"               => "_Page/LaporanDiagnosis/ModalLaporanDiagnosis.php",
            "LaporanSatuSehat"               => "_Page/LaporanSatuSehat/ModalLaporanSatuSehat.php",
            "Aktivitas"                      => "_Page/Aktivitas/ModalAktivitas.php",
            "Dokumentasi"                    => "_Page/Dokumentasi/ModalDokumentasi.php",
            "Help"                           => "_Page/Help/ModalHelp.php"
        ];

        // Cek apakah halaman memiliki modal terkait dan sertakan file modalnya
        if (!empty($_GET['Page']) && isset($modals[$_GET['Page']])) {
            include $modals[$_GET['Page']];
        }
    }
?>