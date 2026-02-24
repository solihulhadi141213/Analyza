<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'6e7QM7lQsJKd7QYI0tV65OjQ6ONatKkfG2kt');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="ri ri-test-tube-line"></i> Spesimen</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Spesimen</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard" id="data_table">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Berikut ini adalah halaman untuk mengelola dan menampilkan data spesimen yang berasal dari data pemeriksaan.
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
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><b>No</b></th>
                                        <th><b>Nama / Jenis</b></th>
                                        <th><b>IHS Spesimen</b></th>
                                        <th><b>Tanggal</b></th>
                                        <th><b>L/P</b></th>
                                        <th><b>Usia</b></th>
                                        <th><b>Tujuan</b></th>
                                        <th><b>Pembayaran</b></th>
                                        <th><b>Priority</b></th>
                                        <th><b>Status</b></th>
                                        <th><b>Opsi</b></th>
                                    </tr>
                                </thead>
                                <tbody id="TabelPemeriksaan">
                                    <tr>
                                        <td class="text-center" colspan="12">
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
                                <small id="page_info">Page : 0 / 0</small>
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