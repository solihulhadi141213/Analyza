<?php
    if(empty($_GET['Page'])){
        include "_Page/Dashboard/Dashboard.php";
    }else{
        $Page=$_GET['Page'];
        //Index Halaman
        $page_arry=[
            "MyProfile"                      => "_Page/MyProfile/MyProfile.php",
            "AksesFitur"                     => "_Page/AksesFitur/AksesFitur.php",
            "AksesEntitas"                   => "_Page/AksesEntitas/AksesEntitas.php",
            "Akses"                          => "_Page/Akses/Akses.php",
            "SettingGeneral"                 => "_Page/SettingGeneral/SettingGeneral.php",
            "SettingEmail"                   => "_Page/SettingEmail/SettingEmail.php",
            "SettingSimrs"                   => "_Page/SettingSimrs/SettingSimrs.php",
            "SettingSatuSehat"               => "_Page/SettingSatuSehat/SettingSatuSehat.php",
            "ApiKey"                         => "_Page/ApiKey/ApiKey.php",
            "ReferensiPemeriksaan"           => "_Page/ReferensiPemeriksaan/ReferensiPemeriksaan.php",
            "ReferensiMetodePemeriksaan"     => "_Page/ReferensiMetodePemeriksaan/ReferensiMetodePemeriksaan.php",
            "ReferensiJenisSpesimen"         => "_Page/ReferensiJenisSpesimen/ReferensiJenisSpesimen.php",
            "ReferensiCaraPengambilanSample" => "_Page/ReferensiCaraPengambilanSample/ReferensiCaraPengambilanSample.php",
            "ReferensiKemasanSample"         => "_Page/ReferensiKemasanSample/ReferensiKemasanSample.php",
            "ReferensiSatuan"                => "_Page/ReferensiSatuan/ReferensiSatuan.php",
            "Route"                          => "_Page/Route/Route.php",
            "Question"                       => "_Page/Question/Question.php",
            "Medication"                     => "_Page/Medication/Medication.php",
            "MedicationRequest"              => "_Page/MedicationRequest/MedicationRequest.php",
            "Aktivitas"                      => "_Page/Aktivitas/Aktivitas.php",
            "Help"                           => "_Page/Help/Help.php",
        ];

        //Tangkap 'Page'
        $Page = !empty($_GET['Page']) ? $_GET['Page'] : "";

        //Kondisi Pada masing-masing Page
        if (array_key_exists($Page, $page_arry)) { 
            include $page_arry[$Page]; 
        } else { 
            include "_Page/Error/PageNotFound.php";
        }
    }
?>