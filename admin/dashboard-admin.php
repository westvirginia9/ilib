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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" type="text/css" href="../csspengguna/dashbord.css"> -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Dashboard</title>
  <style>
    body {
        display: flex;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #D9D9D9;
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
        flex-grow: 1;
        box-sizing: border-box;
        margin-top: -25px;
    }
    
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    #dashboard-title {
        margin: 0;
        padding: 20px 0;
    }
    
    .search-profile {
        display: flex;
        align-items: center;
    }
    
    .search-profile input[type="text"] {
        padding: 5px;
        margin-right: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    
    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    
    .data-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        margin-top: 2px;
    }
    
    .data-overview {
        display: flex;
        justify-content: space-around;
    }
    
    .data-box {
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        flex-grow: 1;
        margin: 0 10px;
    }
    
    .data-box h2 {
        margin: 0;
        font-size: 15px;
        color: #ACACAC;
    }
    
    .data-box p {
        font-size: 2em;
        margin: 10px 0 0;
    }
    
    /* 2 */
    .container {
        display: flex;
    }
    
    .data-container2 {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        position: relative;
        width: 80%;
        box-sizing: border-box;
    }
    
    .data-container3 {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        position: relative;
        width: 40%;
        box-sizing: border-box;
        margin-left: 10px;
    }
    
    .data-container2 .status, .data-container3 .status {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 14px;
        color: #333;
    }
    
    .horizontal-container {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }
    
    .horizontal-item {
        padding: 20px;
        border-radius: 8px;
        width: 30%;
        box-sizing: border-box;
        text-align: center;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <img src="../gambar/image.png" alt="Logo">
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
        <img src="../gambar/22965342.jpg" alt="Profile Picture" class="profile-pic">
      </div>
    </div>
    <div class="container">
        <div class="data-container2">
            <div class="status">Jumlah Pengguna</div>
            <div class="horizontal-container">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="data-container2">
            <div class="status">Pemasukan Harian</div>
            <div class="horizontal-container">
                <canvas id="barChart12"></canvas>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="data-container2">
            <div class="status">Peserta Kontes</div>
            <div class="horizontal-container">
                <canvas id="barChart113"></canvas>
            </div>
        </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
