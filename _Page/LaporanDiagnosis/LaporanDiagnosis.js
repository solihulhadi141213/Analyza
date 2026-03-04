//Fungsi Menampilkan Data
function ShowData() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    var $tabel       = $('#TabelLaporanDiagnosis');

    // Tambahkan efek visual loading (opacity menurun)
    $tabel.css({
        'opacity': '0.5',
        'pointer-events': 'none',
        'transition': 'opacity 0.3s ease'
    });

    $.ajax({
        type   : 'POST',
        url    : '_Page/LaporanDiagnosis/TabelLaporanDiagnosis.php',
        data   : ProsesFilter,
        success: function(data) {
            // Ganti isi tabel tanpa mengganti elemen induk
            $tabel.html(data);

            // Kembalikan efek normal
            $tabel.css({
                'opacity': '1',
                'pointer-events': 'auto'
            });
            
            // 🔁 Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        error: function() {
            $tabel.html('<tr><td class="text-center" colspan="5"><small class="text-danger">Gagal Memuat, Silahkan Coba Lagi!</small></td></tr>');
            $tabel.css({
                'opacity': '1',
                'pointer-events': 'auto'
            });
        }
    });
}


//Menampilkan Data Pertama Kali
$(document).ready(function() {

    //Menampilkan Modal Filter Pertama Kali
    $('#ModalFilter').modal('show');

    // Menampilkan Modal Filter
    $(document).on('click', '.modal_filter', function () {
        $('#ModalFilter').modal('show');
    });

    //Ketika KeywordBy diubah
    $('#periode').change(function(){
        var periode =$('#periode').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/LaporanDiagnosis/FormLanjutan.php',
            data        : {periode: periode},
            success     : function(data){
                $('#form_filter_lanjutan').html(data);
            }
        });
    });

    //Ketika Data Di Filter Kembalikan Ke Halaman Awal
    $('#ProsesFilter').submit(function(){
        $('#ModalFilter').modal('hide');
        ShowData();
    });

    // Menampilkan Modal Filter
    $(document).on('click', '.modal_export', function () {

        // Tangkap Data Filter
        var ProsesFilter = $('#ProsesFilter').serialize();

        // Tampilkan Modal
        $('#ModalExport').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiExport').html("");

        // Loading Form
        $('#FormExport').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/LaporanDiagnosis/FormExport.php',
            data        : ProsesFilter,
            success     : function(data){
                $('#FormExport').html(data);
            }
        });
    });

    // Menampilkan 'ModalRincianLaporanDiagnosis'
    $(document).on('click', '.modal_rincian_diagnosis', function () {

        // Tangkap Data Filter
        var diagnosa = $(this).data('diagnosa');
        var code     = $(this).data('code');
        var periode  = $(this).data('periode');
        var bulan    = $(this).data('bulan');
        var tahun    = $(this).data('tahun');

        // Tampilkan Modal
        $('#ModalRincianLaporanDiagnosis').modal('show');

        // Loading Form
        $('#FormRincianLaporanDiagnosis').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/LaporanDiagnosis/FormRincianLaporanDiagnosis.php',
            data        : {diagnosa: diagnosa, code: code, periode: periode, bulan: bulan, tahun: tahun},
            success     : function(data){
                $('#FormRincianLaporanDiagnosis').html(data);
            }
        });
    });
    

});





