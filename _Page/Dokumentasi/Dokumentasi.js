//Fungsi Menampilkan Data
function ShowData() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    var $tabel       = $('#TabelDokumentasi');

    // Tambahkan efek visual loading (opacity menurun)
    $tabel.css({
        'opacity': '0.5',
        'pointer-events': 'none',
        'transition': 'opacity 0.3s ease'
    });

    $.ajax({
        type   : 'POST',
        url    : '_Page/Dokumentasi/TabelDokumentasi.php',
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

function SelectKategoriDokumentasi() {

    let el = $('#dokumentasi_category');

    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme          : "bootstrap-5",
        dropdownParent : $('#ModalTambah'),
        placeholder    : "Pilih atau ketik kategori",
        allowClear     : true,
        tags           : true,
        width          : "100%",
        minimumInputLength: 0,

        ajax: {
            url     : "_Page/Dokumentasi/list_kategori.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page  : params.page || 1
                };
            },
            processResults: function (response, params) {

                params.page = params.page || 1;

                return {
                    results: (response && response.results) ? response.results : [],
                    pagination: {
                        more: (response && response.more) ? response.more : false
                    }
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
                newTag: true
            };
        },

        insertTag: function (data, tag) {
            // Tag baru muncul di atas
            data.unshift(tag);
        }

    });
}
function SelectKategoriDokumentasiEdit() {

    let el = $('#dokumentasi_category_edit');

    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme          : "bootstrap-5",
        dropdownParent : $('#ModalEdit'),
        placeholder    : "Pilih atau ketik kategori",
        allowClear     : true,
        tags           : true,
        width          : "100%",
        minimumInputLength: 0,

        ajax: {
            url     : "_Page/Dokumentasi/list_kategori.php",
            type    : "POST",
            dataType: "json",
            delay   : 250,
            data    : function (params) {
                return {
                    search: params.term,
                    page  : params.page || 1
                };
            },
            processResults: function (response, params) {

                params.page = params.page || 1;

                return {
                    results: (response && response.results) ? response.results : [],
                    pagination: {
                        more: (response && response.more) ? response.more : false
                    }
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
                newTag: true
            };
        },

        insertTag: function (data, tag) {
            // Tag baru muncul di atas
            data.unshift(tag);
        }

    });
}
//Fungsi Menampilkan Detail
function ShowDetail(id) {
    var $tabel       = $('#detail_dokumentasi');

    // Tambahkan efek visual loading (opacity menurun)
    $tabel.css({
        'opacity': '0.5',
        'pointer-events': 'none',
        'transition': 'opacity 0.3s ease'
    });

    $.ajax({
        type   : 'POST',
        url    : '_Page/Dokumentasi/DetailDokumentasi.php',
        data   : {id: id},
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
            $tabel.html('<div class="alert alert-danger"><small>Gagal Menampilkan Data</small></div>');
            $tabel.css({
                'opacity': '1',
                'pointer-events': 'auto'
            });
        }
    });
}

