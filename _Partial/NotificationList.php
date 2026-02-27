<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    include "../_Config/Session.php";
    
    //Menghitung Jumlah Pinjaman Yang Menunggak
    $JumlahNotifikasi = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE status='Diminta'"));
    
    
    //Apabila Tidak ada notifgikasi
    if(empty($JumlahNotifikasi)){
        echo '<li class="dropdown-header">';
        echo '  Tidak Ada Permintaan Pemeriksaan';
        echo '</li>';
    }else{
        //Apabila Ada
        echo '<li class="dropdown-header">';
        echo '  Ada '.$JumlahNotifikasi.' Permintaan Pemeriksaan';
        echo '</li>';
        if(!empty($JumlahNotifikasi)){
            $query = mysqli_query($Conn, "SELECT*FROM laboratorium WHERE status='Diminta' LIMIT 5");
            while ($data = mysqli_fetch_array($query)) {
                $id_laboratorium  = $data['id_laboratorium'];
                $nama             = $data['nama'];
                $datetime_diminta = $data['datetime_diminta'];
                echo '
                    <li><hr class="dropdown-divider"></li>
                    <li class="notification-item">
                        <a href="javascript:void(0);" class="modal_terima_permintaan_pemeriksaan" data-id="'.$id_laboratorium.'">
                            <div>
                                <b>'.$nama.'</b>
                                <p>'.date('d/m/Y H:i', strtotime($datetime_diminta)).'</p>
                            </div>
                        </a>
                    </li>
                ';
            }
        }
        if($JumlahNotifikasi>5){
            echo '
                <li class="dropdown dropdown-footer">
                    <a href="javascript:void(0);" class="text-danger" class="modal_lihat_daftar_permintaan">
                        Lihat Selengkapnya <i class="bi bi-chevron-down"></i>
                    </a>
                </li>
            ';
        }
    }
?>