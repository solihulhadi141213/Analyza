<?php
    // Daftar tahun dari datetime_spesimen
    $tahun_list = "";
    $query_tahun = mysqli_query($Conn, "
        SELECT DISTINCT YEAR(datetime_spesimen) AS tahun
        FROM laboratorium_spesimen
        WHERE datetime_spesimen IS NOT NULL
        ORDER BY tahun DESC
    ");

    if ($query_tahun) {
        while ($data_tahun = mysqli_fetch_assoc($query_tahun)) {
            $tahun = htmlspecialchars($data_tahun['tahun']);
            $tahun_list .= "<option value=\"$tahun\">$tahun</option>";
        }
    }

    // Daftar bulan 01-12
    $daftar_bulan = [
        "01" => "Januari",
        "02" => "Februari",
        "03" => "Maret",
        "04" => "April",
        "05" => "Mei",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "Agustus",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember"
    ];

    $bulan_list = "";
    foreach ($daftar_bulan as $value_bulan => $nama_bulan) {
        $bulan_list .= "<option value=\"$value_bulan\">$nama_bulan</option>";
    }
?>
<!-- 
==========================================================================================
FILTER DATA 
==========================================================================================
-->
<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilter">
                <input type="hidden" name="page" id="page" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="periode">
                                <small>Periode</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="periode" id="periode" class="form-control">
                                <option selected value="Tahun">Tahun</option>
                                <option value="Bulan">Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="tahun">
                                <small>Tahun</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="tahun" id="tahun" class="form-control">
                                <?php echo $tahun_list; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="bulan">
                                <small>Bulan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="bulan" id="bulan" class="form-control">
                                <?php echo $bulan_list; ?>
                            </select>
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

<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/LaporanSpesimen/ProsesExportLaporanSpesimen.php" method="POST" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Data Spesimen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="FormExport">
                            <!-- Menampilkan Form Export -->
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
<div class="modal fade" id="ModalRincianSpesimen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">

            <form action="_Page/LaporanSpesimen/ProsesExportRincianSpesimen.php" 
                  id="ProsesExportRincianSpesimen" 
                  method="POST" 
                  target="_blank">

                <div class="modal-header nav_background">
                    <h5 class="modal-title text-light">
                        <i class="bi bi-download"></i> Export Data Spesimen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Form Filter Export -->
                    <div class="row mb-3">
                        <div class="col-12 text-center" id="FormRincianSpesimen">
                            <!-- Form export akan dimuat via AJAX -->
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th><b>No</b></th>
                                            <th><b>Nama Pasien</b></th>
                                            <th><b>RM</b></th>
                                            <th><b>Tanggal/Jam</b></th>
                                            <th><b><i>Method</i></b></th>
                                            <th><b><i>Body Site</i></b></th>
                                            <th><b><i>Container</i></b></th>
                                            <th><b><i>Value</i></b></th>
                                        </tr>
                                    </thead>
                                    <tbody id="TabelRincianSpesimen">
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                                <small> Loading data...</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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