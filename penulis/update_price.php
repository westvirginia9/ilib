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

// Ambil data dari form
$book_id = $_POST['book_id'];
$new_price = $_POST['new_price'];

// Update harga di database
$sql = "UPDATE books SET rental_price = ? WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dii", $new_price, $book_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    $_SESSION['message'] = "Harga berhasil diperbarui.";
} else {
    $_SESSION['message'] = "Gagal memperbarui harga.";
}
$stmt->close();

$conn->close();

header('Location: biaya.php');
exit();
?>
