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

$sql = "SELECT books.*, users.name AS author_name 
        FROM books 
        JOIN users ON books.author_id = users.id";
$result = $conn->query($sql);

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
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-wGHeB_77sjrLoW2O"></script>
  <title>Buku</title>
  <style>
    /* CSS for Modal */
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
      background-color: #fff;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 60%;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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

    .modal-header, .modal-body, .modal-footer {
      padding: 10px;
    }

    .modal-header {
      border-bottom: 1px solid #eee;
    }

    .modal-footer {
      border-top: 1px solid #eee;
      text-align: right;
    }

    .modal-title {
      font-size: 24px;
      margin: 0;
    }

    .modal-body p {
      margin: 10px 0;
      font-size: 16px;
      color: #333;
    }

    .modal-footer button {
      padding: 10px 20px;
      margin: 5px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .modal-footer button:hover {
      opacity: 0.9;
    }

    .preview-button {
      background-color: #4CAF50;
      color: white;
    }

    .rent-button {
      background-color: #008CBA;
      color: white;
    }

    /* CSS for Gallery Navigation */
    .image-gallery {
      display: flex;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    .gallery-wrapper {
      display: flex;
      transition: transform 0.5s ease;
    }

    .gallery-item {
      flex: 0 0 20%;
      max-width: 20%;
    }

    .gallery-image {
      width: 100%;
      height: auto;
      cursor: pointer;
    }

    .nav-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      padding: 10px;
      cursor: pointer;
      z-index: 2;
    }

    .nav-button.left {
      left: 10px;
    }

    .nav-button.right {
      right: 10px;
    }
  </style>
</head>
<body>

<ul>
  <li class="profile-icon1">
    <img src="../gambar/image.png" alt="Profile Icon">
  </li>
  <li><a class="active" href="kontes.php">kontes</a></li>
  <li><a href="trend.php">trend</a></li>
  <li><a href="pembaca.php">buku</a></li>
  <li><a href="tentang.php">tentang</a></li>
  <li><a href="rental_and_contest.php">Anda</a></li>
  <li class="search-bar">
    <input type="text" placeholder="Cari...." name="search">
  </li>
  <li class="profile-icon">
    <img src="../gambar/22965342.jpg" alt="Profile Icon">
  </li>
</ul>

<div class="image-gallery">
  <button class="nav-button left" onclick="scrollGallery(-1)">&#10094;</button>
  <div class="gallery-wrapper">
    <?php foreach ($books as $book): ?>
    <div class="gallery-item" onclick="showBookDetails(<?php echo htmlspecialchars(json_encode($book)); ?>)">
      <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover Buku" class="gallery-image">
    </div>
    <?php endforeach; ?>
  </div>
  <button class="nav-button right" onclick="scrollGallery(1)">&#10095;</button>
</div>

<div id="bookModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2 id="bookTitle" class="modal-title"></h2>
    </div>
    <div class="modal-body">
      <p id="bookAuthor"></p>
      <p id="bookGenre"></p>
      <p id="bookDate"></p>
    </div>
    <div class="modal-footer">
      <button id="readButton" class="preview-button">Preview</button>
      <button id="rentButton" class="rent-button">Sewa</button>
    </div>
  </div>
</div>

<script>
const galleryWrapper = document.querySelector('.gallery-wrapper');
let scrollPosition = 0;

function scrollGallery(direction) {
  const itemWidth = document.querySelector('.gallery-item').offsetWidth;
  const galleryWidth = galleryWrapper.scrollWidth;
  const visibleWidth = document.querySelector('.image-gallery').offsetWidth;

  scrollPosition += direction * itemWidth;

  if (scrollPosition < 0) {
    scrollPosition = 0;
  } else if (scrollPosition > galleryWidth - visibleWidth) {
    scrollPosition = galleryWidth - visibleWidth;
  }

  galleryWrapper.style.transform = `translateX(-${scrollPosition}px)`;
}

function showBookDetails(book) {
  document.getElementById('bookTitle').innerText = book.title;
  document.getElementById('bookAuthor').innerText = "Penulis: " + book.author_name;
  document.getElementById('bookGenre').innerText = "Genre: " + book.genre;
  document.getElementById('bookDate').innerText = "Terunggah: " + book.created_at;

  document.getElementById('readButton').onclick = function() {
    Swal.fire({
      title: 'Anda membaca preview buku',
      text: book.title,
      icon: 'info',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = '../read-book.php?book_id=' + book.id + '&page=1';
    });
  };

  document.getElementById('rentButton').onclick = function() {
    Swal.fire({
      title: 'Mulai Pembayaran',
      text: 'Anda akan diarahkan ke halaman pembayaran.',
      icon: 'info',
      confirmButtonText: 'Lanjutkan'
    }).then(() => {
      window.location.href = '../php/payment.php?book_id=' + book.id;
    });
  };

  document.getElementById('bookModal').style.display = "block";
}

function closeModal() {
  document.getElementById('bookModal').style.display = "none";
}

window.onclick = function(event) {
  if (event.target == document.getElementById('bookModal')) {
    closeModal();
  }
}

// Ensure this function is globally accessible
window.showBookDetails = showBookDetails;
</script>

</body>
</html>


