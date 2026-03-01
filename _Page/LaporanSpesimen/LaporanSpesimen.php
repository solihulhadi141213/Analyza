<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'LdfrkklBNVlbzMhihjuIUbFxMO7lkFaq6ZfG');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-filetype-doc"></i> Laporan Spesimen</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Spesimen</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard" id="data_table">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman laporan jumlah spesimen dari pelayanan pemeriksaan yang telah dilakukan. 
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
                            <div class="col-8">
                                <b class="card-title"># Jenis Spesimen</b>
                            </div>
                            <div class="col-4 text-end">
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
                            <div class="col-12 text-center" id="title_report">
                                <!-- Judul Laporan Ditampilkan Disini -->
                            </div>
                        </div>
                        <div class="table table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <td valign="middle" class="text-center"><b>No</b></td>
                                        <td valign="middle" class="text-center"><b>Nama Spesimen</b></td>
                                        <td valign="middle" class="text-center"><b><i>Display</i></b></td>
                                        <td valign="middle" class="text-center"><b><i>Code</i></b></td>
                                        <td valign="middle" class="text-center"><b><i>System</i></b></td>
                                        <td valign="middle" class="text-center"><b>Jumlah</b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelLaporanSpesimen">
                                    <tr>
                                        <td class="text-center" colspan="6">
                                            <small>Tidak ada data yang ditampilkan</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <small id="page_info">
                                    0 / 0
                                </small>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="prev_button">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info btn-floating" id="next_button">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