//Menampilkan Data Pertama Kali
$(document).ready(function() {

    // Sembunyikan 'detail_dokumentasi'
    $('#detail_dokumentasi').hide();

    // Munculkan 'data_dokumentasi'
    $('#data_dokumentasi').show();

    //Menampilkan Data Pertama Kali
    ShowData();

    //Ketika KeywordBy diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Dokumentasi/FormFilter.php',
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
    // TAMBAH
    // ===============================================================================
    
    //Ketika ModalTambah muncul, inisiasi Select2
    $(document).on('shown.bs.modal', '#ModalTambah', function () {
        SelectKategoriDokumentasi();
    });

    // Membatasi jumlah karakter yang diinput ke dokumentasi_description
    const maxLength = 1000;
    $('#dokumentasi_description').attr('maxlength', maxLength);

    $('#dokumentasi_description').on('input', function(){

        let currentLength = $(this).val().length;

        $('#jumlah_karakter_deskripsi').text(currentLength + ' / ' + maxLength);

        if(currentLength >= maxLength){
            $('#jumlah_karakter_deskripsi').removeClass('text-muted')
                                           .addClass('text-danger');
        }else{
            $('#jumlah_karakter_deskripsi').removeClass('text-danger')
                                           .addClass('text-muted');
        }

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
            url      : '_Page/Dokumentasi/ProsesTambah.php',
            dataType : 'json',
            data     : ProsesTambah,

            success: function(response){

                var payload  = response.payload;
                var status  = response.status;
                var message = response.message || 'Proses berhasil';

                if(status === 'success'){
                    var insert_id  = payload.insert_id;

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

                    // Sembunyikan Data
                    $('#data_dokumentasi').hide();

                    // Tampilkan detail
                    $('#detail_dokumentasi').show();

                    // Panggil Fungsi Menampilkan Detail
                    ShowDetail(insert_id);
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
    // DETAIL
    // ===============================================================================
    $(document).on('click', '.modal_detail', function () {

        //tangkap data 'id_dokumentasi' dan buat variabel
        var id_dokumentasi   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        //Form Loading
        $('#FormDetail').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Dokumentasi/FormDetail.php',
            data        : {id_dokumentasi: id_dokumentasi},
            success     : function(data){
                $('#FormDetail').html(data);

                // 🔁 Re-inisialisasi tooltip setelah data dimuat
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    });

    // Detail Selengkapnya
    $('#ProsesDetail').submit(function(e){
        
        // Ambil Data Dari form
        var id_dokumentasi = $('#id_dokumentasi').val();

        // Tutup Modal
        $('#ModalDetail').modal('hide');

        // Sembunyikan Data
        $('#data_dokumentasi').hide();

        // Tampilkan detail
        $('#detail_dokumentasi').show();

        // Panggil Fungsi Menampilkan Detail
        ShowDetail(id_dokumentasi);
        
    });

    // Ketika Di reload
    $(document).on('click', '.reload_detail', function () {

        //tangkap data 'id_dokumentasi' dan buat variabel
        var id_dokumentasi   = $(this).data('id');
        
        // Panggil Fungsi Menampilkan Detail
        ShowDetail(id_dokumentasi);
        
    });

    // Kembali Ke Data
    $(document).on('click', '.kembali_ke_data', function () {
        // Sembunyikan Data
        $('#data_dokumentasi').show();

        // Tampilkan detail
        $('#detail_dokumentasi').hide();
    });

    // ===============================================================================
    // TAMBAH KONTEN
    // ===============================================================================
    // Ketika 'modal_tambah_konten' di clcik
    $(document).on('click', '.modal_tambah_konten', function () {
        
        // Tangkap Data Dari Tombol
        var id_dokumentasi = $(this).data('id');
        var order          = $(this).data('order');
        var order_by       = $(this).data('order_by');
        var type_content   = $(this).data('type_content');

        // Tampilkan Modal
        $('#ModalTambahKonten').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambahKonten').html('');

        // Loading Form
        $('#FormTambahKonten').html('Loading...');
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Dokumentasi/FormTambahKonten.php',
            data        : {id_dokumentasi: id_dokumentasi, order: order, order_by: order_by, type_content: type_content},
            success: function(data){
                $('#FormTambahKonten').html(data);

                // Jika ada editor quill
                if ($('#editor_quill').length) {

                    var quill = new Quill('#editor_quill', {
                        theme: 'snow',
                        placeholder: 'Tulis isi konten...',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                ['blockquote', 'code-block'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });

                    // Simpan instance global supaya bisa diakses saat submit
                    window.quillEditor = quill;
                }
            }
        });

    });

    // Validasi file saat dipilih
    $(document).on('change', 'input[type="file"]', function () {

        var fileInput = this;
        var file      = fileInput.files[0];

        if (!file) return;

        var typeContent = $('input[name="type_content"]').val();
        var fileSize    = file.size; // byte
        var fileName    = file.name.toLowerCase();
        var ext         = fileName.split('.').pop();

        var allowedImage = ['jpg','jpeg','png','gif','webp','bmp'];
        var allowedVideo = ['mp4','webm','ogg','mov','avi','mkv'];

        var maxImageSize = 5 * 1024 * 1024;   // 5MB
        var maxVideoSize = 50 * 1024 * 1024;  // 50MB

        var errorMessage = '';

        if(typeContent === 'image'){

            if(!allowedImage.includes(ext)){
                errorMessage = 'Tipe file gambar tidak diizinkan!';
            }

            if(fileSize > maxImageSize){
                errorMessage = 'Ukuran gambar maksimal 5MB!';
            }

        }else if(typeContent === 'video'){

            if(!allowedVideo.includes(ext)){
                errorMessage = 'Tipe file video tidak diizinkan!';
            }

            if(fileSize > maxVideoSize){
                errorMessage = 'Ukuran video maksimal 50MB!';
            }

        }

        if(errorMessage !== ''){
            $('#NotifikasiTambahKonten').html(`
                <div class="alert alert-danger text-center">
                    <small>${errorMessage}</small>
                </div>
            `);

            $(this).val(''); // reset file
        }else{
            $('#NotifikasiTambahKonten').html(`
                <div class="alert alert-success text-center">
                    <small>File valid ✔</small>
                </div>
            `);
        }
    });

    // ADD MULTIPLE FORM
    $(document).on('click', '.add_multiple_form', function () {

        $('#list_container').append(`
            <div class="row mb-2 list_item_row">
                <div class="col-10">
                    <input type="text" name="value_content[]" class="form-control">
                </div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-floating btn-md btn-outline-danger delete_multiple_form">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `);

    });


    // DELETE MULTIPLE FORM
    $(document).on('click', '.delete_multiple_form', function () {

        if($('.list_item_row').length > 1){
            $(this).closest('.list_item_row').remove();
        }else{
            $('#NotifikasiTambahKonten').html(`
                <div class="alert alert-warning text-center">
                    <small>Minimal harus ada satu item list!</small>
                </div>
            `);
        }

    });

    $('#ProsesTambahKonten').on('submit', function(e){
        e.preventDefault();

        var formData = new FormData(this);

        // Ambil ulang type_content di sini
        var type_content = $('input[name="type_content"]').val();

        // Khusus Quill
        if(type_content === 'text' && window.quillEditor){
            var isi = window.quillEditor.root.innerHTML.trim();

            if(isi === '<p><br></p>' || isi === ''){
                $('#NotifikasiTambahKonten').html(`
                    <div class="alert alert-danger text-center">
                        <small>Isi konten tidak boleh kosong!</small>
                    </div>
                `);
                return false;
            }

            formData.set('value_content', isi);
        }

        $('#NotifikasiTambahKonten').html('<small class="text-muted">Menyimpan data...</small>');

        $.ajax({
            type       : 'POST',
            url        : '_Page/Dokumentasi/ProsesTambahKonten.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'json',

            success: function(response){

                if(response.status === 'success'){

                    $('#ModalTambahKonten').modal('hide');

                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + response.message);
                    toast.show();

                    ShowDetail($('input[name="id_dokumentasi"]').val());

                }else{
                    $('#NotifikasiTambahKonten').html(
                        '<div class="alert alert-danger"><small>'+response.message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);
                $('#NotifikasiTambahKonten').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });

    });


    // ===============================================================================
    // EDIT
    // ===============================================================================
    $(document).on('click', '.modal_edit', function () {

        var id_dokumentasi = $(this).data('id');

        $('#ModalEdit').modal('show');
        $('#FormEdit').html('<div class="text-center">Loading...</div>');

        $.ajax({
            type: 'POST',
            url: '_Page/Dokumentasi/FormEdit.php',
            data: {id_dokumentasi: id_dokumentasi},
            success: function(data){

                $('#FormEdit').html(data);

                // Tooltip
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Inisialisasi Select2
                SelectKategoriDokumentasiEdit();

                // ===== CHARACTER COUNTER =====
                const maxLength2 = 1000;
                const textarea = $('#dokumentasi_description_edit');

                textarea.attr('maxlength', maxLength2);

                function updateCounter() {
                    let currentLength = textarea.val().length;

                    $('#jumlah_karakter_deskripsi_edit')
                        .text(currentLength + ' / ' + maxLength2);

                    if(currentLength >= maxLength2){
                        $('#jumlah_karakter_deskripsi_edit')
                            .removeClass('text-muted')
                            .addClass('text-danger');
                    } else {
                        $('#jumlah_karakter_deskripsi_edit')
                            .removeClass('text-danger')
                            .addClass('text-muted');
                    }
                }

                // Trigger awal supaya langsung tampil jumlah karakter
                updateCounter();

                // Event input
                textarea.on('input', function(){
                    updateCounter();
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
            url      : '_Page/Dokumentasi/ProsesEdit.php',
            dataType : 'json',
            data     : ProsesEdit,

            success: function(response){

                var status  = response.status;
                var message = response.message;
                var id = response.id;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEdit').html('');

                    // Tutup modal jika ada
                    $('#ModalEdit').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();
                    ShowDetail(id);

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

        //tangkap data 'id_dokumentasi' dan buat variabel
        var id_dokumentasi   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapus').modal('show');

        //Form Loading
        $('#FormHapus').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Dokumentasi/FormHapus.php',
            data        : {id_dokumentasi: id_dokumentasi},
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
            url      : '_Page/Dokumentasi/ProsesHapus.php',
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

                    // Tampilkan Data
                    $('#data_dokumentasi').show();

                    // Sembunyikan detail
                    $('#detail_dokumentasi').hide();

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

    // ===============================================================================
    // UPDATE STATUS
    // ===============================================================================
    $(document).on('click', '.modal_update_status', function () {
        // Tangkap Data
        var id_dokumentasi = $(this).data('id');

        // Tampilkan Modal
        $('#ModalUpdateStatus').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiUpdateStatus').html('');

        // Loading Form
        $('#FormUpdateStatus').html('<div class="text-center">Loading...</div>');

        // Tampilkan Form Dengan AJAX
        $.ajax({
            type: 'POST',
            url: '_Page/Dokumentasi/FormUpdateStatus.php',
            data: {id_dokumentasi: id_dokumentasi},
            success: function(data){
                $('#FormUpdateStatus').html(data);
            }
        });

    });

    //Proses Update Status Konten
    $('#ProsesUpdateStatus').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesUpdateStatus = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiUpdateStatus').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Dokumentasi/ProsesUpdateStatus.php',
            dataType : 'json',
            data     : ProsesUpdateStatus,

            success: function(response){

                var status  = response.status;
                var message = response.message;
                var id = response.id;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiUpdateStatus').html('');

                    // Tutup modal jika ada
                    $('#ModalUpdateStatus').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();
                    ShowDetail(id);

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiUpdateStatus').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiUpdateStatus').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // ===============================================================================
    // HAPUS KONTEN
    // ===============================================================================
    $(document).on('click', '.modal_hapus_konten', function () {
        // Tangkap Data
        var id_dokumentasi_content = $(this).data('id');
        var id_dokumentasi = $(this).data('id_dokumentasi');

        // Tampilkan Modal
        $('#ModalHapusKonten').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiHapusKonten').html('');

        // Loading Form
        $('#FormHapusKonten').html('<div class="text-center">Loading...</div>');

        // Tampilkan Form Dengan AJAX
        $.ajax({
            type: 'POST',
            url: '_Page/Dokumentasi/FormHapusKonten.php',
            data: {id_dokumentasi_content: id_dokumentasi_content, id_dokumentasi: id_dokumentasi},
            success: function(data){
                $('#FormHapusKonten').html(data);
            }
        });

    });

    //Proses Hapus Konten
    $('#ProsesHapusKonten').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesHapusKonten = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiHapusKonten').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Dokumentasi/ProsesHapusKonten.php',
            dataType : 'json',
            data     : ProsesHapusKonten,

            success: function(response){

                var status  = response.status;
                var message = response.message;
                var id = response.id;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiHapusKonten').html('');

                    // Tutup modal jika ada
                    $('#ModalHapusKonten').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();
                    ShowDetail(id);

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiHapusKonten').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiHapusKonten').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

    // ===============================================================================
    // EDIT KONTEN
    // ===============================================================================
    $(document).on('click', '.modal_edit_konten', function () {
        // Tangkap Data
        var id_dokumentasi_content = $(this).data('id');
        var id_dokumentasi = $(this).data('id_dokumentasi');

        // Tampilkan Modal
        $('#ModalEditKonten').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEditKonten').html('');

        // Loading Form
        $('#FormEditKonten').html('<div class="text-center">Loading...</div>');

        // Tampilkan Form Dengan AJAX
        $.ajax({
            type: 'POST',
            url: '_Page/Dokumentasi/FormEditKonten.php',
            data: {id_dokumentasi_content: id_dokumentasi_content, id_dokumentasi: id_dokumentasi},
            success: function(data){
                $('#FormEditKonten').html(data);

                // Jika ada editor quill
                if ($('#editor_quill_edit').length) {

                    var quill = new Quill('#editor_quill_edit', {
                        theme: 'snow',
                        placeholder: 'Tulis isi konten...',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                ['blockquote', 'code-block'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });

                    // Simpan instance global supaya bisa diakses saat submit
                    window.quillEditor = quill;
                }
            }
        });
    });

    // Validasi file saat dipilih
    $(document).on('change', '#value_content_file_edit', function () {

        var fileInput = this;
        var file      = fileInput.files[0];

        if (!file) return;

        var typeContent = $('#type_content_edit').val();
        var fileSize    = file.size; // byte
        var fileName    = file.name.toLowerCase();
        var ext         = fileName.split('.').pop();

        var allowedImage = ['jpg','jpeg','png','gif','webp','bmp'];
        var allowedVideo = ['mp4','webm','ogg','mov','avi','mkv'];

        var maxImageSize = 5 * 1024 * 1024;   // 5MB
        var maxVideoSize = 50 * 1024 * 1024;  // 50MB

        var errorMessage = '';

        if(typeContent === 'image'){

            if(!allowedImage.includes(ext)){
                errorMessage = 'Tipe file gambar tidak diizinkan!';
            }

            if(fileSize > maxImageSize){
                errorMessage = 'Ukuran gambar maksimal 5MB!';
            }

        }else if(typeContent === 'video'){

            if(!allowedVideo.includes(ext)){
                errorMessage = 'Tipe file video tidak diizinkan!';
            }

            if(fileSize > maxVideoSize){
                errorMessage = 'Ukuran video maksimal 50MB!';
            }

        }

        if(errorMessage !== ''){
            $('#NotifikasiEditKonten').html(`
                <div class="alert alert-danger text-center">
                    <small>${errorMessage}</small>
                </div>
            `);

            $(this).val(''); // reset file
        }else{
            $('#NotifikasiEditKonten').html(`
                <div class="alert alert-success text-center">
                    <small>File valid ✔</small>
                </div>
            `);
        }
    });

    // ADD MULTIPLE FORM
    $(document).on('click', '.add_multiple_form_edit', function () {

        $('#list_container_edit').append(`
            <div class="row mb-2 list_item_row_edit">
                <div class="col-10">
                    <input type="text" name="value_content[]" class="form-control">
                </div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-floating btn-md btn-outline-danger delete_multiple_form_edit">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `);

    });


    // DELETE MULTIPLE FORM
    $(document).on('click', '.delete_multiple_form_edit', function () {

        if($('.list_item_row_edit').length > 1){
            $(this).closest('.list_item_row_edit').remove();
        }else{
            $('#NotifikasiEditKonten').html(`
                <div class="alert alert-warning text-center">
                    <small>Minimal harus ada satu item list!</small>
                </div>
            `);
        }

    });

    //Proses Edit Konten
    $('#ProsesEditKonten').submit(function(e){
        e.preventDefault();
        
        // Ambil Data Dari form
        var ProsesEditKonten = $(this).serialize();

        //Loading Notifikasi
        $('#NotifikasiEditKonten').html('<small class="text-muted">Menyimpan data...</small>');

        // Ajax Request
        $.ajax({
            type     : 'POST',
            url      : '_Page/Dokumentasi/ProsesEditKonten.php',
            dataType : 'json',
            data     : ProsesEditKonten,

            success: function(response){

                var status  = response.status;
                var message = response.message;
                var id = response.id;

                if(status === 'success'){

                    // Bersihkan notifikasi
                    $('#NotifikasiEditKonten').html('');

                    // Tutup modal jika ada
                    $('#ModalEditKonten').modal('hide');

                    // Reload detail pemeriksaan
                    ShowData();
                    ShowDetail(id);

                    // Toast Proses Berhasil
                    $('#put_message').html('<i class="bi bi-check-circle me-2"></i> ' + message);

                    // Tampilkan Toast
                    var toastEl = document.getElementById('toast_proses');
                    var toast   = new bootstrap.Toast(toastEl, {delay: 3000});
                    toast.show();

                } else {
                    // Tampilkan Pesan Kesalahan
                    $('#NotifikasiEditKonten').html(
                        '<div class="alert alert-danger"><small>'+message+'</small></div>'
                    );
                }
            },

            error: function(xhr){
                console.log(xhr.responseText);

                $('#NotifikasiEditKonten').html(
                    '<div class="alert alert-danger"><small>Terjadi kesalahan sistem</small></div>'
                );
            }
        });
    });

});