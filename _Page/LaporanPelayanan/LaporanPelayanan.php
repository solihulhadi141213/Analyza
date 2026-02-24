<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'1zAPZc48JXroreTQaXGkUmCRJFgsunqmhrZV');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-filetype-doc"></i> Laporan Pelayanan</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Pelayanan</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard" id="data_table">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman laporan pelayanan pemeriksaan laboratorium. 
                        Halaman ini berfungsi untuk menampilkan monitoring durasi pelayanan pemeriksaan.
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
                                <button type="button" class="btn btn-md btn-secondary btn-floating modal_filter" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Filter Pemeriksaan">
                                    <i class="bi bi-search"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-outline-primary btn-floating modal_export" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Export / Cetak">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 text-center" id="TitleLaporan">
                                <div class="alert alert-danger">
                                    <small>Filter Laporan Belum Diatur</small>
                                </div>
                            </div>
                        </div>
                        <div class="table table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <td valign="middle" rowspan="2" class="text-center"><b>No</b></td>
                                        <td valign="middle" rowspan="2" class="text-left"><b>Bulan / Tanggal</b></td>
                                        <td valign="middle" rowspan="2" class="text-center"><b>Pemeriksaan</b></td>
                                        <td valign="middle" colspan="2" class="text-center"><b>Tujuan</b></td>
                                        <td valign="middle" colspan="2" class="text-center"><b>Pembayaran</b></td>
                                        <td valign="middle" colspan="3" class="text-center"><b>Priority</b></td>
                                        <td valign="middle" colspan="3" class="text-center"><b>Status</b></td>
                                        <td valign="middle" rowspan="2" class="text-center"><b>Durasi</b></td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="text-center"><b>Rajal</b></td>
                                        <td valign="middle" class="text-center"><b>Ranap</b></td>
                                        <td valign="middle" class="text-center"><b>UMUM</b></td>
                                        <td valign="middle" class="text-center"><b>ASRN</b></td>
                                        <td valign="middle" class="text-center"><b>Biasa</b></td>
                                        <td valign="middle" class="text-center"><b>Segera</b></td>
                                        <td valign="middle" class="text-center"><b>Gawat</b></td>
                                        <td valign="middle" class="text-center"><b>Diminta</b></td>
                                        <td valign="middle" class="text-center"><b>Batal</b></td>
                                        <td valign="middle" class="text-center"><b>Selesai</b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelLaporanPelayanan">
                                    <tr>
                                        <td class="text-center" colspan="14">
                                            <small>Tidak ada data yang ditampilkan</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center" ><b>JUMLAH</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
                                        <td class="text-center"><b>-</b></td>
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