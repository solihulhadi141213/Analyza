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

    $('#DaftarTags').html('Loading..');
    $.ajax({
        type    : 'POST',
        url     : '_Page/ReferensiPemeriksaan/DaftarTags.php',
        data    : ProsesFilter,
        success: function(data) {
            $('#DaftarTags').html(data);
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

function initSelect2MetodePemeriksaan() {

    let el = $('#id_referensi_metode_pemeriksaan');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahRelasi'),
        placeholder       : "Pilih atau ketik Metode Pemeriksaan",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/ReferensiPemeriksaan/ListMetodePemeriksaan.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.results,
                    pagination: { more: response.more }
                };
            },
            cache: true
        },

        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') return null;

            return {
                id: term,
                text: term,
                isNew: true
            };
        }
    });
}

function initSelect2JenisSpesimen() {

    let el = $('#id_referensi_jenis_spesimen');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahRelasi'),
        placeholder       : "Pilih Atau Ketik Jenis Spesimen",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/ReferensiPemeriksaan/ListJenisSpesimen.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.results,
                    pagination: { more: response.more }
                };
            },
            cache: true
        },

        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') return null;

            return {
                id: term,
                text: term,
                isNew: true
            };
        }
    });
}

function initSelect2MetodeSample() {

    let el = $('#id_referensi_metode_sample');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahRelasi'),
        placeholder       : "Pilih Atau Ketik Metode Spesimen",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/ReferensiPemeriksaan/ListMetodeSpesimen.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.results,
                    pagination: { more: response.more }
                };
            },
            cache: true
        },

        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') return null;

            return {
                id: term,
                text: term,
                isNew: true
            };
        }
    });
}

function initSelect2Kontainer() {

    let el = $('#id_referensi_container');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahRelasi'),
        placeholder       : "Pilih Atau Ketik Jenis kontainer",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/ReferensiPemeriksaan/ListJenisKontainer.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.results,
                    pagination: { more: response.more }
                };
            },
            cache: true
        },

        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') return null;

            return {
                id: term,
                text: term,
                isNew: true
            };
        }
    });
}

