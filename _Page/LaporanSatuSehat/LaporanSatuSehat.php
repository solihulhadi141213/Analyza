<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'JhBZHNzNXK7TgVpuOrdSMAmuk7i0mNH2TGd8');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-filetype-doc"></i> Laporan SATUSEHAT</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan SATUSEHAT</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard" id="data_table">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman laporan kepatuhan pengiriman data ke SATUSEHAT. 
                        Halaman ini berfungsi untuk melakukan monitoring kepatuhan pengiriman data ke platform SATUSEHAT.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-md btn-secondary btn-floating modal_filter" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Filter Periode">
                                    <i class="bi bi-calendar"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-outline-primary btn-floating modal_export" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Export / Cetak">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <td rowspan="2" valign="middle" class="text-center"><b>No</b></td>
                                        <td rowspan="2" valign="middle" class="text-left"><b>Resource SATUSEHAT</b></td>
                                        <td colspan="12" valign="middle" class="text-center"><b>PERIODE TAHUN <span id="TitleLaporan">-</span></b></td>
                                        <td rowspan="2" valign="middle" class="text-center"><b>Jumlah</b></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><b>Jan</b></td>
                                        <td class="text-center"><b>Feb</b></td>
                                        <td class="text-center"><b>Mar</b></td>
                                        <td class="text-center"><b>Apr</b></td>
                                        <td class="text-center"><b>Mei</b></td>
                                        <td class="text-center"><b>Jun</b></td>
                                        <td class="text-center"><b>Jul</b></td>
                                        <td class="text-center"><b>Agu</b></td>
                                        <td class="text-center"><b>Sep</b></td>
                                        <td class="text-center"><b>Okt</b></td>
                                        <td class="text-center"><b>Nov</b></td>
                                        <td class="text-center"><b>Des</b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelLaporanSatuSehat">
                                    <tr>
                                        <td colspan="14" class="text-center"><small>NO DATA</small></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <small id="duration_data_process">Duration Process : - </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>