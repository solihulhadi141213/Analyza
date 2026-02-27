<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    date_default_timezone_set("Asia/Jakarta");
    //Menghitung Jumlah Permintaan Pemeriksaan
    $JumlahNotifikasi= mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE status='Diminta'"));
    
    //Apabila ada notifgikasi
    if(!empty($JumlahNotifikasi)){
        echo '<i class="bi bi-bell"></i>';
        echo '<span class="badge bg-danger rounded-pill badge-number">'.$JumlahNotifikasi.'</span>';
    }else{
        //Apabila Tidak Ada
        echo '<i class="bi bi-bell"></i>';
    }
?>