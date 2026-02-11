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

    $('#id_referensi_satuan').select2({
        theme: "bootstrap-5",
        dropdownParent: $('#ModalTambah'),
        placeholder: "Pilih unit",
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url     : "_Page/ReferensiKemasanSample/list_satuan.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    keyword: params.term || "", 
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.results,
                    pagination: {
                        more: data.more
                    }
                };
            },
            cache: true
        }
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

                // Select2 Untuk Satuan
                 $('#id_referensi_satuan_edit').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#ModalEdit'),
                    placeholder: "Pilih Unit",
                    allowClear: true,
                    minimumInputLength: 0,
                    ajax: {
                        url     : "_Page/ReferensiKemasanSample/list_satuan.php",
                        type    : "POST",
                        dataType: "json",
                        delay   : 250,
                        data    : function (params) {
                            return {
                                keyword: params.term || "", 
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: data.results,
                                pagination: {
                                    more: data.more
                                }
                            };
                        },
                        cache: true
                    }
                });
            }
        });
    });

    // Jika Modal Edit Muncul
    $('#ModalEdit').on('show.bs.modal', function (e) {
       
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

    // ===================================================================================
    // TAMBAH NILAI RUJUKAN
    // ===================================================================================
     $(document).on('click', '.modal_tambah_range', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalTambahRange').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahRange').html('');

        //Form Loading
        $('#FormTambahRange').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormTambahRange.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormTambahRange').html(data);
            }
        });
    });

    /* Ketika 'ProsesTambahRange' disubmit */
    $('#ProsesTambahRange').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambahRange=$('#ProsesTambahRange').serialize();

        /* Loading Notification */
        $('#NotifikasiTambah').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesTambahRange.php',
            dataType: 'json',
            data    : ProsesTambahRange,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambahRange').html('');

                    //reset form
                    $('#ProsesTambahRange')[0].reset();

                    //Tutup modal
                    $('#ModalTambahRange').modal('hide');

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

                    //Tampilkan Ulang Data
                    ShowDetail();
                }else{
                    $('#NotifikasiTambahRange').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // DETAIL NILAI RUJUKAN
    // ===================================================================================
    $(document).on('click', '.modal_detail_range', function () {

        //tangkap data 'id_referensi_range' dan buat variabel
        var id_referensi_range   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailCategory').modal('show');

        //Form Loading
        $('#FormDetailRange').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormDetailRange.php',
            data        : {id_referensi_range: id_referensi_range},
            success     : function(data){
                $('#FormDetailRange').html(data);
            }
        });
    });

    // ===================================================================================
    // EDIT NILAI RUJUKAN
    // ===================================================================================
    $(document).on('click', '.modal_edit_range', function () {

        //tangkap data 'id_referensi_range' dan buat variabel
        var id_referensi_range   = $(this).data('id');

        //tampilkan modal
        $('#ModalEditRange').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditRange').html('');

        //Form Loading
        $('#FormEditRange').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormEditRange.php',
            data        : {id_referensi_range: id_referensi_range},
            success     : function(data){
                $('#FormEditRange').html(data);
            }
        });
    });

    /* Ketika 'ProsesEditRange' disubmit */
    $('#ProsesEditRange').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEditRange=$('#ProsesEditRange').serialize();

        /* Loading Notification */
        $('#NotifikasiEditRange').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesEditRange.php',
            dataType: 'json',
            data    : ProsesEditRange,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEditRange').html('');

                    //Tutup modal
                    $('#ModalEditRange').modal('hide');

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

                    //Tampilkan Ulang Data
                    ShowDetail();
                }else{
                    $('#NotifikasiEditRange').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // HAPUS NILAI RUJUKAN
    // ===================================================================================
    $(document).on('click', '.modal_delete_range', function () {

        //tangkap data 'id_referensi_range' dan buat variabel
        var id_referensi_range   = $(this).data('id');

        //tampilkan modal
        $('#ModalDeleteRange').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDeleteRange').html('');

        //Form Loading
        $('#FormDeleteRange').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormDeleteRange.php',
            data        : {id_referensi_range: id_referensi_range},
            success     : function(data){
                $('#FormDeleteRange').html(data);
            }
        });
    });

    /* Ketika 'ProsesDeleteRange' disubmit */
    $('#ProsesDeleteRange').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesDeleteRange=$('#ProsesDeleteRange').serialize();

        /* Loading Notification */
        $('#NotifikasiDeleteRange').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesDeleteRange.php',
            dataType: 'json',
            data    : ProsesDeleteRange,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiDeleteRange').html('');

                    //Tutup modal
                    $('#ModalDeleteRange').modal('hide');

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

                    //Tampilkan Ulang Data
                    ShowDetail();
                }else{
                    $('#NotifikasiDeleteRange').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // TAMBAH CATEGORY
    // ===================================================================================
    $(document).on('click', '.modal_tambah_category', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalTambahCategory').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahCategory').html('');

        //Form Loading
        $('#FormTambahCategory').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormTambahCategory.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormTambahCategory').html(data);
            }
        });
    });

    /* Ketika 'ProsesTambahCategory' disubmit */
    $('#ProsesTambahCategory').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambahCategory=$('#ProsesTambahCategory').serialize();

        /* Loading Notification */
        $('#NotifikasiTambahCategory').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesTambahCategory.php',
            dataType: 'json',
            data    : ProsesTambahCategory,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambahCategory').html('');

                    //reset form
                    $('#ProsesTambahCategory')[0].reset();

                    //Tutup modal
                    $('#ModalTambahCategory').modal('hide');

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

                    //Tampilkan Ulang Data
                    ShowDetail();
                }else{
                    $('#NotifikasiTambahCategory').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // DETAIL CATEGORY
    // ===================================================================================
    $(document).on('click', '.modal_detail_category', function () {

        //tangkap data 'id_referensi_category' dan buat variabel
        var id_referensi_category   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailCategory').modal('show');

        //Form Loading
        $('#FormDetailCategory').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormDetailCategory.php',
            data        : {id_referensi_category: id_referensi_category},
            success     : function(data){
                $('#FormDetailCategory').html(data);
            }
        });
    });

    // ===================================================================================
    // EDIT CATEGORY
    // ===================================================================================
    $(document).on('click', '.modal_edit_category', function () {

        //tangkap data 'id_referensi_category' dan buat variabel
        var id_referensi_category   = $(this).data('id');

        //tampilkan modal
        $('#ModalEditCategory').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditCategory').html('');

        //Form Loading
        $('#FormEditCategory').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormEditCategory.php',
            data        : {id_referensi_category: id_referensi_category},
            success     : function(data){
                $('#FormEditCategory').html(data);
            }
        });
    });

    /* Ketika 'ProsesEditCategory' disubmit */
    $('#ProsesEditCategory').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEditCategory=$('#ProsesEditCategory').serialize();

        /* Loading Notification */
        $('#NotifikasiEditCategory').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesEditCategory.php',
            dataType: 'json',
            data    : ProsesEditCategory,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEditCategory').html('');

                    //Tutup modal
                    $('#ModalEditCategory').modal('hide');

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

                    //Tampilkan Ulang Data
                    ShowDetail();
                }else{
                    $('#NotifikasiEditCategory').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // HAPUS CATEGORY
    // ===================================================================================
    $(document).on('click', '.modal_delete_category', function () {

        //tangkap data 'id_referensi_category' dan buat variabel
        var id_referensi_category   = $(this).data('id');

        //tampilkan modal
        $('#ModalDeleteCategory').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDeleteCategory').html('');

        //Form Loading
        $('#FormDeleteCategory').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormDeleteCategory.php',
            data        : {id_referensi_category: id_referensi_category},
            success     : function(data){
                $('#FormDeleteCategory').html(data);
            }
        });
    });

    /* Ketika 'ProsesDeleteCategory' disubmit */
    $('#ProsesDeleteCategory').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesDeleteCategory=$('#ProsesDeleteCategory').serialize();

        /* Loading Notification */
        $('#NotifikasiDeleteCategory').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesDeleteCategory.php',
            dataType: 'json',
            data    : ProsesDeleteCategory,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiDeleteCategory').html('');

                    //Tutup modal
                    $('#ModalDeleteCategory').modal('hide');

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

                    //Tampilkan Ulang Data
                    ShowDetail();
                }else{
                    $('#NotifikasiDeleteCategory').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });
    
});
