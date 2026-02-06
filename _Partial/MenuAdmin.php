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
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Satuan"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components4-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-table"></i> 
                <span>Referensi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components4-nav" class="nav-content collapse <?php if($PageMenu=="Satuan"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=Satuan" class="<?php if($PageMenu=="Satuan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Satuan</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($PageMenu=="Pelayanan"){echo "";}else{echo "collapsed";} ?>" href="index.php?Page=Pelayanan">
                <i class="bi bi-basket"></i> <span>Pelayanan</span>
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