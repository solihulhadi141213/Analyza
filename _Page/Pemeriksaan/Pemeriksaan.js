//Fungsi Menampilkan Data Kunjungan
function ShowTable() {

    var $container = $('#TabelPemeriksaan');
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
        url     : '_Page/Pemeriksaan/TabelPemeriksaan.php',
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

function ShowTableKunjungan() {

    var $container = $('#TabelKunjungan');
    var heightBefore = $container.height(); // simpan tinggi awal

    var ProsesFilterKunjungan = $('#ProsesFilterKunjungan').serialize();

    // Kunci tinggi agar layout tidak loncat
    $container
        .css({
            'min-height': heightBefore + 'px',
            'opacity': 0.5
        });

    $.ajax({
        type    : 'POST',
        url     : '_Page/Pemeriksaan/TabelKunjungan.php',
        data    : ProsesFilterKunjungan,
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
        url     : '_Page/Pemeriksaan/_DetailPemeriksaan.php',
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

function SelectDokter() {

    let el = $('#nama_dokter_pengirim');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahPermintaan'),
        placeholder       : "Pilih Dokter Pengirim",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListDokter.php",
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
function SelectDokter2() {

    let el = $('#nama_dokter_penerima');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTerimaPermintaan'),
        placeholder       : "Pilih Dokter",
        allowClear        : true,
        tags              : true,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListDokter.php",
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

function ToggleFormTerimaPermintaanFields() {
    let status = $('#FormTerimaPermintaan #status').val();

    let showDiterima = (status === 'Diterima');
    let showAlasan = (status === 'Ditolak' || status === 'Dibatalkan');

    $('#FormTerimaPermintaan #wrap_datetime_diterima').toggle(showDiterima);
    $('#FormTerimaPermintaan #wrap_dokter_penerima').toggle(showDiterima);
    $('#FormTerimaPermintaan #wrap_alasan_penolakan').toggle(showAlasan);

    $('#FormTerimaPermintaan [name="tanggal_diterima"]').prop('required', showDiterima);
    $('#FormTerimaPermintaan [name="jam_diterima"]').prop('required', showDiterima);
    $('#FormTerimaPermintaan [name="nama_dokter_penerima"]').prop('required', showDiterima);
    $('#FormTerimaPermintaan #alasan').prop('required', showAlasan);

    if (!showAlasan) {
        $('#FormTerimaPermintaan #alasan').val('');
    }
    if (!showDiterima) {
        $('#FormTerimaPermintaan #nama_dokter_penerima').val(null).trigger('change');
        $('#FormTerimaPermintaan #kode_dokter_penerima').val('');
        $('#FormTerimaPermintaan #ihs_dokter_penerima').val('');
    }
}

function SelectDiagnosis() {

    let el = $('#id_diagnosis');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahPermintaan'),
        placeholder       : "Pilih Diagnosis (ICD10)",
        allowClear        : true,
        tags              : false,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListDoagnosis.php",
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

function SelectSpesimen() {
    let el = $('#id_referensi_jenis_spesimen');
    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }
    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahSpesimen'),
        placeholder       : "Pilih Jenis Spesimen",
        allowClear        : true,
        tags              : false,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListSpesimen.php",
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

function SelectMetodeSpesimen() {
    let el = $('#id_referensi_metode_sample');
    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }
    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahSpesimen'),
        placeholder       : "Pilih Metode",
        allowClear        : true,
        tags              : false,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListMetodeSpesimen.php",
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
function SelectBodySite() {
    let el = $('#id_referensi_body_site');
    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }
    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahSpesimen'),
        placeholder       : "Pilih Body Site",
        allowClear        : true,
        tags              : false,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListBodySite.php",
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
function SelectContainer() {
    let el = $('#id_referensi_container');
    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }
    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTambahSpesimen'),
        placeholder       : "Pilih Kemasan/Kontainer",
        allowClear        : true,
        tags              : false,
        minimumInputLength: 0,
        width             : "100%",
        ajax: {
            url     : "_Page/Pemeriksaan/ListKontainer.php",
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
            url 	    : '_Page/Pemeriksaan/FormFilter.php',
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
        $('#ModalKunjungan').modal('show');

        ShowTableKunjungan(0);
    });

    // Saat modal benar-benar tampil
    $('#ModalKunjungan').on('shown.bs.modal', function () {
        $('#keyword_kunjungan').focus().select();
        ShowTableKunjungan();
    });

    //Pagging kunjungan
    $(document).on('click', '#next_button_kunjungan', function() {
        var page_now = parseInt($('#page_kunjungan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page_kunjungan').val(next_page);
        ShowTableKunjungan(0);
    });
    $(document).on('click', '#prev_button_kunjungan', function() {
        var page_now = parseInt($('#page_kunjungan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page_kunjungan').val(next_page);
        ShowTableKunjungan(0);
    });

    // Submit Pencarian
    $('#ProsesFilterKunjungan').submit(function(e){

        e.preventDefault();
        // Reset Halaman
        $('#page_kunjungan').val(1);

        // Tampilkan Data
        ShowTableKunjungan(0);
    });

    //Menampilkan Form Tambah Permintaan
    $(document).on('click', '.tambah_permintaan', function () {

        var id_kunjungan = $(this).data('id');

        // Tampilkan ModalTambah
        $('#ModalTambah').modal('show');

        // Reset UI
        $('#NotifikasiTambahPermintaan').html('');
        $('#FormTambahPermintaan').html('Loading...');

        // Tutup Modal 'ModalKunjungan'
        $('#ModalKunjungan').modal('hide');

        // Load form via AJAX (TIDAK bergantung event modal)
        $.ajax({
            type: 'POST',
            url: '_Page/Pemeriksaan/FormTambahPermintaan.php',
            data: { id_kunjungan: id_kunjungan },
            success: function (data) {

                $('#FormTambahPermintaan').html(data);

                SelectDokter();
                SelectDiagnosis();
            }
        });
    });

    // SELECT2 METODE PEMERIKSAAN
    $(document).on('select2:select', '#nama_dokter_pengirim', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#nama_metode_pemeriksaan').val(data.nama);
            $('#kode_dokter_pengirim').val(data.kode || '');
            $('#ihs_dokter_pengirim').val(data.id_ihs_practitioner || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#nama_metode_pemeriksaan').val('');
            $('#kode_dokter_pengirim').val('');
            $('#ihs_dokter_pengirim').val('');
        }
    });
    $(document).on('select2:clear', '#nama_dokter_pengirim', function () {
        $('#nama_metode_pemeriksaan').val('');
        $('#kode_dokter_pengirim').val('');
        $('#ihs_dokter_pengirim').val('');
    });

    // SELECT2 DIAGNOSIS
    $(document).on('select2:select', '#id_diagnosis', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#id_diagnosis').val(data.short_des);
            $('#diagnosis_display').val(data.short_des);
            $('#diagnosis_code').val(data.kode || '');
            $('#diagnosis_system').val(data.system || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#diagnosis_display').val('');
            $('#diagnosis_code').val('');
            $('#diagnosis_system').val('');
        }
    });
    $(document).on('select2:clear', '#id_diagnosis', function () {
        $('#diagnosis_display').val('');
        $('#diagnosis_code').val('');
        $('#diagnosis_system').val('');
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
            url     : '_Page/Pemeriksaan/ProsesTambah.php',
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
        //Menangkap 'id_laboratorium'
        var id_laboratorium = $(this).data('id');

        // Menampilkan modal
        $('#ModalDetail').modal('show');

        //Menampilkan Detail Dengan AJAX
        $('#FormDetail').html('Loading...');
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/FormDetail.php',
            data    : {id_laboratorium: id_laboratorium},
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

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium   = $(this).data('id');

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
            url 	    : '_Page/Pemeriksaan/FormEdit.php',
            data        : {id_laboratorium: id_laboratorium},
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
            url     : '_Page/Pemeriksaan/ProsesEdit.php',
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

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDelete.php',
            data        : {id_laboratorium: id_laboratorium},
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
            url     : '_Page/Pemeriksaan/ProsesDelete.php',
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
    // PENERIMAAN/ PENOLAKAN PEMERIKSAAN
    // ===================================================================================
    $(document).on('click', '.modal_terima_pemeriksaan', function () {

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium   = $(this).data('id');

        //tampilkan modal
        $('#ModalTerimaPermintaan').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTerimaPermintaan').html('');

        //Form Loading
        $('#FormTerimaPermintaan').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormTerimaPermintaan.php',
            data        : {id_laboratorium: id_laboratorium},
            success     : function(data){
                $('#FormTerimaPermintaan').html(data);
                SelectDokter2();
                ToggleFormTerimaPermintaanFields();
            }
        });
    });

    $(document).on('change', '#FormTerimaPermintaan #status', function () {
        ToggleFormTerimaPermintaanFields();
    });

    // SELECT2 METODE PEMERIKSAAN
    $(document).on('select2:select', '#nama_dokter_penerima', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#nama_dokter_penerima').val(data.nama);
            $('#kode_dokter_penerima').val(data.kode || '');
            $('#ihs_dokter_penerima').val(data.id_ihs_practitioner || '');
        } else {
            // Jika manual input → kosongkan autofill
            $('#nama_dokter_penerima').val('');
            $('#kode_dokter_penerima').val('');
            $('#ihs_dokter_penerima').val('');
        }
    });
    $(document).on('select2:clear', '#nama_dokter_penerima', function () {
        $('#nama_dokter_penerima').val('');
        $('#kode_dokter_penerima').val('');
        $('#ihs_dokter_penerima').val('');
    });

    /* Ketika 'ProsesKirimServiceRequest' disubmit */
    $('#ProsesTerimaPermintaan').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTerimaPermintaan=$('#ProsesTerimaPermintaan').serialize();

        /* Loading Notification */
        $('#NotifikasiTerimaPermintaan').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesTerimaPermintaan.php',
            dataType: 'json',
            data    : ProsesTerimaPermintaan,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTerimaPermintaan').html('');

                    //Tutup modal
                    $('#ModalTerimaPermintaan').modal('hide');

                    //reload tabel
                    ShowDetail();
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
                    $('#NotifikasiTerimaPermintaan').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // ===================================================================================
    // PROCEDURE (PUASA)
    // ===================================================================================
    $(document).on('click', '.modal_kirim_procedure', function () {

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium   = $(this).data('id');

        //tampilkan modal
        $('#ModalKirimProcedure').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiKirimProcedure').html('');

        //Form Loading
        $('#FormKirimProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormKirimProcedure.php',
            data        : {id_laboratorium: id_laboratorium},
            success     : function(data){
                $('#FormKirimProcedure').html(data);
            }
        });
    });
    
    /* Ketika 'ProsesKirimProcedure' disubmit */
    $('#ProsesKirimProcedure').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesKirimProcedure=$('#ProsesKirimProcedure').serialize();

        /* Loading Notification */
        $('#NotifikasiKirimProcedure').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesKirimProcedure.php',
            dataType: 'json',
            data    : ProsesKirimProcedure,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiKirimProcedure').html('');

                    //Tutup modal
                    $('#ModalKirimProcedure').modal('hide');

                    //reload tabel
                    ShowDetail();
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
                    var payload = response.payload;
                    $('#NotifikasiKirimProcedure').html('<div class="alert alert-danger"><small>'+message+' <br> <pre>'+payload+'</pre></small></div>');
                }
                
            }
        });
    });

    // Kirim Manual
    $(document).on('click', '.modal_kirim_resource_procedure', function () {

        //tangkap data 'id_laboratorium_procedure' dan buat variabel
        var id_laboratorium_procedure   = $(this).data('id');

        //tampilkan modal
        $('#ModalKirimResourceProcedure').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiKirimResourceProcedure').html('');

        //Form Loading
        $('#FormKirimResourceProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormKirimResourceProcedure.php',
            data        : {id_laboratorium_procedure: id_laboratorium_procedure},
            success     : function(data){
                $('#FormKirimResourceProcedure').html(data);
            }
        });
    });

    /* Ketika 'ProsesKirimResourceProcedure' disubmit */
    $('#ProsesKirimResourceProcedure').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesKirimResourceProcedure=$('#ProsesKirimResourceProcedure').serialize();

        /* Loading Notification */
        $('#NotifikasiKirimResourceProcedure').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesKirimResourceProcedure.php',
            dataType: 'json',
            data    : ProsesKirimResourceProcedure,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiKirimResourceProcedure').html('');

                    //Tutup modal
                    $('#ModalKirimResourceProcedure').modal('hide');

                    //reload tabel
                    ShowDetail();
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
                    var payload = response.payload;
                    $('#NotifikasiKirimResourceProcedure').html('<div class="alert alert-danger"><small>'+message+' <br> <pre>'+payload+'</pre></small></div>');
                }
                
            }
        });
    });

    // Modal Edit Procedure
    $(document).on('click', '.modal_edit_procedure', function () {

        //tangkap data 'id_laboratorium_procedure' dan buat variabel
        var id_laboratorium_procedure   = $(this).data('id');

        //tampilkan modal
        $('#ModalEditProcedure').modal('show');

        //Form Loading
        $('#FormEditProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormEditProcedure.php',
            data        : {id_laboratorium_procedure: id_laboratorium_procedure},
            success     : function(data){
                $('#FormEditProcedure').html(data);
            }
        });
    });
    $(document).on('click', '.check_procedure_edit', function() {
        var code        = $(this).data('code');
        var display     = $(this).data('display');
        var system      = $(this).data('system');
        var description = $(this).data('description');

        // Tempel ke form
        $('.procedure_description_edit').val(description);
        $('.procedure_display_edit').val(display);
        $('.procedure_system_edit').val(system);
        $('.procedure_code_edit').val(code);
    });
    /* Ketika 'ProsesEditProcedure' disubmit */
    $('#ProsesEditProcedure').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEditProcedure=$('#ProsesEditProcedure').serialize();

        /* Loading Notification */
        $('#NotifikasiEditProcedure').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesEditProcedure.php',
            dataType: 'json',
            data    : ProsesEditProcedure,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEditProcedure').html('');

                    //Tutup modal
                    $('#ModalEditProcedure').modal('hide');

                    //reload tabel
                    ShowDetail();
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
                    var payload = response.payload;
                    $('#NotifikasiEditProcedure').html('<div class="alert alert-danger"><small>'+message+' <br> <pre>'+payload+'</pre></small></div>');
                }
                
            }
        });
    });

    // Modal Hapus Procedure
    $(document).on('click', '.modal_hapus_procedure', function () {

        //tangkap data 'id_laboratorium_procedure' dan buat variabel
        var id_laboratorium_procedure   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapusProcedure').modal('show');

        //Form Loading
        $('#FormHapusProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormHapusProcedure.php',
            data        : {id_laboratorium_procedure: id_laboratorium_procedure},
            success     : function(data){
                $('#FormHapusProcedure').html(data);
            }
        });
    });

    /* Ketika 'ProsesHapusProcedure' disubmit */
    $('#ProsesHapusProcedure').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesHapusProcedure=$('#ProsesHapusProcedure').serialize();

        /* Loading Notification */
        $('#NotifikasiHapusProcedure').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesHapusProcedure.php',
            dataType: 'json',
            data    : ProsesHapusProcedure,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiHapusProcedure').html('');

                    //Tutup modal
                    $('#ModalHapusProcedure').modal('hide');

                    //reload tabel
                    ShowDetail();
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
                    var payload = response.payload;
                    $('#NotifikasiHapusProcedure').html('<div class="alert alert-danger"><small>'+message+' <br> <pre>'+payload+'</pre></small></div>');
                }
                
            }
        });
    });

    // Modal Detail Procedure
    $(document).on('click', '.modal_detail_procedure', function () {

        //tangkap data 'id_procedure' dan buat variabel
        var id_procedure   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailProcedure').modal('show');

        //Form Loading
        $('#FormDetailProcedure').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailProcedure.php',
            data        : {id_procedure: id_procedure},
            success     : function(data){
                $('#FormDetailProcedure').html(data);
            }
        });
    });

    // ===================================================================================
    // KIRIM ServiceRequest
    // ===================================================================================
    $(document).on('click', '.modal_kirim_service_request', function () {

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium_rincian   = $(this).data('id');

        //tampilkan modal
        $('#ModalKirimServiceRequest').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiKirimServiceRequest').html('');

        //Form Loading
        $('#FormKirimServiceRequest').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormKirimServiceRequest.php',
            data        : {id_laboratorium_rincian: id_laboratorium_rincian},
            success     : function(data){
                $('#FormKirimServiceRequest').html(data);
            }
        });
    });

    /* Ketika 'ProsesKirimServiceRequest' disubmit */
    $('#ProsesKirimServiceRequest').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesKirimServiceRequest=$('#ProsesKirimServiceRequest').serialize();

        /* Loading Notification */
        $('#NotifikasiKirimServiceRequest').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesKirimServiceRequest.php',
            dataType: 'json',
            data    : ProsesKirimServiceRequest,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiKirimServiceRequest').html('');

                    //Tutup modal
                    $('#ModalKirimServiceRequest').modal('hide');

                    //reload tabel
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
                    $('#NotifikasiKirimServiceRequest').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    $(document).on('click', '.modal_detail_service_request', function () {

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium_rincian   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailServiceRequest').modal('show');

        //Form Loading
        $('#FormDetailServiceRequest').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailServiceRequest.php',
            data        : {id_laboratorium_rincian: id_laboratorium_rincian},
            success     : function(data){
                $('#FormDetailServiceRequest').html(data);
            }
        });
    });

    // ===================================================================================
    // SPESIMEN
    // ===================================================================================
    $(document).on('click', '.modal_tambah_spesimen', function () {

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium   = $(this).data('id');

        //tampilkan modal
        $('#ModalTambahSpesimen').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahSpesimen').html('');

        //Form Loading
        $('#FormTambahSpesimen').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormTambahSpesimen.php',
            data        : {id_laboratorium: id_laboratorium},
            success     : function(data){
                $('#FormTambahSpesimen').html(data);

                SelectSpesimen();
                SelectMetodeSpesimen();
                SelectBodySite();
                SelectContainer();
            }
        });

    });

    // Select Jenis Spesimen
    $(document).on('select2:select', '#id_referensi_jenis_spesimen', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#id_referensi_jenis_spesimen').val(data.id);
        } else {
            // Jika manual input → kosongkan autofill
            $('#id_referensi_jenis_spesimen').val('');
        }
    });
    $(document).on('select2:clear', '#id_referensi_jenis_spesimen', function () {
        $('#id_referensi_jenis_spesimen').val('');
    });
    
    // Select Metode Spesimen
    $(document).on('select2:select', '#id_referensi_metode_sample', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#id_referensi_metode_sample').val(data.id);
        } else {
            // Jika manual input → kosongkan autofill
            $('#id_referensi_metode_sample').val('');
        }
    });
    $(document).on('select2:clear', '#id_referensi_metode_sample', function () {
        $('#id_referensi_metode_sample').val('');
    });

    // Select Body Site
    $(document).on('select2:select', '#id_referensi_body_site', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#id_referensi_body_site').val(data.id);
        } else {
            // Jika manual input → kosongkan autofill
            $('#id_referensi_body_site').val('');
        }
    });
    $(document).on('select2:clear', '#id_referensi_body_site', function () {
        $('#id_referensi_body_site').val('');
    });

    // Select Kontainer
    $(document).on('select2:select', '#id_referensi_container', function (e) {
        let data = e.params.data || {};

        // Autofill jika dari database
        if (!data.isNew) {
            $('#id_referensi_container').val(data.id);
            $('#quantity_value').val(data.kapasitas);
            $('#quantity_unit').html(data.unit);
        } else {
            // Jika manual input → kosongkan autofill
            $('#id_referensi_container').val('');
            $('#quantity_value').val('');
            $('#quantity_unit').html('');
        }
    });
    $(document).on('select2:clear', '#id_referensi_container', function () {
        $('#id_referensi_container').val('');
        $('#quantity_value').val('');
        $('#quantity_unit').html('');
    });

    /* Ketika 'ProsesTambahSpesimen' disubmit */
    $('#ProsesTambahSpesimen').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambahSpesimen=$('#ProsesTambahSpesimen').serialize();

        /* Loading Notification */
        $('#NotifikasiTambahSpesimen').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesTambahSpesimen.php',
            dataType: 'json',
            data    : ProsesTambahSpesimen,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambahSpesimen').html('');

                    //Tutup modal
                    $('#ModalTambahSpesimen').modal('hide');

                    //reload tabel
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
                    $('#NotifikasiTambahSpesimen').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // Modal Detail Spesimen
    $(document).on('click', '.modal_detail_speciment', function () {
        
        //tangkap data 'id_laboratorium_spesimen' dan buat variabel
        var id_laboratorium_spesimen   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailSpesimen').modal('show');

        //Form Loading
        $('#FormDetailSpesimen').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailSpesimen.php',
            data        : {id_laboratorium_spesimen: id_laboratorium_spesimen},
            success     : function(data){
                $('#FormDetailSpesimen').html(data);
            }
        });
    });

    // Modal Kirim Spesimen
    $(document).on('click', '.modal_kirim_speciment', function () {
        
        //tangkap data 'id_laboratorium_spesimen' dan buat variabel
        var id_laboratorium_spesimen   = $(this).data('id');

        //tampilkan modal
        $('#ModalKirimSpeciment').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiKirimSpeciment').html('');

        //Form Loading
        $('#FormKirimSpeciment').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormKirimSpeciment.php',
            data        : {id_laboratorium_spesimen: id_laboratorium_spesimen},
            success     : function(data){
                $('#FormKirimSpeciment').html(data);
            }
        });
    });
    
    $('#ProsesKirimSpeciment').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesKirimSpeciment=$('#ProsesKirimSpeciment').serialize();

        /* Loading Notification */
        $('#NotifikasiKirimSpeciment').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesKirimSpeciment.php',
            dataType: 'json',
            data    : ProsesKirimSpeciment,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiKirimSpeciment').html('');

                    //Tutup modal
                    $('#ModalKirimSpeciment').modal('hide');

                    //reload tabel
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
                    $('#NotifikasiKirimSpeciment').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    // Modal Detail ID Spesimen Satusehat
    $(document).on('click', '.modal_detail_id_speciment', function () {
        
        //tangkap data 'id_speciment' dan buat variabel
        var id_speciment   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetailIdSpesimen').modal('show');

        //Form Loading
        $('#FormDetailIdSpesimen').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormDetailIdSpesimen.php',
            data        : {id_speciment: id_speciment},
            success     : function(data){
                $('#FormDetailIdSpesimen').html(data);
            }
        });
    });
});
