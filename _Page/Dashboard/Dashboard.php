<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-grid"></i> Dashboard
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12" id="notifikasi_proses">
            <!-- Kejadian Kegagalan Menampilkan Data Akan Ditampilkan Disini -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="card_jam_menarik">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-12 col-md-3 mb-3 mb-md-0 text-center text-md-start" id="image_menarik">
                            <img src="assets/img/<?php echo $app_logo; ?>" width="100px" class="img-fluid" alt="<?php echo $company_name; ?>">
                        </div>

                        <div class="col-12 col-md-9 text-center text-md-end">
                            <div id="title_menarik"><?php echo $company_name; ?></div>
                            <div id="alamat_company"><?php echo $company_address; ?></div>
                            <!-- <div id="tanggal_menarik">Hari, 01 Januari 1900</div>
                            <div id="jam_menarik">00:00 WIB</div> -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="filter">
                                        <a class="icon" href="javascript:void(0);" id="ReloadChart">
                                            <i class="bi bi-repeat"></i>
                                        </a>
                                        <a class="icon" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <li class="dropdown-header text-start"><h6>Periode</h6></li>
                                            <li><a href="javascript:void(0);" class="dropdown-item" id="ChartBulanini">Bulan Ini</a></li>
                                            <li><a href="javascript:void(0);" class="dropdown-item" id="ChartTahunIni">Tahun Ini</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" id="chart_pelayanan">
                                    <!-- Menampilkan Grafik Disini -->
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-12">
            <div class="card info-card sales-card">
                <div class="filter">
                    <a class="icon" href="javascript:void(0);" id="reload_permintaan_pemeriksaan">
                        <i class="bi bi-repeat"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-send"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_diminta">00.000</b><br>
                            <small>Permintaan</small><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-ban"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_ditolak">00.000</b><br>
                            <small>Ditolak/Dibatalkan</small><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card yellow-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_diterima">00.000</b><br>
                            <small>Diterima</small><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-check"></i>
                        </div>
                        <div class="ps-3">
                            <b id="count_selesai">00.00</b><br>
                            <small>Selesai</small><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <b class="card-title">
                        <i class="bi bi-list"></i> Indikator Layanan</small>
                    </b>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead>
                                <tr class="table-dark">
                                    <td valign="middle" class="text-center" colspan="2" rowspan="2"><b>NO</b></td>
                                    <td valign="middle" class="text-center" rowspan="2"><b>INDIKATOR</b></td>
                                    <td valign="middle" class="text-center" colspan="2"><b>TUJUAN</b></td>
                                    <td valign="middle" class="text-center" colspan="2"><b>PEMBAYARAN</b></td>
                                    <td valign="middle" class="text-center" colspan="3"><b>PRIORITAS</b></td>
                                </tr>
                                <tr class="table-dark">
                                    <td valign="middle" class="text-center"><b>RAJAL</b></td>
                                    <td valign="middle" class="text-center"><b>RANAP</b></td>
                                    <td valign="middle" class="text-center"><b>UMUM</b></td>
                                    <td valign="middle" class="text-center"><b>ASURANSI</b></td>
                                    <td valign="middle" class="text-center"><b>BIASA</b></td>
                                    <td valign="middle" class="text-center"><b>SEGERA</b></td>
                                    <td valign="middle" class="text-center"><b>GAWAT</b></td>
                                </tr>
                            </thead>
                            <tbody id="TabelIndikatorLayanan">
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
