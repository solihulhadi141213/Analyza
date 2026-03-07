<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Tangkap Data
    if(empty($_POST['tahun'])){
        $tahun = date('Y');
    }else{
        $tahun = $_POST['tahun'];
    }
    echo '
        <div class="row mb-3">
            <div class="col-12">
                <label for="tahun_data">
                    <small>Tahun</small>
                </label>
                <input type="text" readonly name="tahun" id="tahun_data" class="form-control" value="'.$tahun.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="format_data">
                    <small>Format Data</small>
                </label>
                <select name="format_data" id="format_data" class="form-control">
                    <option value="HTML">HTML</option>
                    <option value="Excel">Excel</option>
                    <option value="PDF">PDF</option>
                </select>
            </div>
        </div>
    ';
?>
