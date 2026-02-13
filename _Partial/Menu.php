<?php
    if(empty($_GET['Page'])){
        $PageMenu="";
    }else{
        $PageMenu=$_GET['Page'];
    }
    if(empty($_GET['Sub'])){
        $SubMenu="";
    }else{
        $SubMenu=$_GET['Sub'];
    }
?>
<aside id="sidebar" class="sidebar menu_background">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu==""){echo "";}else{echo "collapsed";} ?>" href="index.php">
                <i class="bi bi-grid"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-heading border-1 border-top">
            <div class="mt-3">Fitur Dasar</div>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="SettingGeneral"||$PageMenu=="SettingEmail"||$PageMenu=="ApiKey"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-gear"></i>
                    <span>Pengaturan</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components-nav" class="nav-content collapse <?php if($PageMenu=="SettingGeneral"||$PageMenu=="SettingEmail"||$PageMenu=="ApiKey"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SettingGeneral" class="<?php if($PageMenu=="SettingGeneral"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Pengaturan Umum</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingEmail" class="<?php if($PageMenu=="SettingEmail"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Email Gateway</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=ApiKey" class="<?php if($PageMenu=="ApiKey"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>API Key</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="AksesFitur"||$PageMenu=="AksesEntitas"||$PageMenu=="Akses"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components2-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-key"></i>
                    <span>Aksesibilitas</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components2-nav" class="nav-content collapse <?php if($PageMenu=="AksesFitur"||$PageMenu=="AksesEntitas"||$PageMenu=="Akses"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=AksesFitur" class="<?php if($PageMenu=="AksesFitur"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Fitur Aplikasi</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=AksesEntitas" class="<?php if($PageMenu=="AksesEntitas"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Group/Entitas</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Akses" class="<?php if($PageMenu=="Akses"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Akses Pengguna</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="SettingSimrs"||$PageMenu=="SettingSatuSehat"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components3-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bx bx-plug"></i> 
                <span>Koneksi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components3-nav" class="nav-content collapse <?php if($PageMenu=="SettingSimrs"||$PageMenu=="SettingSatuSehat"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SettingSimrs" class="<?php if($PageMenu=="SettingSimrs"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Konkesi SIMRS</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=SettingSatuSehat" class="<?php if($PageMenu=="SettingSatuSehat"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Koneksi Satu Sehat</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-heading border-1 border-top">
            <div class="mt-3">Master</div>
        </li>

        <?php
            // Routing Referensi
            if(
                $PageMenu=="ReferensiPemeriksaan" ||
                $PageMenu=="ReferensiMetodePemeriksaan" ||
                $PageMenu=="ReferensiInterpertasi" ||
                $PageMenu=="ReferensiJenisSpesimen" ||
                $PageMenu=="ReferensiBodySite" ||
                $PageMenu=="ReferensiCaraPengambilanSample" ||
                $PageMenu=="ReferensiKemasanSample" ||
                $PageMenu=="ReferensiSatuan" ||
                $PageMenu=="TandaTangan" 
            ){
                $collapsed_referensi="";
            }else{
                $collapsed_referensi="collapsed";
            }

            // Routing Referensi content
            if(
                $PageMenu=="ReferensiPemeriksaan" ||
                $PageMenu=="ReferensiMetodePemeriksaan" ||
                $PageMenu=="ReferensiInterpertasi" ||
                $PageMenu=="ReferensiJenisSpesimen" ||
                $PageMenu=="ReferensiBodySite" ||
                $PageMenu=="ReferensiCaraPengambilanSample" ||
                $PageMenu=="ReferensiKemasanSample" ||
                $PageMenu=="ReferensiSatuan" ||
                $PageMenu=="TandaTangan"
            ){
                $collapsed_content_referensi="show";
            }else{
                $collapsed_content_referensi="";
            }
        ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $collapsed_referensi; ?>" data-bs-target="#components4-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-table"></i> <span>Referensi</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components4-nav" class="nav-content collapse <?php echo $collapsed_content_referensi; ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=ReferensiPemeriksaan" class="<?php if($PageMenu=="ReferensiPemeriksaan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Jenis Pemeriksaan</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=ReferensiMetodePemeriksaan" class="<?php if($PageMenu=="ReferensiMetodePemeriksaan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Metode Pemeriksaan</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=ReferensiJenisSpesimen" class="<?php if($PageMenu=="ReferensiJenisSpesimen"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Jenis Spesimen</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=ReferensiBodySite" class="<?php if($PageMenu=="ReferensiBodySite"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Body Site</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=ReferensiCaraPengambilanSample" class="<?php if($PageMenu=="ReferensiCaraPengambilanSample"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Pengambilan Sample</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=ReferensiKemasanSample" class="<?php if($PageMenu=="ReferensiKemasanSample"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Kemasan (Container)</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=ReferensiSatuan" class="<?php if($PageMenu=="ReferensiSatuan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Satuan Ukur</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=TandaTangan" class="<?php if($PageMenu=="TandaTangan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Tanda Tangan</span>
                    </a>
                </li> 
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Pemeriksaan"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Pemeriksaan">
                <i class="bi bi-file-medical"></i> <span>Pemeriksaan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Spesimen"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Spesimen">
                <i class="ri ri-test-tube-line"></i> <span>Spesimen</span>
            </a>
        </li>
        <li class="nav-heading border-1 border-top">
            <div class="mt-3">Fitur Lainnya</div>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Aktivitas"){echo "collapsed";} ?>" href="index.php?Page=Aktivitas">
                <i class="bi bi-circle"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu!=="Help"){echo "collapsed";} ?>" href="index.php?Page=Help&Sub=HelpData">
                <i class="bi bi-question"></i>
                <span>Dokumentasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalLogout">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</aside> 