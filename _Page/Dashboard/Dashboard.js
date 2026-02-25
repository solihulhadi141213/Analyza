var chartPelayananInstance = null;
var chartRawDataset = [];
var chartMode = 'tahun'; // default: Januari - Desember tahun ini

function getAjaxErrorMessage(xhr, fallbackMessage) {
    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
        return xhr.responseJSON.message;
    }

    if (xhr && xhr.responseText) {
        try {
            var parsed = JSON.parse(xhr.responseText);
            if (parsed && parsed.message) {
                return parsed.message;
            }
        } catch (e) {
            // Abaikan parse error dan gunakan fallback
        }
    }

    if (typeof xhr === 'string' && xhr.length > 0) {
        return xhr;
    }

    return fallbackMessage;
}

function rejectPromise(message) {
    return $.Deferred().reject({ responseJSON: { message: message } }).promise();
}

function getMonthNameId(monthNumber) {
    var names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return names[monthNumber - 1] || '';
}

function aggregateYearCurrent(dataset) {
    var now = new Date();
    var year = now.getFullYear();
    var categories = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var values = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    dataset.forEach(function (item) {
        if (!item || !item.datetime) {
            return;
        }

        var parts = String(item.datetime).split('-');
        if (parts.length < 3) {
            return;
        }

        var y = Number(parts[0]);
        var m = Number(parts[1]);
        if (y === year && m >= 1 && m <= 12) {
            values[m - 1] += Number(item.y) || 0;
        }
    });

    return {
        title: 'Grafik Pelayanan Periode  ' + year,
        categories: categories,
        values: values
    };
}

function aggregateCurrentMonth(dataset) {
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth() + 1;
    var lastDay = new Date(year, month, 0).getDate();

    var categories = [];
    var values = [];
    var map = {};

    dataset.forEach(function (item) {
        if (!item || !item.datetime) {
            return;
        }

        var parts = String(item.datetime).split('-');
        if (parts.length < 3) {
            return;
        }

        var y = Number(parts[0]);
        var m = Number(parts[1]);
        var d = Number(parts[2]);

        if (y === year && m === month && d >= 1 && d <= lastDay) {
            map[d] = (map[d] || 0) + (Number(item.y) || 0);
        }
    });

    for (var day = 1; day <= lastDay; day += 1) {
        categories.push(String(day));
        values.push(map[day] || 0);
    }

    return {
        title: 'Grafik Pelayanan Periode Harian ' + getMonthNameId(month) + ' ' + year,
        categories: categories,
        values: values
    };
}

function renderChartPelayanan(payload) {
    if (typeof ApexCharts === 'undefined') {
        return rejectPromise('Pustaka ApexCharts tidak ditemukan.');
    }

    $('#chart_pelayanan').html('<div id="chart_pelayanan_apex"></div>');

    if (chartPelayananInstance) {
        chartPelayananInstance.destroy();
        chartPelayananInstance = null;
    }

    var options = {
        series: [{
            name: 'Jumlah Pelayanan',
            data: payload.values
        }],
        chart: {
            type: 'bar',
            height: 450,
            toolbar: {
                show: false
            }
        },
        title: {
            text: payload.title,
            align: 'left'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '55%'
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: payload.categories,
            labels: {
                rotate: -45
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            title: {
                text: 'Jumlah'
            }
        },
        noData: {
            text: 'Belum ada data pelayanan.'
        },
        colors: ['#0d6efd']
    };

    chartPelayananInstance = new ApexCharts(document.querySelector('#chart_pelayanan_apex'), options);
    return chartPelayananInstance.render();
}

function showChartByMode() {
    var payload = (chartMode === 'bulan')
        ? aggregateCurrentMonth(chartRawDataset)
        : aggregateYearCurrent(chartRawDataset);

    return $.when(renderChartPelayanan(payload));
}

// Fungsi untuk update data rekap ke JumlahPelayanan.json
function UpdateCountDashboard() {
    return $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/UpdateCountDashboard.php',
        dataType: 'json'
    });
}

// Fungsi untuk mengambil dataset lalu tampilkan chart sesuai mode aktif
function ShowChartPelayanan() {
    return $.ajax({
        type: 'GET',
        url: '_Page/Dashboard/JumlahPelayanan.json',
        dataType: 'json',
        cache: false
    }).then(function (response) {
        chartRawDataset = (response && Array.isArray(response.dataset)) ? response.dataset : [];
        return showChartByMode();
    });
}

function initChartControls() {
    $(document).on('click', '#ChartBulanini', function () {
        chartMode = 'bulan';
        showChartByMode().fail(function (xhr) {
            Swal.fire('Opps!', getAjaxErrorMessage(xhr, 'Gagal menampilkan grafik pelayanan bulanan.'), 'error');
        });
    });

    $(document).on('click', '#ChartTahunIni', function () {
        chartMode = 'tahun';
        showChartByMode().fail(function (xhr) {
            Swal.fire('Opps!', getAjaxErrorMessage(xhr, 'Gagal menampilkan grafik pelayanan tahunan.'), 'error');
        });
    });

    $(document).on('click', '#ReloadChart', function () {
        UpdateCountDashboard()
            .done(function (response) {
                if (response && response.status === 'Success') {
                    ShowChartPelayanan().fail(function (xhr) {
                        Swal.fire('Opps!', getAjaxErrorMessage(xhr, 'Gagal memuat ulang grafik pelayanan.'), 'error');
                    });
                } else {
                    var message = (response && response.message) ? response.message : 'Gagal update data dashboard.';
                    Swal.fire('Opps!', message, 'error');
                }
            })
            .fail(function (xhr) {
                Swal.fire('Opps!', getAjaxErrorMessage(xhr, 'Terjadi kesalahan saat update data dashboard.'), 'error');
            });
    });
}

function ShowCountPelayanan() {
    $.ajax({
        type    : 'POST',
        url     : '_Page/Dashboard/CountPelayanan.php',
        dataType: 'json',
        success : function(response){
            $('#count_diminta').html(response.diminta);
            $('#count_ditolak').html(response.ditolak);
            $('#count_diterima').html(response.diterima);
            $('#count_selesai').html(response.selesai);
        }
    });
}
function ShowTableLayanan() {
    $('#TabelIndikatorLayanan').html('<tr><td colspan="4" class="text-center">LOADING...</td></tr>');
    $.ajax({
        type    : 'POST',
        url     : '_Page/Dashboard/TabelIndikatorLayanan.php',
        success : function(response){
            $('#TabelIndikatorLayanan').html(response);
        }
    });
}

// Inisialisasi Halaman
$(document).ready(function () {
    ShowCountPelayanan();
    initChartControls();
    ShowTableLayanan();

    // Default mode saat pertama buka halaman: Tahun Ini (Januari - Desember)
    chartMode = 'tahun';

    UpdateCountDashboard()
        .done(function (response) {
            if (response && response.status === 'Success') {
                ShowChartPelayanan().fail(function (xhr) {
                    Swal.fire('Opps!', getAjaxErrorMessage(xhr, 'Gagal menampilkan grafik pelayanan.'), 'error');
                });
            } else {
                var message = (response && response.message) ? response.message : 'Gagal update data dashboard.';
                Swal.fire('Opps!', message, 'error');
            }
        })
        .fail(function (xhr) {
            Swal.fire('Opps!', getAjaxErrorMessage(xhr, 'Terjadi kesalahan saat update data dashboard.'), 'error');
        });
});
