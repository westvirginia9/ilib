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
$sql = "SELECT * FROM books WHERE author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $author_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
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
  <title>Buku</title>
  <style>
    /* Tambahkan CSS untuk menata form dan daftar buku */
    .form-container {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
      padding: 20px;
      border: 1px solid #ccc;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }
    .form-container input,
    .form-container select {
      display: block;
      width: 100%;
      margin-bottom: 10px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    .form-container button {
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }
    .form-container button:hover {
      background-color: #0056b3;
    }
    .book-grid {
      display: flex;
      flex-wrap: wrap;
    }
    .book-slot {
      width: 200px;
      margin: 10px;
      text-align: center;
    }
    .book-slot img {
      width: 100%;
      height: auto;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
      <li><a href="dashbord.php">Dashboard</a></li>
      <li><a href="buku.php">Buku</a></li>
      <li><a href="databuku.php">Data Buku</a></li>
      <li><a href="biaya.php">Biaya</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1 id="dashboard-title">Buku</h1>
      <div class="search-profile">
        <input type="text" placeholder="Search...">
        <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
      </div>
    </div>
    <!-- Konten utama halaman disini -->
    <div class="book-entry">
      <div class="book-grid">
        <?php foreach ($books as $book): ?>
        <div class="book-slot">
          <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="">
          
        </div>
        <?php endforeach; ?>
        <div class="book-slot" onclick="showForm()">
          <img class="plus" src="../gambar/v878-mind-50.jpg" alt="">
          <p>Tambahkan Karya</p>
        </div>
      </div>
    </div>
  </div>

  <div class="form-container" id="bookForm">
    <form action="process-add-book.php" method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Judul Buku" required>
      <input type="file" name="cover_image" required>
      <select name="genre" required>
        <option value="">Pilih Genre</option>
        <option value="fiksi">Fiksi</option>
        <option value="nonfiksi">Non-Fiksi</option>
      </select>
      <input type="number" name="rental_price" placeholder="Harga Sewa" required>
      <input type="file" name="preview_file" required>
      <input type="file" name="full_file" required>
      <button type="submit">Tambahkan</button>
    </form>
  </div>

  <script>
    function showForm() {
      document.getElementById('bookForm').style.display = 'block';
    }
  </script>
</body>
</html>
