<!-- 
==========================================================================================
FILTER DATA 
==========================================================================================
-->
<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilter">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="periode">
                                <small>Periode Data</small>
                            </label>
                            <select name="periode" id="periode" class="form-control">
                                <option value="">Pilih Periode</option>
                                <option value="Tahunan">Tahunan</option>
                                <option value="Bulanan">Bulanan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="form_filter_lanjutan">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-check"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- 
==========================================================================================
EXPORT DATA 
==========================================================================================
-->
<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/LaporanPelayanan/ProsesExport.php" method="GET" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormExport">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="NotifikasiExport">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 
==========================================================================================
RINCIAN LAPORAN PELAYANAN
==========================================================================================
-->
<div class="modal fade" id="ModalRincianLaporanPelayanan" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <form action="_Page/LaporanPelayanan/ProsesExportRincian.php" method="GET" target="_blank">
                <div class="modal-header nav_background">
                    <h5 class="modal-title text-light"><i class="bi bi-table"></i> Rincian Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormRincianLaporanPelayanan">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer nav_background">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>