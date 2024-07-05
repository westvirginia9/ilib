<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembaca') {
    header('Location: ../index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilib3";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Jumlah buku yang disewa
$sql = "SELECT COUNT(*) AS total_books FROM rentals WHERE reader_id = ? AND payment_status = 'paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookResult = $stmt->get_result();
$bookData = $bookResult->fetch_assoc();
$total_books = $bookData['total_books'];
$stmt->close();

// Jenis buku yang disewa
$sql = "SELECT b.genre, COUNT(*) AS count FROM rentals r JOIN books b ON r.book_id = b.id WHERE r.reader_id = ? AND r.payment_status = 'paid' GROUP BY b.genre";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$genreResult = $stmt->get_result();
$genreData = [];
while ($row = $genreResult->fetch_assoc()) {
    $genreData[] = $row;
}
$stmt->close();

// Total biaya sewa
$sql = "SELECT SUM(r.amount) AS total_cost FROM rentals r WHERE r.reader_id = ? AND r.payment_status = 'paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$costResult = $stmt->get_result();
$costData = $costResult->fetch_assoc();
$total_cost = $costData['total_cost'];
$stmt->close();

// Jumlah kontes yang diikuti
$sql = "SELECT COUNT(*) AS total_contests FROM contest_participants WHERE reader_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$contestResult = $stmt->get_result();
$contestData = $contestResult->fetch_assoc();
$total_contests = $contestData['total_contests'];
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../csspengguna/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Pembaca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            width: 200px;
            background-color: #ffffff;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            box-sizing: border-box;
        }
        .navbar img {
            display: block;
            width: 80px; 
            height: auto;
            margin: 20px auto;
            margin-top: 30px;
        }
        .navbar ul {
            list-style-type: none;
            padding: 0;
            margin-top: 80px;
        }
        .navbar ul li {
            padding: 15px;
            text-align: center;
        }
        .navbar ul li a {
            color: #979797;
            text-decoration: none;
            display: block;
        }
        .navbar ul li a:hover {
            background-color: #575757;
        }
        .content {
            margin-left: 200px;
            padding: 20px;
            box-sizing: border-box;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            margin-bottom: 20px;
        }
        .chart-container {
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img class="gambar" src="../gambar/image.png" alt="Logo">
        <ul>
            <li><a href="reader_dashboard.php">Dashboard</a></li>
            <li><a class="active" href="rental_and_contest.php">Buku & Kontes</a></li>
            <li><a class="active" href="buku.php">Home</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="section">
            <h2>Statistik Buku</h2>
            <div class="chart-container">
                <canvas id="bookChart"></canvas>
            </div>
        </div>
        <div class="section">
            <h2>Statistik Genre Buku</h2>
            <div class="chart-container">
                <canvas id="genreChart"></canvas>
            </div>
        </div>
        <div class="section">
            <h2>Statistik Biaya Sewa</h2>
            <div class="chart-container">
                <canvas id="costChart"></canvas>
            </div>
        </div>
        <div class="section">
            <h2>Statistik Kontes</h2>
            <div class="chart-container">
                <canvas id="contestChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var bookChartCtx = document.getElementById('bookChart').getContext('2d');
            var genreChartCtx = document.getElementById('genreChart').getContext('2d');
            var costChartCtx = document.getElementById('costChart').getContext('2d');
            var contestChartCtx = document.getElementById('contestChart').getContext('2d');

            var bookChart = new Chart(bookChartCtx, {
                type: 'bar',
                data: {
                    labels: ['Jumlah Buku yang Disewa'],
                    datasets: [{
                        label: 'Jumlah Buku',
                        data: [<?= $total_books ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            var genreLabels = <?= json_encode(array_column($genreData, 'genre')) ?>;
            var genreCounts = <?= json_encode(array_column($genreData, 'count')) ?>;
            
            var genreChart = new Chart(genreChartCtx, {
                type: 'pie',
                data: {
                    labels: genreLabels,
                    datasets: [{
                        label: 'Jenis Buku',
                        data: genreCounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                }
            });

            var costChart = new Chart(costChartCtx, {
                type: 'bar',
                data: {
                    labels: ['Total Biaya Sewa'],
                    datasets: [{
                        label: 'Biaya Sewa (Rp)',
                        data: [<?= $total_cost ?>],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            var contestChart = new Chart(contestChartCtx, {
                type: 'bar',
                data: {
                    labels: ['Jumlah Kontes yang Diikuti'],
                    datasets: [{
                        label: 'Jumlah Kontes',
                        data: [<?= $total_contests ?>],
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
