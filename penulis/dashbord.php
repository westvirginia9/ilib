<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'penulis') {
    header('Location: ../index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilib3";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$author_id = $_SESSION['user_id'];

// Ambil data pembaca per buku
$bookData = [];
$sql = "SELECT b.title, b.reader_count FROM books b WHERE b.author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookData[] = $row;
    }
}

// Ambil data untuk pie chart (misalnya, data genre)
$pieData = [];
$sql = "SELECT genre, COUNT(*) AS count FROM books WHERE author_id = ? GROUP BY genre";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pieData[] = $row;
    }
}

// Ambil data untuk bar chart (misalnya, data jumlah pembaca)
$barData = $bookData; // Data pembaca per buku sudah diambil sebelumnya

// Ambil data pendapatan
$incomeData = [];
$sql = "SELECT b.title, SUM(r.amount) AS total_income FROM books b 
        JOIN rentals r ON b.id = r.book_id 
        WHERE b.author_id = ? AND r.payment_status = 'paid' 
        GROUP BY b.title";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $incomeData[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../csspengguna/dashbord.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Dashboard Penulis</title>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
        <li><a href="dashbord.php">Dashboard</a></li>
        <li><a href="buku.php">Buku</a></li>
        <li><a href="databuku.php">Data Buku</a></li>
        <li><a href="biaya.php">Biaya</a></li>
        <li><a href="chatbot.php">chatbot</a></li>
      </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1 id="dashboard-title">Dashboard</h1>
      <div class="search-profile">
        <input type="text" placeholder="Search...">
        <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
      </div>
    </div>
    
    <div class="container">
        <div class="data-container2">
            <div class="status">Jumlah pembaca</div>
            <div class="horizontal-container">
              <?php foreach ($bookData as $book): ?>
              <div class="horizontal-item">
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p>Data pembaca: <?php echo $book['reader_count']; ?></p>
                
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          
        <div class="data-container3">
            <div class="status">Data pencarian</div>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="data-container2">
            <div class="status">Jumlah Pembaca</div>
            <canvas id="barChart"></canvas>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="data-container2">
            <div class="status">Pendapatan</div>
            <canvas id="incomeChart"></canvas>
        </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.horizontal-item');
        items.forEach(function(item) {
            const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16);
            item.style.backgroundColor = randomColor;
        });

        // Data untuk pie chart
        const pieData = {
            labels: <?php echo json_encode(array_column($pieData, 'genre')); ?>,
            datasets: [{
                label: 'Data Pencarian',
                data: <?php echo json_encode(array_column($pieData, 'count')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        };

        const pieOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2) + '%';
                        }
                    }
                }
            }
        };

        const pieChart = document.getElementById('pieChart').getContext('2d');
        new Chart(pieChart, {
            type: 'pie',
            data: pieData,
            options: pieOptions
        });

        // Data untuk bar chart
        const barData = {
            labels: <?php echo json_encode(array_column($barData, 'title')); ?>,
            datasets: [{
                label: 'Jumlah Pembaca',
                data: <?php echo json_encode(array_column($barData, 'reader_count')); ?>,
                backgroundColor: [
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 159, 64, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        const barOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                        }
                    }
                }
            }
        };

        const barChart = document.getElementById('barChart').getContext('2d');
        new Chart(barChart, {
            type: 'bar',
            data: barData,
            options: barOptions
        });

        // Data untuk income chart
        const incomeData = {
            labels: <?php echo json_encode(array_column($incomeData, 'title')); ?>,
            datasets: [{
                label: 'Pendapatan',
                data: <?php echo json_encode(array_column($incomeData, 'total_income')); ?>,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        const incomeOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                        }
                    }
                }
            }
        };

        const incomeChart = document.getElementById('incomeChart').getContext('2d');
        new Chart(incomeChart, {
            type: 'bar',
            data: incomeData,
            options: incomeOptions
        });
    });
  </script>
</body>
</html>
