<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Helper
    function getInisial($nama){
        if ($nama === null) {
            return '-';
        }

        $nama = trim((string)$nama);

        if ($nama === '') {
            return '-';
        }

        $kata = preg_split('/\s+/', $nama);
        $inisial = '';

        foreach ($kata as $item) {
            if ($item !== '') {
                $inisial .= strtoupper(mb_substr($item, 0, 1));
            }
        }

        return $inisial;
    }

    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="13" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
    }else{
        //Keyword_by
        if(!empty($_POST['keyword_by'])){
            $keyword_by=$_POST['keyword_by'];
        }else{
            $keyword_by="";
        }
        //keyword
        if(!empty($_POST['keyword'])){
            $keyword=$_POST['keyword'];
        }else{
            $keyword="";
        }
        //batas
        if(!empty($_POST['batas'])){
            $batas=$_POST['batas'];
        }else{
            $batas="10";
        }
        //ShortBy
        if(!empty($_POST['ShortBy'])){
            $ShortBy=$_POST['ShortBy'];
        }else{
            $ShortBy="DESC";
        }
        //OrderBy
        if(!empty($_POST['OrderBy'])){
            $OrderBy=$_POST['OrderBy'];
        }else{
            $OrderBy="datetime_diminta";
        }
        //Atur Page
        if(!empty($_POST['page'])){
            $page=$_POST['page'];
            $posisi = ( $page - 1 ) * $batas;
        }else{
            $page="1";
            $posisi = 0;
        }
        if(empty($keyword_by)){
            if(empty($keyword)){
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE id_pasien like '%$keyword%' OR nama like '%$keyword%'"));
            }
        }else{
            if(empty($keyword)){
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium"));
            }else{
                $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_laboratorium FROM laboratorium WHERE $keyword_by like '%$keyword%'"));
            }
        }
        
        //Mengatur Halaman
        $JmlHalaman = ceil($jml_data/$batas); 
        if(empty($jml_data)){
            echo '
                <tr>
                    <td colspan="13" class="text-center">
                        <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                    </td>
                </tr>
            ';
        }else{
            $no = 1+$posisi;
            //KONDISI PENGATURAN MASING FILTER
             if(empty($keyword_by)){
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT id_laboratorium, id_pasien, nama, gender, tanggal_lahir, datetime_diminta, tujuan, pembayaran, priority, status, keterangan, nama_petugas FROM laboratorium ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT id_laboratorium, id_pasien, nama, gender, tanggal_lahir, datetime_diminta, tujuan, pembayaran, priority, status, keterangan, nama_petugas FROM laboratorium WHERE id_pasien like '%$keyword%' OR nama like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }else{
                if(empty($keyword)){
                    $query = mysqli_query($Conn, "SELECT id_laboratorium, id_pasien, nama, gender, tanggal_lahir, datetime_diminta, tujuan, pembayaran, priority, status, keterangan, nama_petugas FROM laboratorium ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }else{
                    $query = mysqli_query($Conn, "SELECT id_laboratorium, id_pasien, nama, gender, tanggal_lahir, datetime_diminta, tujuan, pembayaran, priority, status, keterangan, nama_petugas FROM laboratorium WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
                }
            }
            while ($data = mysqli_fetch_array($query)) {
                $id_laboratorium  = $data['id_laboratorium'];
                $id_pasien        = $data['id_pasien'];
                $nama             = $data['nama'];
                $gender           = $data['gender'];
                $tanggal_lahir    = $data['tanggal_lahir'];
                $datetime_diminta = $data['datetime_diminta'];
                $tujuan           = $data['tujuan'];
                $pembayaran       = $data['pembayaran'];
                $priority         = $data['priority'];
                $status           = $data['status'];
                $keterangan       = $data['keterangan'];
                $nama_petugas     = $data['nama_petugas'];

                // Ubah Inisial
                $inisial_petugas = getInisial($nama_petugas);

                // Routing Gender
                if($gender=="Laki-laki"){
                    $label_gender = "L";
                }else{
                    $label_gender = "P";
                }

                // Routing Tujuan
                if($tujuan=="Rajal"){
                    $label_tujuan = '<a href="javascript:void(0);" class="text-warning" title="Rawat Jalan">RJL</a>';
                }else{
                    $label_tujuan = '<a href="javascript:void(0);" class="text-info" title="Rawat Inap">INP</a>';
                }

                // Usia pada saat permintaan dibuat (tanggal_lahir -> datetime_diminta)
                // Aturan:
                // - < 1 bulan  => satuan Hari
                // - < 1 tahun  => satuan Bulan
                // - >= 1 tahun => satuan Tahun
                // - Dibulatkan ke atas bila sisa > setengah satuan
                if (empty($tanggal_lahir) || empty($datetime_diminta)) {
                    $usia = "-";
                } else {
                    try {
                        $tgl_lahir = new DateTime($tanggal_lahir);
                        $tgl_diminta = new DateTime($datetime_diminta);

                        if ($tgl_diminta < $tgl_lahir) {
                            $usia = "-";
                        } else {
                            $selisih = $tgl_lahir->diff($tgl_diminta);

                            if ($selisih->y >= 1) {
                                $tahun = (int) $selisih->y;
                                $lebih_setengah_tahun = (
                                    $selisih->m > 6 ||
                                    ($selisih->m == 6 && ($selisih->d > 0 || $selisih->h > 0 || $selisih->i > 0 || $selisih->s > 0))
                                );
                                if ($lebih_setengah_tahun) {
                                    $tahun++;
                                }
                                $usia = $tahun . ' Th';
                            } elseif ($selisih->m >= 1) {
                                $bulan = (int) $selisih->m;
                                $acuan_bulan = clone $tgl_lahir;
                                $acuan_bulan->add(new DateInterval('P' . $bulan . 'M'));
                                $hari_dalam_bulan = (int) $acuan_bulan->format('t');
                                $sisa_hari = $selisih->d + ($selisih->h / 24) + ($selisih->i / 1440) + ($selisih->s / 86400);

                                if ($sisa_hari > ($hari_dalam_bulan / 2)) {
                                    $bulan++;
                                }

                                $usia = $bulan . ' Bl';
                            } else {
                                $hari = (int) $selisih->days;
                                $sisa_hari = ($selisih->h / 24) + ($selisih->i / 1440) + ($selisih->s / 86400);

                                if ($sisa_hari > 0.5) {
                                    $hari++;
                                }

                                $usia = $hari . ' Hr';
                            }
                        }
                    } catch (Exception $e) {
                        $usia = "-";
                    }
                }

                // priority
                if($priority=="routine"){
                    $label_priority = '<span class="badge badge-success">Biasa</span>';
                }else{
                    if($priority=="urgent"){
                        $label_priority = '<span class="badge badge-warning">Segera</span>';
                    }else{
                        $label_priority = '<span class="badge badge-danger">Darurat</span>';
                    }
                }

                // Status
                if($status=="Diminta"){
                    $label_status = '
                        <a href="javascript:void(0);" class="modal_terima_pemeriksaan" data-id="'.$id_laboratorium.'">
                            <span class="badge bg-danger">Diminta</span>
                        </a>
                    ';
                }else{
                    if($status=="Ditolak"||$status=="Dibatalkan"){
                        $label_status = '<span class="badge bg-secondary" title="Pemeriksaan Dibatalkan">Batal</span>';
                    }else{
                        if($status=="Diterima"){
                            $label_status = '<span class="badge bg-info" title="Pemeriksaan Diterima">Diterima</span>';
                        }else{
                            if($status=="Pengambilan Spesimen"){
                                $label_status = '<span class="badge bg-warning" title="Pengambilan Spesimen">PGS</span>';
                            }else{
                                if($status=="Pemeriksaan Spesimen"){
                                    $label_status = '<span class="badge bg-primary" title="Pemeriksaan Spesimen">PMS</span>';
                                }else{
                                    if($status=="Keluar Hasil"){
                                        $label_status = '<span class="badge bg-success text-dark" title="Keluar Hasil">KH</span>';
                                    }else{
                                        if($status=="Selesai"){
                                            $label_status = '<span class="badge bg-success" title="Selesai">SLS</span>';
                                        }else{
                                            $label_status = '<span class="badge bg-dark">None</span>';
                                        }
                                    }
                                    
                                }
                            }
                        }
                    }
                }
               
                // Tampilkan Data
                echo '
                    <tr>
                        <td class="text-center"><small>'.$no.'</small></td>
                        <td>
                            <small>'.date('d/m/y', strtotime($datetime_diminta)).'</small>
                        </td>
                        <td>
                            <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_laboratorium.'">
                                <small><i>'.$nama.'</i></small>
                            </a>
                        </td>
                        <td><small>'.$id_pasien.'</small></td>
                        <td><small>'.$label_gender.'</small></td>
                        <td><small>'.$usia.'</small></td>
                        <td><small>'.$label_tujuan.'</small></td>
                        <td><small><small>'.$pembayaran.'</small></small></td>
                        <td><small>'.$label_priority.'</small></td>
                        <td><small>'.$keterangan.'</small></td>
                        <td><small>'.$label_status.'</small></td>
                        <td>
                            <small class="text text-muted">
                                <small title="'.$nama_petugas.'">'.$inisial_petugas.'</small>
                            </small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                <li class="dropdown-header text-start">
                                    <h6>Option</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_laboratorium.'">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_laboratorium.'">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item modal_hapus" href="javascript:void(0)" data-id="'.$id_laboratorium.'">
                                        <i class="bi bi-x"></i> Hapus
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                ';
                $no++;
            }
        }
    }
?>
<script>
    //Creat Javascript Variabel
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#page_info').html(''+curent_page+' / '+page_count+'');
    
    //Set Pagging Button
    if(curent_page==1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count<=curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }
</script>
