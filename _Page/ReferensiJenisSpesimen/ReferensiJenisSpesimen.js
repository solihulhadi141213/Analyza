//Fungsi Menampilkan Data
function ShowData() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    var $tabel       = $('#TabelSpesimen');

    // Tambahkan efek visual loading (opacity menurun)
    $tabel.css({
        'opacity': '0.5',
        'pointer-events': 'none',
        'transition': 'opacity 0.3s ease'
    });

    $.ajax({
        type   : 'POST',
        url    : '_Page/ReferensiJenisSpesimen/TabelSpesimen.php',
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

    //Menampilkan Data Pertama Kali
    ShowData();

    //Ketika KeywordBy diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiJenisSpesimen/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
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


    // ===============================================================================
    // TAMBAH SATUAN / UNIT
    // ===============================================================================
    $('#ModalTambah').on('show.bs.modal', function (e) {
        // Menampilkan Datalist Category Dengan AJAX
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiJenisSpesimen/list_system.php',
            success     : function(data){
                $('#list_system').html(data);
            }
        });
    });
    $('#ModalTambah').on('shown.bs.modal', function () {
        $('#nama_spesimen').trigger('focus');
    });

    //Proses Tambah
    $('#ProsesTambah').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesTambah = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiTambah').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/ReferensiJenisSpesimen/ProsesTambah.php',
            dataType : 'json',
            data     : ProsesTambah,

            success: function(response){

                var payload  = response.payload;
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiTambah').html('');

                    // Tutup modal jika ada
                    $('#ModalTambah').modal('hide');

                    // Reset Form
                    $("#ProsesFilter")[0].reset();
                    $("#ProsesTambah")[0].reset();

                    // Reload detail pemeriksaan
                    $("#ProsesFilter")[0].reset();
                    $('#page').val(1);
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiTambah').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiTambah').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });


    // ===============================================================================
    // EDIT
    // ===============================================================================
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'kfa_code' dan buat variabel
        var id_referensi_jenis_spesimen   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiJenisSpesimen/FormEdit.php',
            data        : {id_referensi_jenis_spesimen: id_referensi_jenis_spesimen},
            success     : function(data){
                $('#FormEdit').html(data);

                // 🔁 Re-inisialisasi tooltip setelah data dimuat
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Menampilkan Datalist list_system Dengan AJAX
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/ReferensiJenisSpesimen/list_system.php',
                    success     : function(data){
                        $('#list_system_edit').html(data);
                    }
                });
            }
        });
    });

    

    //Proses Edit
    $('#ProsesEdit').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesEdit = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiEdit').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/ReferensiJenisSpesimen/ProsesEdit.php',
            dataType : 'json',
            data     : ProsesEdit,

            success: function(response){

                var status  = response.status;
                var message = response.message;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEdit').html('');

                    // Tutup modal jika ada
                    $('#ModalEdit').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiEdit').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEdit').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // ===============================================================================
    // HAPUS
    // ===============================================================================
    $(document).on('click', '.modal_hapus', function () {

        //tangkap data 'kfa_code' dan buat variabel
        var id_referensi_jenis_spesimen   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapus').modal('show');

        //Form Loading
        $('#FormHapus').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiJenisSpesimen/FormHapus.php',
            data        : {id_referensi_jenis_spesimen: id_referensi_jenis_spesimen},
            success     : function(data){
                $('#FormHapus').html(data);
            }
        });
    });

    //Proses Hapus
    $('#ProsesHapus').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesHapus = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiHapus').html('<small class="text-muted">Loading...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/ReferensiJenisSpesimen/ProsesHapus.php',
            dataType : 'json',
            data     : ProsesHapus,

            success: function(response){

                var status  = response.status;
                var message = response.message;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapus').html('');

                    // Tutup modal jika ada
                    $('#ModalHapus').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapus').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapus').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });
});





