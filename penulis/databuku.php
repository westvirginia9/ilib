<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'penulis') {
    header('Location: ../login.php');
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

// Ambil jumlah total buku yang dimiliki penulis
$sql = "SELECT COUNT(*) AS total_books FROM books WHERE author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();
$total_books = $result->fetch_assoc()['total_books'];

// Ambil data buku yang disewa dengan pembayaran sukses
$sql = "SELECT b.title, b.genre, b.id, r.rental_date, u.name AS renter, r.payment_status, r.status
        FROM books b
        LEFT JOIN rentals r ON b.id = r.book_id
        LEFT JOIN users u ON r.reader_id = u.id
        WHERE b.author_id = ? AND r.payment_status = 'paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    if ($row['payment_status'] == 'paid') {
        $row['status'] = 'aktif';
    }
    $books[] = $row;
}

// Ambil jumlah pembaca aktif yang menyewa buku penulis yang login
$sql = "SELECT COUNT(DISTINCT r.reader_id) AS active_readers 
        FROM rentals r 
        JOIN books b ON r.book_id = b.id 
        WHERE b.author_id = ? AND r.payment_status = 'paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();
$active_readers = $result->fetch_assoc()['active_readers'];

$conn->close();

// Menghitung jumlah buku aktif
$active_books_count = 0;
foreach ($books as $book) {
    if ($book['status'] == 'aktif') {
        $active_books_count++;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../csspengguna/databuku.css">
  <title>Data Buku</title>
  <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .navbar {
        display: flex;
        background-color: #fff;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .navbar .gambar {
        width: 40px;
        height: 40px;
        margin-right: 20px;
    }
    .navbar ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
    }
    .navbar ul li {
        margin-right: 20px;
    }
    .navbar ul li a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
    }
    .navbar ul li a:hover {
        color: #007bff;
    }
    .content {
        padding: 20px;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .header #dashboard-title {
        font-size: 24px;
        font-weight: bold;
    }
    .search-profile {
        display: flex;
        align-items: center;
    }
    .search-profile input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-right: 10px;
    }
    .search-profile .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .data-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .data-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        flex: 1;
        margin-right: 20px;
    }
    .data-box:last-child {
        margin-right: 0;
    }
    .data-box h2 {
        margin: 0;
        font-size: 18px;
        color: #666;
    }
    .data-box p {
        font-size: 24px;
        font-weight: bold;
        margin: 10px 0 0;
    }
    .data-container2 {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .status {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }
    .book-table {
        width: 100%;
        border-collapse: collapse;
    }
    .book-table th, .book-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    .book-table th {
        background-color: #f9f9f9;
    }
    .book-table td:nth-child(6) {
        text-align: center;
    }
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        color: #fff;
        font-size: 12px;
        font-weight: bold;
    }
    .status-badge.aktif {
        background-color: #28a745;
    }
    .status-badge.kadaluarsa {
        background-color: #dc3545;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
        <li><a href="dashbord.php">Dashboard</a></li>
        <li><a href="buku.php">Buku</a></li>
        <li><a href="databuku.php" class="active">Data Buku</a></li>
        <li><a href="biaya.php">Biaya</a></li>
      </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1 id="dashboard-title">Data Buku</h1>
      <div class="search-profile">
        <input type="text" placeholder="Cari...">
        <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
      </div>
    </div>
    <!-- Konten utama halaman disini -->
    <div class="data-container">
      <div class="data-box">
        <h2>Total Buku</h2>
        <p><?php echo $total_books; ?></p>
        <p>15% bulan ini</p>
      </div>
      <div class="data-box">
        <h2>Buku Aktif</h2>
        <p><?php echo $active_books_count; ?></p>
        <p>1% bulan ini</p>
      </div>
      <div class="data-box">
        <h2>Pembaca Aktif</h2>
        <p><?php echo $active_readers; ?></p>
        <p>Statistik pembaca</p>
      </div>
    </div>
    <div class="data-container2">
      <div class="status">Status <span class="penulis-aktif">Penulis Aktif</span></div>
      <table class="book-table">
        <tr>
          <th>Nama Buku</th>
          <th>Jenis Buku</th>
          <th>ID Buku</th>
          <th>Masa Buku</th>
          <th>Penyewa</th>
          <th>Status</th>
        </tr>
        <?php foreach ($books as $book): ?>
        <tr>
          <td><?php echo htmlspecialchars($book['title']); ?></td>
          <td><?php echo htmlspecialchars($book['genre']); ?></td>
          <td><?php echo htmlspecialchars($book['id']); ?></td>
          <td><?php echo htmlspecialchars($book['rental_date']); ?></td>
          <td><?php echo htmlspecialchars($book['renter']); ?></td>
          <td>
            <span class="status-badge <?php echo $book['status'] == 'aktif' ? 'aktif' : 'kadaluarsa'; ?>">
              <?php echo $book['status'] == 'aktif' ? 'Aktif' : 'Kadaluarsa'; ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>

