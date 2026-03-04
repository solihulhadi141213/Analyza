<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'DoS05OwPS4Fz7QmOuPWJjEWlbsvKQlsw9Ygd');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-filetype-doc"></i> Laporan Diagnosis</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Diagnosis</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard" id="data_table">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman laporan pelayanan berdasarkan Diagnosis. 
                        Diagnosis yang digunakan adalah diagnosis yang berasal dari <i>Diagnostic Report</i>.
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
                                    <small>Filter Laporan Belum Diatur. Secara default akan menampilkan data secara keseluruhan.</small>
                                </div>
                            </div>
                        </div>
                        <div class="table table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <td valign="middle" class="text-center"><b>No</b></td>
                                        <td valign="middle" class="text-center"><b><i>Code</i></b></td>
                                        <td valign="middle" class="text-center"><b><i>Display</i></b></td>
                                        <td valign="middle" class="text-center"><b><i>System</i></b></td>
                                        <td valign="middle" class="text-center"><b>Jumlah</b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelLaporanDiagnosis">
                                    <tr>
                                        <td class="text-center" colspan="5">
                                            <small>Tidak ada data yang ditampilkan</small>
                                        </td>
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