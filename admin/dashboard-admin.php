<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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

$sql = "SELECT COUNT(*) AS count, role FROM users GROUP BY role";
$result = $conn->query($sql);
$usersData = [];
while ($row = $result->fetch_assoc()) {
    $usersData[] = $row;
}

$sql = "SELECT date, SUM(income) as total_income FROM daily_income GROUP BY date";
$result = $conn->query($sql);
$incomeData = [];
while ($row = $result->fetch_assoc()) {
    $incomeData[] = $row;
}

$sql = "SELECT COUNT(*) AS count, contest_id FROM contest_participants GROUP BY contest_id";
$result = $conn->query($sql);
$contestData = [];
while ($row = $result->fetch_assoc()) {
    $contestData[] = $row;
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
  <title>Dashboard</title>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
        <li><a href="dashbord.php">Dashboard</a></li>
        <li><a href="kontes.php">Kontes</a></li>
        <li><a href="admin_chat.php">Chatbot</a></li>
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
    <!-- Konten utama halaman disini -->
    <div class="container">
        <div class="data-container2">
            <div class="status">Jumlah Pengguna</div>
            <div class="horizontal-container">
              <canvas id="donutChart"></canvas>
            </div>
          </div>
          
          
        <div class="data-container3">
            <canvas id="barChart"></canvas>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="data-container2">
            <div class="status">Pemasukan</div>
            <div class="horizontal-container">
              <canvas id="barChart12"></canvas>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="data-container2">
            <div class="status">Peserta kontes</div>
            <canvas id="barChart113"></canvas>
            </div>
        </div>
    </div>
    
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk jumlah pengguna
        var usersData = <?php echo json_encode($usersData); ?>;
        var userLabels = usersData.map(function(data) { return data.role; });
        var userCounts = usersData.map(function(data) { return data.count; });

        var ctxDonut = document.getElementById('donutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: userLabels,
                datasets: [{
                    data: userCounts,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Data untuk pemasukan
        var incomeData = <?php echo json_encode($incomeData); ?>;
        var incomeLabels = incomeData.map(function(data) { return data.date; });
        var incomeTotals = incomeData.map(function(data) { return data.total_income; });

        var ctxBar = document.getElementById('barChart12').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: incomeLabels,
                datasets: [{
                    label: 'Pemasukan',
                    data: incomeTotals,
                    backgroundColor: '#36A2EB'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            }
        });

        // Data untuk peserta kontes
        var contestData = <?php echo json_encode($contestData); ?>;
        var contestLabels = contestData.map(function(data) { return 'Kontes ' + data.contest_id; });
        var contestCounts = contestData.map(function(data) { return data.count; });

        var ctxBarContest = document.getElementById('barChart113').getContext('2d');
        new Chart(ctxBarContest, {
            type: 'bar',
            data: {
                labels: contestLabels,
                datasets: [{
                    label: 'Peserta Kontes',
                    data: contestCounts,
                    backgroundColor: '#FF6384'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            }
        });
    });
  </script>
</body>
</html>
