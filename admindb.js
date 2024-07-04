document.addEventListener('DOMContentLoaded', function() {
    fetch('data.php')
        .then(response => response.json())
        .then(data => {
            // Donut Chart
            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Pengguna Aktif'],
                    datasets: [{
                        data: [data.jumlah_pengguna],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    }]
                }
            });

            // Bar Chart for Pemasukan
            new Chart(document.getElementById('barChart12'), {
                type: 'bar',
                data: {
                    labels: ['Pemasukan'],
                    datasets: [{
                        data: [data.pemasukan],
                        backgroundColor: ['#36A2EB'],
                    }]
                }
            });

            // Bar Chart for Peserta Kontes
            new Chart(document.getElementById('barChart113'), {
                type: 'bar',
                data: {
                    labels: ['Peserta Kontes'],
                    datasets: [{
                        data: [data.peserta_kontes],
                        backgroundColor: ['#FF6384'],
                    }]
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
});
