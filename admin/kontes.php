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

$sql = "SELECT * FROM contests";
$result = $conn->query($sql);

$contests = [];
while ($row = $result->fetch_assoc()) {
    $contests[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../csspengguna/buku.css">
  <script src="../js/script.js" defer></script>
  <title>Kontes</title>
  <style>
    .modal {
      display: none; 
      position: fixed; 
      z-index: 1; 
      left: 0;
      top: 0;
      width: 100%; 
      height: 100%; 
      overflow: auto; 
      background-color: rgb(0,0,0); 
      background-color: rgba(0,0,0,0.4); 
      padding-top: 60px;
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    .kontes-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 10px;
      margin: 10px;
      text-align: center;
    }

    .kontes-card img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }

    .kontes-card h3 {
      margin: 10px 0;
    }

    .join-button {
      background-color: #5415BC;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
      <li><a href="dashboard-admin.php">Dashboard</a></li>
      <li><a href="kontes.html">Kontes</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1 id="dashboard-title">Kontes</h1>
      <div class="search-profile">
        <input type="text" placeholder="Search...">
        <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
      </div>
    </div>
    <!-- Konten utama halaman disini -->
    <div class="book-entry">
      <div class="book-grid">
        <div class="book-slot" onclick="openModal()">
          <img class="plus" src="../gambar/v878-mind-50.jpg" alt="">
          <p>Tambahkan kontes</p>
        </div>
        <!-- Kontes lainnya akan ditambahkan di sini -->
        <?php foreach ($contests as $contest): ?>
        <div class="book-slot">
          <img src="../uploads/<?= htmlspecialchars($contest['contest_image']) ?>" alt="Kontes Image">
          <p><?= htmlspecialchars($contest['contest_name']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Modal untuk menambahkan kontes -->
  <div id="addContestModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Tambah Kontes Baru</h2>
      <form action="add_contest.php" method="post">
        <label for="contest_name">Nama Kontes:</label><br>
        <input type="text" id="contest_name" name="contest_name" required><br><br>
        <label for="start_date">Tanggal Mulai:</label><br>
        <input type="date" id="start_date" name="start_date" required><br><br>
        <label for="deadline">Tanggal Berakhir:</label><br>
        <input type="date" id="deadline" name="deadline" required><br><br>
        <label for="contest_image">URL Gambar:</label><br>
        <input type="text" id="contest_image" name="contest_image" required><br><br>
        <input type="submit" value="Tambah Kontes">
      </form>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('addContestModal').style.display = "block";
    }

    function closeModal() {
      document.getElementById('addContestModal').style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == document.getElementById('addContestModal')) {
        closeModal();
      }
    }
  </script>
</body>
</html>
