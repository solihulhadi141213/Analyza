//Fungsi Menampilkan Data Kunjungan
function ShowTable() {

    var $container = $('#TabelReferensiPemeriksaan');
    var heightBefore = $container.height();
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/ReferensiPemeriksaan/TabelReferensiPemeriksaan.php',
        data    : ProsesFilter,
        success : function (data) {

            // Fade out ringan
            $container.fadeOut(150, function () {

                // Ganti isi tabel
                $container.html(data);

                // Fade in
                $container.fadeIn(200, function () {

                    // Lepas kunci tinggi setelah render
                    $container.css({
                        'min-height': '',
                        'opacity': 1
                    });

                    // Re-init tooltip
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });
            });
        }
    });
}

//Fungsi Menampilkan List Kategori
function ShowListKategori() {
    $.ajax({
        type    : 'POST',
        url     : '_Page/ReferensiPemeriksaan/Listkategori.php',
        success: function(data) {
            $('.list_kategori').html(data);
        }
    });
}

//Fungsi Menampilkan Detail
function ShowDetail() {
    var $container = $('#DetailPemeriksaan');
    var heightBefore = $container.height();
    var ProsesDetail = $('#ProsesDetail').serialize();
    
    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/ReferensiPemeriksaan/_DetailPemeriksaan.php',
        data    : ProsesDetail,
        success : function (data) {

            // Fade out ringan
            $container.fadeOut(150, function () {

                // Ganti isi tabel
                $container.html(data);

                // Fade in
                $container.fadeIn(200, function () {

                    // Lepas kunci tinggi setelah render
                    $container.css({
                        'min-height': '',
                        'opacity': 1
                    });
                    // Re-init tooltip
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });
            });
        }
    });
}

//jika data selesai di load
$(document).ready(function() {
    
    // ===================================================================================
    // MENAMPILKAN HALAMAN PERTAMA KALI
    // ===================================================================================
    ShowTable();
    $('#data_table').show();
    $('#data_detail').hide();

    $(document).on('click', '.modal_filter', function(){
        $('#ModalFilter').modal('show');
    });

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        ShowTable();
        $('#ModalFilter').modal('hide');
    });

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTable(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTable(0);
    });

    // ===================================================================================
    // TAMBAH (INSERT) PMERIKSAAN
    // ===================================================================================
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');

        ShowListKategori();
    });

    /* Ketika 'result_type' diubah */
    $(document).on('change', '#result_type', function() {
        var result_type = $('#result_type').val();
        if(result_type=="Numeric" || result_type=="Decimal"){
            $('#result_interpertation_type').html(`
                <option value="">Pilih</option>
                <option value="Range">Range (Hasil merujuk pada jarak nilai tertentu)</option>
                <option value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                <option value="None">Interpertasi Tidak Digunakan</option>
            `);
        }
        if(result_type=="Coded" || result_type=="Text" || result_type=="Boolean"){
            $('#result_interpertation_type').html(`
                <option value="">Pilih</option>
                <option value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                <option value="None">Interpertasi Tidak Digunakan</option>
            `);
        }
    });

    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambah=$('#ProsesTambah').serialize();

        /* Loading Notification */
        $('#NotifikasiTambah').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesTambah.php',
            dataType: 'json',
            data    : ProsesTambah,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambah').html('');

                    //reset form
                    $('#ProsesTambah')[0].reset();

                    //Tutup modal
                    $('#ModalTambah').modal('hide');

                    // Tampilkan Pesan pada Toast
                    $('#put_message').html(
                        '<i class="bi bi-check-circle me-2"></i> ' + message
                    );
                    
                    // Menampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });
                    toast.show();

                    //Reset Filter
                    $('#ProsesFilter')[0].reset();

                    //Tampilkan Ulang Data
                    ShowTable();
                }else{
                    $('#NotifikasiTambah').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // DETAIL PEMERIKSAAN
    // ===================================================================================
    $(document).on('click', '.modal_detail', function(){
        //Menangkap 'id_referensi_pemeriksaan'
        var id_referensi_pemeriksaan = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan AJAX
        $('#FormDetail').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/FormDetail.php',
            data    : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success: function(data) {
                $('#FormDetail').html(data);

                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    });

    // Submit Data Selengkapnya
    $('#ProsesDetail').submit(function(){

        // Tutup Modal Detail
        $('#ModalDetail').modal('hide');

        // Sembunyikan Tabel
        $('#data_table').hide();

        // Tampilkan Detail
        $('#data_detail').show();

        // Load Detail
        ShowDetail();
    });

    // Kembali Ke Tabel Pemeriksaan
    $(document).on('click', '#kembali_ke_data', function () {
        // Sembunyikan Tabel
        $('#data_table').show();

        // Tampilkan Detail
        $('#data_detail').hide();

        // Load Tabel
        ShowTable();
    });

    // Reload Detail Pemeriksaan
    $(document).on('click', '.reload_detail', function () {

        // Load Detail
        ShowDetail();
    });

    // ===================================================================================
    // EDIT PEMERIKSAAN
    // ===================================================================================
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        // Load 'ShowListKategori'
        ShowListKategori();

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormEdit.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormEdit').html(data);
                var result_type = $('#result_type_edit').val();
            }
        });
    });

    /* Ketika 'result_type' diubah */
    $(document).on('change', '#result_type_edit', function() {
        var result_type = $('#result_type_edit').val();
        if(result_type=="Numeric" || result_type=="Decimal"){
            $('#result_interpertation_type_edit').html(`
                <option value="">Pilih</option>
                <option value="Range">Range (Hasil merujuk pada jarak nilai tertentu)</option>
                <option value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                <option value="None">Interpertasi Tidak Digunakan</option>
            `);
        }
        if(result_type=="Coded" || result_type=="Text" || result_type=="Boolean"){
            $('#result_interpertation_type_edit').html(`
                <option value="">Pilih</option>
                <option value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                <option value="None">Interpertasi Tidak Digunakan</option>
            `);
        }
    });
    
    /* Ketika 'ProsesEdit' disubmit */
    $('#ProsesEdit').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEdit=$('#ProsesEdit').serialize();

        /* Loading Notification */
        $('#NotifikasiEdit').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesEdit.php',
            dataType: 'json',
            data    : ProsesEdit,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEdit').html('');

                    //Tutup modal
                    $('#ModalEdit').modal('hide');

                    //reload tabel
                    ShowTable();

                    // Load Detail
                    ShowDetail();

                    // Tampilkan Pesan pada Toast
                    $('#put_message').html(
                        '<i class="bi bi-check-circle me-2"></i> ' + message
                    );
                    
                    // Menampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });
                    toast.show();
                }else{
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // HAPUS PEMERIKSAAN
    // ===================================================================================
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormDelete.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormDelete').html(data);
            }
        });
    });

    /* Ketika 'ProsesDelete' disubmit */
    $('#ProsesDelete').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesDelete=$('#ProsesDelete').serialize();

        /* Loading Notification */
        $('#NotifikasiDelete').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesDelete.php',
            dataType: 'json',
            data    : ProsesDelete,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiDelete').html('');

                    //Tutup modal
                    $('#ModalDelete').modal('hide');
                    
                    // Tampilkan Tabel
                    $('#data_table').show();

                    // Tutup Detail Detail
                    $('#data_detail').hide();

                    //reload tabel
                    ShowTable();

                    // Tampilkan Pesan pada Toast
                    $('#put_message').html(
                        '<i class="bi bi-check-circle me-2"></i> ' + message
                    );
                    
                    // Menampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {
                        delay: 3000
                    });
                    toast.show();
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });
});