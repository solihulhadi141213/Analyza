//Fungsi Menampilkan Data
function ShowData() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    var $tabel       = $('#TabelLaporanSpesimen');

    // Tambahkan efek visual loading (opacity menurun)
    $tabel.css({
        'opacity': '0.5',
        'pointer-events': 'none',
        'transition': 'opacity 0.3s ease'
    });

    $.ajax({
        type   : 'POST',
        url    : '_Page/LaporanSpesimen/TabelLaporanSpesimen.php',
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

// Fungsi Menampilkan Data Rincian
function ShowRincian(periode = '', keyword = '', code = '') {

    const $tabel = $('#TabelRincianSpesimen');

    // Jika ada request sebelumnya, hentikan
    if ($tabel.data('request')) {
        $tabel.data('request').abort();
    }

    // Tampilkan loading spinner
    $tabel.html(`
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <small> Loading data...</small>
            </td>
        </tr>
    `);

    // Efek visual nonaktif
    $tabel.css({
        opacity: 0.5,
        pointerEvents: 'none'
    });

    // Simpan object request supaya bisa di-abort jika perlu
    const request = $.ajax({
        type: 'POST',
        url: '_Page/LaporanSpesimen/TabelRincianSpesimen.php',
        data: {
            periode: periode,
            keyword: keyword,
            code: code
        },
        dataType: 'html'
    });

    $tabel.data('request', request);

    request.done(function (response) {

        $tabel.html(response);

        // Re-init Tooltip Bootstrap 5
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

    }).fail(function () {

        $tabel.html(`
            <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">
                        Gagal memuat data, silakan coba lagi!
                    </small>
                </td>
            </tr>
        `);

    }).always(function () {

        // Kembalikan efek normal
        $tabel.css({
            opacity: 1,
            pointerEvents: 'auto'
        });

        // Hapus request dari data
        $tabel.removeData('request');
    });
}

// Tampilkan/sembunyikan form filter berdasarkan periode
function ToggleFilterPeriode() {
    var periode = $('#periode').val();
    var $rowBulan = $('#bulan').closest('.row');

    if (periode === 'Tahun') {
        $rowBulan.hide();
        $('#bulan').val('');
    } else {
        $rowBulan.show();
    }
}

//Menampilkan Data Pertama Kali
$(document).ready(function() {
    ShowData();
    ToggleFilterPeriode();

    // Menampilkan Modal Filter
    $(document).on('click', '.modal_filter', function () {
        $('#ModalFilter').modal('show');
        ToggleFilterPeriode();
    });

    // Ubah tampilan form saat periode berubah
    $(document).on('change', '#periode', function() {
        ToggleFilterPeriode();
    });
    //Ketika Data Di Filter Kembalikan Ke Halaman Awal
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        $('#ModalFilter').modal('hide');
        ShowData();
    });

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowData(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowData(0);
    });

    // Menampilkan Modal Export
    $(document).on('click', '.modal_export', function () {

        // Tampilkan Modal
        $('#ModalExport').modal('show');

        // Tangkap Data dari ProsesFilter
        var ProsesFilter = $('#ProsesFilter').serialize();

        // Loading Form
        $('#FormExport').html('Loading...');
        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/LaporanSpesimen/FormExport.php',
            data        : ProsesFilter,
            success     : function(data){
                $('#FormExport').html(data);
            }
        });
    });

    // Menampilkan Modal Rincian Spesimen
    $(document).on('click', '.modal_rincian_spesimen', function () {

        // Tampilkan Modal
        $('#ModalRincianSpesimen').modal('show');

        // Tangkap Data dari ProsesFilter
        var periode       = $(this).data('periode');
        var keyword       = $(this).data('keyword');
        var code_spesimen = $(this).data('code_spesimen');

        // Kosongkan Judul
        $('#FormRincianSpesimen').html('LOADING...');

        ShowRincian(periode,keyword,code_spesimen);
    });


});
