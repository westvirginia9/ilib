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

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$user_id = $_SESSION['user_id'];

// Cek apakah pengguna telah membayar untuk buku ini
$sql = "SELECT * FROM rentals WHERE reader_id = ? AND book_id = ? AND payment_status = 'paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$paymentResult = $stmt->get_result();

$hasPaid = $paymentResult->num_rows > 0;

// Get book details
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$bookResult = $stmt->get_result();
$book = $bookResult->fetch_assoc();

if (!$book) {
    die("Buku tidak ditemukan.");
}

// Tentukan path file yang akan diakses
$preview_path = "../uploads/preview/" . $book['preview_file'];
$full_path = "../uploads/full/" . $book['full_file'];
$file_path = $hasPaid ? $full_path : $preview_path;

// Log untuk debugging
error_log("Preview path: " . $preview_path);
error_log("Full path: " . $full_path);
error_log("File path: " . $file_path);

// if (!file_exists($file_path)) {
//     die("File tidak ditemukan: " . $file_path);
// }

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
readfile($file_path);

$conn->close();
?>
