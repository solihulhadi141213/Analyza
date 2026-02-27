function ReloadBelNotification() {
    $('#MenampilkanBelNotifikasi').load('_Partial/ReloadBelNotification.php');
}

function SelectDokter2Global() {

    let el = $('#nama_dokter_penerima');

    // Hindari double init
    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        theme             : "bootstrap-5",
        dropdownParent    : $('#FormTerimaPermintaanPemeriksaan'),
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
                    results: (response && response.results) ? response.results : [],
                    pagination: { more: (response && response.more) ? response.more : false }
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

function ToggleFormTerimaPermintaanFieldsGlobal() {
    let status = $('#FormTerimaPermintaanPemeriksaan #status').val();

    let showDiterima = (status === 'Diterima');
    let showAlasan = (status === 'Ditolak' || status === 'Dibatalkan');

    $('#FormTerimaPermintaanPemeriksaan #wrap_datetime_diterima').toggle(showDiterima);
    $('#FormTerimaPermintaanPemeriksaan #wrap_dokter_penerima').toggle(showDiterima);
    $('#FormTerimaPermintaanPemeriksaan #wrap_alasan_penolakan').toggle(showAlasan);

    $('#FormTerimaPermintaanPemeriksaan [name="tanggal_diterima"]').prop('required', showDiterima);
    $('#FormTerimaPermintaanPemeriksaan [name="jam_diterima"]').prop('required', showDiterima);
    $('#FormTerimaPermintaanPemeriksaan [name="nama_dokter_penerima"]').prop('required', showDiterima);
    $('#FormTerimaPermintaanPemeriksaan #alasan').prop('required', showAlasan);

    if (!showAlasan) {
        $('#FormTerimaPermintaanPemeriksaan #alasan').val('');
    }
    if (!showDiterima) {
        $('#FormTerimaPermintaanPemeriksaan #nama_dokter_penerima').val(null).trigger('change');
        $('#FormTerimaPermintaanPemeriksaan #kode_dokter_penerima').val('');
        $('#FormTerimaPermintaanPemeriksaan #ihs_dokter_penerima').val('');
    }
}


//Reload Notification
$(document).ready(function() {
    //Notification First Time
    $('#MenampilkanBelNotifikasi').load('_Partial/ReloadBelNotification.php');

    // setInterval(ReloadBelNotification, 5000);
    setInterval(ReloadBelNotification, 5000);

    //Kondisi Ketika Uraian Notifikasi Di Klik
    $('#MenampilkanBelNotifikasi').click(function(){
        $('#MenampilkanNotificationList').html('<li class="dropdown-header">Loading...</li>');
        $('#MenampilkanNotificationList').load('_Partial/NotificationList.php');
    });

    $(document).on('click', '.modal_terima_permintaan_pemeriksaan', function () {

        //tangkap data 'id_laboratorium' dan buat variabel
        var id_laboratorium   = $(this).data('id');

        //tampilkan modal
        $('#ModalTerimaPermintaanPemeriksaan').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTerimaPermintaanPemeriksaan').html('');

        //Form Loading
        $('#FormTerimaPermintaanPemeriksaan').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pemeriksaan/FormTerimaPermintaan.php',
            data        : {id_laboratorium: id_laboratorium},
            success     : function(data){
                $('#FormTerimaPermintaanPemeriksaan').html(data);
                SelectDokter2Global();
                ToggleFormTerimaPermintaanFieldsGlobal();
            }
        });
    });
    $(document).on('change', '#FormTerimaPermintaanPemeriksaan #status', function () {
        ToggleFormTerimaPermintaanFieldsGlobal();
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

    /* Ketika 'ProsesTerimaPermintaanPemeriksaan' disubmit */
    $('#ProsesTerimaPermintaanPemeriksaan').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTerimaPermintaanPemeriksaan=$('#ProsesTerimaPermintaanPemeriksaan').serialize();

        /* Loading Notification */
        $('#NotifikasiTerimaPermintaanPemeriksaan').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pemeriksaan/ProsesTerimaPermintaan.php',
            dataType: 'json',
            data    : ProsesTerimaPermintaanPemeriksaan,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTerimaPermintaanPemeriksaan').html('');

                    //Tutup modal
                    $('#ModalTerimaPermintaanPemeriksaan').modal('hide');

                    //reload tabel
                    ReloadBelNotification();

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
                    $('#NotifikasiTerimaPermintaanPemeriksaan').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });
});
