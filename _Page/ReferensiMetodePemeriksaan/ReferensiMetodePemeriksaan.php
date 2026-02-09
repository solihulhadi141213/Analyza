<?php
    //Cek Aksesibilitas ke halaman ini
    $IjinAksesSaya=IjinAksesSaya($Conn,$SessionIdAccess,'6W5aMQEkhaBfwBGXQOEQx7M04Iv9h8IXOEsT');
    if($IjinAksesSaya!=="Ada"){
        include "_Page/Error/NoAccess.php";
    }else{
?>
    <div class="pagetitle">
        <h1>
            <a href="">
                <i class="bi bi-box"></i> Metode Pemeriksaan</a>
            </a>
        </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Metode Pemeriksaan</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <small>
                        Halaman ini digunakan untuk mengelola referensi metode pemeriksaan. 
                        Informasi tersebut berguna pada saat mengisi kelengkapan informasi hasil pemeriksaan. 
                        Satu pemeriksaan bisa memiliki banyak metode. Metode berbeda dapat menghasilkan sensitivitas/akurasi berbeda
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
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilter">
                                    <i class="bi bi-search"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalTambah">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <td align="center"><b>No</b></td>
                                        <td><b>Metode Pemeriksaan</b></td>
                                        <td><b><i>Display</i></b></td>
                                        <td><b><i>Code</i></b></td>
                                        <td><b><i>System</i></b></td>
                                        <td align="center"><b>Opsi</b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelMetodePemeriksaan">
                                    <tr>
                                        <td class="text-center" colspan="6">
                                            <small>Loading...</small>
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