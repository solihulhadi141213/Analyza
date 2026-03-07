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
                            <label for="tahun">
                                <small>Pilih Tahun</small>
                            </label>
                            <select name="tahun" id="tahun" class="form-control">
                                <option value="">Pilih</option>
                                <?php
                                    $queryTahun = $Conn->prepare("
                                        SELECT DISTINCT YEAR(datetime_diminta) AS tahun
                                        FROM laboratorium
                                        WHERE datetime_diminta IS NOT NULL
                                          AND datetime_diminta <> '0000-00-00 00:00:00'
                                        ORDER BY tahun DESC
                                    ");

                                    if($queryTahun){
                                        $queryTahun->execute();
                                        $resultTahun = $queryTahun->get_result();

                                        while($rowTahun = $resultTahun->fetch_assoc()){
                                            if(!empty($rowTahun['tahun'])){
                                                $tahun = $rowTahun['tahun'];
                                                echo '<option value="'.$tahun.'">'.$tahun.'</option>';
                                            }
                                        }

                                        $queryTahun->close();
                                    }
                                ?>
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
<!-- 
==========================================================================================
EXPORT DATA 
==========================================================================================
-->
<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/LaporanSatuSehat/ProsesExport.php" method="GET" target="_blank">
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