let chart = null;

// Render Chart Function
function loadChart(jsonUrl, titleText) {
    $.getJSON(jsonUrl, function (data) {

        const categories = data.map(item => item.x);
        const seriesData = data.map(item => parseInt(item.y));

        const options = {
            chart: {
                type: 'area',
                height: 400,
                toolbar: { show: false }
            },
            series: [{
                name: 'Jumlah Pelayanan',
                data: seriesData
            }],
            xaxis: {
                categories: categories
            },
            yaxis: {
                labels: {
                    formatter: value => Math.round(value)
                }
            },
            tooltip: {
                y: {
                    formatter: value => Math.round(value) + ' Pelayanan'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            title: {
                text: titleText,
                align: 'center'
            }
        };

        // Destroy chart lama jika ada
        if (chart !== null) {
            chart.destroy();
        }

        chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    });
}

function loadChartByService() {
    $.getJSON("_Page/Dashboard/DashboardLayanan.json", function (data) {

        // Ambil object pertama
        const raw = data[0];

        // Convert object ke array
        let chartData = Object.keys(raw).map(key => ({
            name: key,
            value: parseInt(raw[key])
        }));

        // Sort DESC (terbesar ke terkecil)
        chartData.sort((a, b) => b.value - a.value);

        // Split ke categories & series
        const categories = chartData.map(item => item.name);
        const values = chartData.map(item => item.value);

        // Chart Options
        var options = {
            chart: {
                type: 'bar',
                height: 320,
                horizontal: true,
                toolbar: { show: false }
            },
            series: [{
                name: 'Jumlah Pemeriksaan',
                data: values
            }],
            xaxis: {
                categories: categories
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    horizontal: true
                }
            },
            dataLabels: {
                enabled: false,
                formatter: val => val,
                style: {
                    fontSize: '12px'
                }
            },
            tooltip: {
                y: {
                    formatter: val => val + ' Pemeriksaan'
                }
            }
        };

        var chart = new ApexCharts(
            document.querySelector(".chart_by_service"),
            options
        );

        chart.render();
    });
}

// Fungsi untuk menampilkan dashboard
function ShowDashboard() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/CountDashboard.php',
        dataType: 'json',
        success: function(data) {
            $('#put_pengguna').hide().html(data.user).fadeIn('slow');
            $('#put_siswa_aktif').hide().html(data.siswa).fadeIn('slow');
            $('#put_periode_akademik').hide().html(data.periode).fadeIn('slow');
            $('#put_pembayaran').hide().html(data.pembayaran).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

// Fungsi menampilkan jam digital
function tampilkanJam() {
    const waktu = new Date();
    let jam = waktu.getHours().toString().padStart(2, '0');
    let menit = waktu.getMinutes().toString().padStart(2, '0');

    $('#jam_menarik').text(`${jam}:${menit} WIB`);
}

// Fungsi menampilkan tanggal
function tampilkanTanggal() {
    const waktu = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const tanggal = waktu.toLocaleDateString('id-ID', options);

    $('#tanggal_menarik').text(tanggal);
}



$(document).ready(function () {
    // DEFAULT: Chart Bulan Ini
    loadChart(
        "_Page/Dashboard/DashboardBulanan.json",
        "Grafik Pelayanan Bulan Ini"
    );

    // Klik Bulan Ini
    $("#ChartBulan").click(function () {
        loadChart(
            "_Page/Dashboard/DashboardBulanan.json",
            "Grafik Pelayanan Bulan Ini"
        );
    });

    // Klik Tahun Ini
    $("#ChartTahun").click(function () {
        loadChart(
            "_Page/Dashboard/DashboardTahunan.json",
            "Grafik Pelayanan Tahun " + new Date().getFullYear()
        );
    });

    loadChartByService();

    // Jalankan pertama kali saat halaman load
    tampilkanJam();
    tampilkanTanggal();

    // Update jam setiap 1 menit (60000 ms)
    setInterval(function () {
        tampilkanJam();
        tampilkanTanggal();
    }, 60000);
});