function initSelect2UnitKapasitas() {

    let el = $('#unit_container');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahRelasi'),
        placeholder       : "Pilih Atau Ketik Unit Satuan",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/ReferensiPemeriksaan/ListUnitKapasitas.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.results,
                    pagination: { more: response.more }
                };
            },
            cache: true
        },

        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') return null;

            return {
                id: term,
                text: term,
                isNew: true
            };
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

    // Ketika Tag dipilih
    $(document).on('click', '.PilihTags', function() {
       var PilihTags = $(this).data('id');
        $('#keyword').val(PilihTags);
        $('#KeywordBy').val('category_pemeriksaan');
        ShowTable(0);
    });

    // ===================================================================================
    // TAMBAH (INSERT) PMERIKSAAN
    // ===================================================================================
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');

        ShowListKategori();
    });

    // Autofocus
    $('#ModalTambah').on('shown.bs.modal', function () {
        $('#category_pemeriksaan').trigger('focus');
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

    // Ketika Operator Di Pilih
    $(document).on('change', '#operator', function() {
        var operator = $(this).val();
        // Reset nilai ke 0 setiap kali operator berubah
        $('#nilai_min, #nilai_max').val(0);

        if (operator == '' || operator == 'Between') {
            // Semuanya bisa diisi
            $('#nilai_min').prop('readonly', false);
            $('#nilai_max').prop('readonly', false);
        } 
        else if (operator == 'More') {
            // Hanya nilai_max yang bisa diisi, nilai_min dikunci
            $('#nilai_min').prop('readonly', false);
            $('#nilai_max').prop('readonly', true);
        }
    });

    /* Ketika 'result_type' diubah */
    $(document).on('change', '#result_type', function() {
        var result_type = $('#result_type').val();
        if(result_type=="Numeric" || result_type=="Decimal"){
            $('#result_interpertation_type').html(`
                <option value="Range">Range (Hasil merujuk pada jarak nilai tertentu)</option>
                <option value="None">Interpertasi Tidak Digunakan</option>
            `);
        }
        if(result_type=="Text" || result_type=="Boolean"){
            $('#result_interpertation_type').html(`
                <option value="">Pilih</option>
                <option value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
                <option value="None">Interpertasi Tidak Digunakan</option>
            `);
        }
        if(result_type=="Coded"){
            $('#result_interpertation_type').html(`
                <option value="Category">Category (Hasil merujuk pada kelompok kategori tertentu)</option>
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
        var id_referensi_pemeriksaan = $(this).data('id');
        var id_referensi_usia        = $(this).data('usia');

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
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan, id_referensi_usia: id_referensi_usia},
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
        $('#ModalDetailRange').modal('show');

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
        var id_referensi_pemeriksaan = $(this).data('id');
        var id_referensi_usia        = $(this).data('usia');

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
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan, id_referensi_usia: id_referensi_usia},
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

    // ===================================================================================
    // TAMBAH RELASI
    // ===================================================================================
    $(document).on('click', '.modal_tambah_relasi', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalTambahRelasi').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahRelasi').html('');

        //Form Loading
        $('#FormTambahRelasi').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/ReferensiPemeriksaan/FormTambahRelasi.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormTambahRelasi').html(data);

                // Panggil init Select2 setelah form dimuat
                initSelect2MetodePemeriksaan();
                initSelect2JenisSpesimen();
                initSelect2MetodeSample();
                initSelect2Kontainer();
                initSelect2UnitKapasitas();
            }
        });
    });

    // SELECT2 METODE PEMERIKSAAN
    $(document).on('select2:select', '#id_referensi_metode_pemeriksaan', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#nama_metode_pemeriksaan').val(data.text);
            $('#display_metode_pemeriksaan').val(data.display || '');
            $('#code_metode_pemeriksaan').val(data.code || '');
            $('#system_metode_pemeriksaan').val(data.system || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#nama_metode_pemeriksaan').val('');
            $('#display_metode_pemeriksaan').val('');
            $('#code_metode_pemeriksaan').val('');
            $('#system_metode_pemeriksaan').val('');
        }
    });

    $(document).on('select2:clear', '#id_referensi_metode_pemeriksaan', function () {
        $('#nama_metode_pemeriksaan').val('');
        $('#display_metode_pemeriksaan').val('');
        $('#code_metode_pemeriksaan').val('');
        $('#system_metode_pemeriksaan').val('');
    });

    // SELECT2 JENIS SPESIMEN
    $(document).on('select2:select', '#id_referensi_jenis_spesimen', function (e) {
        let data = e.params.data || {};

        // Selalu simpan TEXT (nama metode)
        $('#nama_spesimen').val(data.text);

        // Autofill jika dari database
        if (!data.isNew) {
            $('#display_spesimen').val(data.display || '');
            $('#code_spesimen').val(data.code || '');
            $('#system_spesimen').val(data.system || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#display_spesimen').val('');
            $('#code_spesimen').val('');
            $('#system_spesimen').val('');
        }
    });

    $(document).on('select2:clear', '#id_referensi_jenis_spesimen', function () {
        $('#nama_spesimen').val('');
        $('#display_spesimen').val('');
        $('#code_spesimen').val('');
        $('#system_spesimen').val('');
    });

    // SELECT2 METODE SAMPLE
    $(document).on('select2:select', '#id_referensi_metode_sample', function (e) {
        let data = e.params.data || {};

        // Selalu simpan TEXT (nama metode)
        $('#nama_metode_sample').val(data.text);

        // Autofill jika dari database
        if (!data.isNew) {
            $('#display_metode_sample').val(data.display || '');
            $('#code_metode_sample').val(data.code || '');
            $('#system_metode_sample').val(data.system || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#display_metode_sample').val('');
            $('#code_metode_sample').val('');
            $('#system_metode_sample').val('');
        }
    });

    $(document).on('select2:clear', '#id_referensi_metode_sample', function () {
        $('#nama_metode_sample').val('');
        $('#display_metode_sample').val('');
        $('#code_metode_sample').val('');
        $('#system_metode_sample').val('');
    });

    // SELECT2 KONTAINER
    $(document).on('select2:select', '#id_referensi_container', function (e) {
        let data = e.params.data || {};

        // Selalu simpan TEXT (nama metode)
        $('#nama_container').val(data.text);

        // Autofill jika dari database
        if (!data.isNew) {
            var unit_ex = data.unit;

            $('#display_container').val(data.display || '');
            $('#code_container').val(data.code || '');
            $('#system_container').val(data.system || '');
            $('#kapasitas_container').val(data.kapasitas || '');
            $('#unit_container').html('<option selected value="'+unit_ex+'">'+unit_ex+'</option>');
            $('#code_unit_container').val(data.unit_code || '');
            $('#system_unit_container').val(data.unit_system || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#display_container').val('');
            $('#code_container').val('');
            $('#system_container').val('');
            $('#kapasitas_container').val('');
            $('#unit_container').html('');
            $('#code_unit_container').val('');
            $('#system_unit_container').val('');
        }
    });

    $(document).on('select2:clear', '#id_referensi_container', function () {
        $('#nama_container').val('');
        $('#display_container').val('');
        $('#code_container').val('');
        $('#system_container').val('');
        $('#kapasitas_container').val('');
        $('#unit_container').html('');
        $('#code_unit_container').val('');
        $('#system_unit_container').val('');
    });

    // SELECT2 UNIT KAPASITAS
    $(document).on('select2:select', '#unit_container', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#code_unit_container').val(data.code || '');
            $('#system_unit_container').val(data.system || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#code_unit_container').val('');
            $('#system_unit_container').val('');
        }
    });

    $(document).on('select2:clear', '#unit_container', function () {
        $('#unit_container').val('');
        $('#code_unit_container').val('');
        $('#system_unit_container').val('');
    });



    /* Ketika 'ProsesTambahRelasi' disubmit */
    $('#ProsesTambahRelasi').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambahRelasi=$('#ProsesTambahRelasi').serialize();

        /* Loading Notification */
        $('#NotifikasiTambahRelasi').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesTambahRelasi.php',
            dataType: 'json',
            data    : ProsesTambahRelasi,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambahRelasi').html('');

                    //reset form
                    $('#ProsesTambahRelasi')[0].reset();

                    //Tutup modal
                    $('#ModalTambahRelasi').modal('hide');

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
                    $('#NotifikasiTambahRelasi').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // HAPUS RELASI
    // ===================================================================================
    $(document).on('click', '.modal_delete_relasi', function () {

        //tangkap data 'id_referensi_pemeriksaan_relasi' dan buat variabel
        var id_referensi_pemeriksaan_relasi   = $(this).data('id');

        //tampilkan modal
        $('#ModalDeleteRelasi').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDeleteRelasi').html('');

        //Form Loading
        $('#FormDeleteRelasi').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormDeleteRelasi.php',
            data        : {id_referensi_pemeriksaan_relasi: id_referensi_pemeriksaan_relasi},
            success     : function(data){
                $('#FormDeleteRelasi').html(data);
            }
        });
    });

    /* Ketika 'ProsesDeleteRelasi' disubmit */
    $('#ProsesDeleteRelasi').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesDeleteRelasi=$('#ProsesDeleteRelasi').serialize();

        /* Loading Notification */
        $('#NotifikasiDeleteRelasi').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesDeleteRelasi.php',
            dataType: 'json',
            data    : ProsesDeleteRelasi,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiDeleteRelasi').html('');

                    //Tutup modal
                    $('#ModalDeleteRelasi').modal('hide');

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
                    $('#NotifikasiDeleteRelasi').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // REFERENSI INTERPERTASI
    // ===================================================================================
    $(document).on('click', '.modal_referensi_interpertasi', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalReferensiInterpertasi').modal('show');

        //Form Loading
        $('#FormReferensiInterpertasi').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormReferensiInterpertasi.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormReferensiInterpertasi').html(data);
            }
        });
    });

    // ===================================================================================
    // KLASIFIKASI USIA
    // ===================================================================================
    $(document).on('click', '.modal_tambah_kelas_usia', function () {

        //tangkap data 'id_referensi_pemeriksaan' dan buat variabel
        var id_referensi_pemeriksaan   = $(this).data('id');

        //tampilkan modal
        $('#ModalTambahKlasifikasiUsia').modal('show');
        
        //Kosongkan Notifikasi
        $('#NotifikasiTambahKlasifikasiUsia').html('');
        
        //Form Loading
        $('#FormTambahKlasifikasiUsia').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormTambahKlasifikasiUsia.php',
            data        : {id_referensi_pemeriksaan: id_referensi_pemeriksaan},
            success     : function(data){
                $('#FormTambahKlasifikasiUsia').html(data);
            }
        });
    });

    /* Ketika 'ProsesTambahKlasifikasiUsia' disubmit */
    $('#ProsesTambahKlasifikasiUsia').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambahKlasifikasiUsia=$('#ProsesTambahKlasifikasiUsia').serialize();

        /* Loading Notification */
        $('#NotifikasiTambahKlasifikasiUsia').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesTambahKlasifikasiUsia.php',
            dataType: 'json',
            data    : ProsesTambahKlasifikasiUsia,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambahKlasifikasiUsia').html('');

                    //Tutup modal
                    $('#ModalTambahKlasifikasiUsia').modal('hide');

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
                    $('#NotifikasiTambahKlasifikasiUsia').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_edit_kelasifikasi_usia', function () {

        //tangkap data 'id_referensi_usia' dan buat variabel
        var id_referensi_usia   = $(this).data('id');

        //tampilkan modal
        $('#ModalEditKlasifikasiUsia').modal('show');
        
        //Kosongkan Notifikasi
        $('#NotifikasiEditKlasifikasiUsia').html('');
        
        //Form Loading
        $('#FormEditKlasifikasiUsia').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormEditKlasifikasiUsia.php',
            data        : {id_referensi_usia: id_referensi_usia},
            success     : function(data){
                $('#FormEditKlasifikasiUsia').html(data);
            }
        });
    });

    /* Ketika 'ProsesEditKlasifikasiUsia' disubmit */
    $('#ProsesEditKlasifikasiUsia').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEditKlasifikasiUsia=$('#ProsesEditKlasifikasiUsia').serialize();

        /* Loading Notification */
        $('#NotifikasiEditKlasifikasiUsia').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesEditKlasifikasiUsia.php',
            dataType: 'json',
            data    : ProsesEditKlasifikasiUsia,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEditKlasifikasiUsia').html('');

                    //Tutup modal
                    $('#ModalEditKlasifikasiUsia').modal('hide');

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
                    $('#NotifikasiEditKlasifikasiUsia').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_hapus_kelasifikasi_usia', function () {

        //tangkap data 'id_referensi_usia' dan buat variabel
        var id_referensi_usia   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapusKlasifikasiUsia').modal('show');
        
        //Kosongkan Notifikasi
        $('#NotifikasiHapusKlasifikasiUsia').html('');
        
        //Form Loading
        $('#FormHapusKlasifikasiUsia').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type        : 'POST',
            url         : '_Page/ReferensiPemeriksaan/FormHapusKlasifikasiUsia.php',
            data        : {id_referensi_usia: id_referensi_usia},
            success     : function(data){
                $('#FormHapusKlasifikasiUsia').html(data);
            }
        });
    });

    /* Ketika 'ProsesHapusKlasifikasiUsia' disubmit */
    $('#ProsesHapusKlasifikasiUsia').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesHapusKlasifikasiUsia=$('#ProsesHapusKlasifikasiUsia').serialize();

        /* Loading Notification */
        $('#NotifikasiHapusKlasifikasiUsia').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/ReferensiPemeriksaan/ProsesHapusKlasifikasiUsia.php',
            dataType: 'json',
            data    : ProsesHapusKlasifikasiUsia,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiHapusKlasifikasiUsia').html('');

                    //Tutup modal
                    $('#ModalHapusKlasifikasiUsia').modal('hide');

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
                    $('#NotifikasiHapusKlasifikasiUsia').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });
    
});
