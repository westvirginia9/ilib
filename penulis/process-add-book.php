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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $price = $_POST['rental_price'];
    $author_id = $_SESSION['user_id'];

    // Upload cover image
    $cover_image = $_FILES['cover_image'];
    $cover_image_path = "../uploads/covers/" . uniqid() . "_" . basename($cover_image["name"]);
    move_uploaded_file($cover_image["tmp_name"], $cover_image_path);

    // Upload preview file
    $preview_file = $_FILES['preview_file'];
    $preview_file_path = "../uploads/preview/" . uniqid() . "_" . basename($preview_file["name"]);
    move_uploaded_file($preview_file["tmp_name"], $preview_file_path);

    // Upload full book file
    $full_file = $_FILES['full_file'];
    $full_file_path = "../uploads/full/" . uniqid() . "_" . basename($full_file["name"]);
    move_uploaded_file($full_file["tmp_name"], $full_file_path);

    // Insert book data into database
    $sql = "INSERT INTO books (title, genre, rental_price, author_id, cover_image, preview_file, full_file) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissss", $title, $genre, $price, $author_id, $cover_image_path, $preview_file_path, $full_file_path);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Buku berhasil ditambahkan.";
    } else {
        echo "Gagal menambahkan buku.";
    }

    $stmt->close();
}

$conn->close();
?>
