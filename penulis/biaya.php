<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'penulis') {
    header('Location: login.php');
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

// Ambil data buku yang dimiliki penulis
$sql = "SELECT title, genre, id, rental_period, rental_price FROM books WHERE author_id = ?";
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
  <link rel="stylesheet" type="text/css" href="../csspengguna/databuku.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Biaya</title>
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
        text-align: right;
        color: green;
        font-weight: bold;
    }
    .book-table button {
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .book-table button:hover {
        background-color: #0056b3;
    }
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.4); 
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%; 
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
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
  </style>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
        <li><a href="dashbord.php">Dashboard</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1 id="dashboard-title">Biaya</h1>
      <div class="search-profile">
        <input type="text" placeholder="Cari...">
        <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
      </div>
    </div>
    <!-- Konten utama halaman disini -->

    <div class="data-container2">
        <div class="status">Daftar Biaya <span class="penulis-aktif">Penulis Aktif</span></div>
        <table class="book-table">
          <tr>
            <th>Nama Buku</th>
            <th>Jenis Buku</th>
            <th>ID Buku</th>
            <th>Masa Berlaku</th>
            <th>Harga</th>
            <th>Aksi</th>
          </tr>
          <?php foreach ($books as $book): ?>
          <tr>
            <td><?php echo htmlspecialchars($book['title']); ?></td>
            <td><?php echo htmlspecialchars($book['genre']); ?></td>
            <td><?php echo htmlspecialchars($book['id']); ?></td>
            <td><?php echo htmlspecialchars($book['rental_period']); ?></td>
            <td>Rp. <?php echo number_format($book['rental_price'], 2, ',', '.'); ?></td>
            <td>
              <button onclick="showEditModal(<?php echo $book['id']; ?>, '<?php echo $book['title']; ?>', <?php echo $book['rental_price']; ?>)">Edit</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
    </div>
  </div>

  <!-- Modal untuk mengedit harga -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Edit Harga</h2>
      <form id="editForm" method="POST" action="update_price.php">
        <input type="hidden" id="bookId" name="book_id">
        <label for="bookTitle">Nama Buku:</label>
        <input type="text" id="bookTitle" name="book_title" readonly>
        <label for="newPrice">Harga Baru:</label>
        <input type="number" id="newPrice" name="new_price" required>
        <button type="submit">Update</button>
      </form>
    </div>
  </div>

  <script>
    function showEditModal(bookId, bookTitle, rentalPrice) {
      document.getElementById('bookId').value = bookId;
      document.getElementById('bookTitle').value = bookTitle;
      document.getElementById('newPrice').value = rentalPrice;
      document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target == document.getElementById('editModal')) {
        closeModal();
      }
    }

    // Tampilkan SweetAlert jika ada pesan
    <?php if (isset($_SESSION['message'])): ?>
      Swal.fire({
        title: 'Informasi',
        text: '<?php echo $_SESSION['message']; ?>',
        icon: 'info',
        confirmButtonText: 'OK'
      });
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
  </script>
</body>
</html>
