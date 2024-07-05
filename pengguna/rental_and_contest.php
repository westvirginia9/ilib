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

// Ambil data buku yang sudah disewa
$sql = "SELECT b.id, b.title, b.cover_image, r.rental_date
        FROM rentals r
        JOIN books b ON r.book_id = b.id
        WHERE r.reader_id = ? AND r.payment_status = 'paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookResult = $stmt->get_result();

$books = [];
while ($row = $bookResult->fetch_assoc()) {
    $books[] = $row;
}
$stmt->close();

// Ambil data kontes yang diikuti
$sql = "SELECT c.id, c.contest_name, c.start_date, c.deadline, c.contest_image
        FROM contest_participants cp
        JOIN contests c ON cp.contest_id = c.id
        WHERE cp.reader_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$contestResult = $stmt->get_result();

$contests = [];
while ($row = $contestResult->fetch_assoc()) {
    $contests[] = $row;
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../csspengguna/styles.css">
    <title>Buku dan Kontes Saya</title>
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
        .grid-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 200px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .card p {
            color: #555;
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
            <h2>Buku yang Disewa</h2>
            <div class="grid-container">
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="card">
                            <img src="../uploads/<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover Buku">
                            <h3><?= htmlspecialchars($book['title']) ?></h3>
                            <p>Disewa pada: <?= htmlspecialchars($book['rental_date']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada buku yang disewa.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="section">
            <h2>Kontes yang Diikuti</h2>
            <div class="grid-container">
                <?php if (!empty($contests)): ?>
                    <?php foreach ($contests as $contest): ?>
                        <div class="card">
                            <img src="../uploads/<?= htmlspecialchars($contest['contest_image']) ?>" alt="Gambar Kontes">
                            <h3><?= htmlspecialchars($contest['contest_name']) ?></h3>
                            <p>Mulai: <?= htmlspecialchars($contest['start_date']) ?></p>
                            <p>Berakhir: <?= htmlspecialchars($contest['deadline']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada kontes yang diikuti.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
