<?php
    include "../../_Config/Connection.php";
    if(empty($_POST['KeywordBy'])){
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }else{
        $keyword_by=$_POST['KeywordBy'];

        // 'datetime_diminta'
        if($keyword_by=="datetime_diminta"){
            echo '<input type="date" name="keyword" id="keyword" class="form-control">';
        }else{

            // gender
            if($keyword_by=="gender"){
                echo '
                    <select name="keyword" id="keyword" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                ';
            }else{
                
                // tujuan
                if($keyword_by=="tujuan"){
                    echo '
                        <select name="keyword" id="keyword" class="form-control">
                            <option value="">Pilih</option>
                            <option value="Rajal">Rajal</option>
                            <option value="Ranap">Ranap</option>
                        </select>
                    ';
                }else{
                    
                    // tujuan
                    if($keyword_by=="pembayaran"){
                       echo '<select name="keyword" id="keyword" class="form-control">';
                        echo '  <option value="">Pilih</option>';
                        $query = mysqli_query($Conn, "SELECT DISTINCT pembayaran FROM laboratorium ORDER BY pembayaran ASC");
                        while ($data = mysqli_fetch_array($query)) {
                            $pembayaran= $data['pembayaran'];
                            echo '<option value="'.$pembayaran.'">'.$pembayaran.'</option>';
                        }
                        echo '</select>';
                    }else{
                        
                        // priority
                        if($keyword_by=="priority"){
                            echo '
                                <select name="keyword" id="keyword" class="form-control">
                                    <option value="">Pilih</option>
                                    <option value="routine">Biasa</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="stat">Darurat</option>
                                </select>
                            ';
                        }else{

                            // status
                            if($keyword_by=="status"){
                                echo '
                                    <select name="keyword" id="keyword" class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="Diminta">Diminta</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Dibatalkan">Dibatalkan</option>
                                        <option value="Diterima">Diterima</option>
                                        <option value="Pengambilan Spesimen">Pengambilan Spesimen</option>
                                        <option value="Pemeriksaan Spesimen">Pemeriksaan Spesimen</option>
                                        <option value="Keluar Hasil">Keluar Hasil</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                ';
                            }else{
                                 echo '<input type="text" name="keyword" id="keyword" class="form-control">';
                            }
                        }
                    }
                }
            }
        }
    }
?>