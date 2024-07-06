<?php
session_start();
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

// Ambil data buku yang paling banyak dibaca berdasarkan reader_count
$sql = "SELECT id, title, cover_image, reader_count FROM books ORDER BY reader_count DESC LIMIT 3";
$result = $conn->query($sql);

// Debug output
if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
}

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
    <link rel="stylesheet" type="text/css" href="../css/trend.css">
    <title>Trend</title>
</head>
<body>
    
<section>
    <ul>
        <li class="profile-icon1">
            <img src="../gambar/image.png" alt="Profile Icon">
        </li>
        <li><a class="active" href="kontes.php">Kontes</a></li>
        <li><a href="trend.php">Trend</a></li>
        <li><a href="buku.php">Buku</a></li>
        <li><a href="tentang.php">Tentang</a></li>
        <li class="search-bar">
            <input type="text" placeholder="Cari...." name="search">
        </li>
        <li class="profile-icon">
            <img src="../gambar/22965342.jpg" alt="Profile Icon">
        </li>
    </ul>
</section>

<section class="trend-section">
    <div class="content">
        <h2 class="t">Buku Populer</h2>
        <div class="buku-container">
            <div class="buku-populer">
                <?php foreach ($books as $book): ?>
                <div class="buku-item">
                    <img class="ini-gmbar" src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="buku-info">
                        <img class="eye-icon" src="../gambar/eye-icon.png" alt="Eye Icon">
                        <p class="readers"><?php echo htmlspecialchars($book['reader_count']); ?> pembaca</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

</body>
</html>
