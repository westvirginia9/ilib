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
$payment_result = isset($_GET['result']) ? json_decode(urldecode($_GET['result']), true) : null;

if ($payment_result && $payment_result['status_code'] == '200') {
    $amount = $payment_result['gross_amount'];

    // Cek apakah rental sudah ada untuk buku ini oleh user ini
    $sql = "SELECT * FROM rentals WHERE book_id = ? AND reader_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();
    $rentalResult = $stmt->get_result();

    if ($rentalResult->num_rows > 0) {
        // Jika rental sudah ada, perbarui status pembayaran dan tanggal sewa
        $sql = "UPDATE rentals SET payment_status = 'paid', rental_date = NOW(), status = 'aktif', amount = ?, payment_date = NOW() WHERE book_id = ? AND reader_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $amount, $book_id, $user_id);
    } else {
        // Jika rental belum ada, masukkan data rental baru
        $sql = "INSERT INTO rentals (book_id, reader_id, amount, payment_date, payment_status, status, rental_date) VALUES (?, ?, ?, NOW(), 'paid', 'aktif', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iid", $book_id, $user_id, $amount);
    }

    if ($stmt->execute()) {
        // Update reader_count di tabel books
        $sql = "UPDATE books SET reader_count = reader_count + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        header("Location: ../read-book.php?book_id=$book_id");
    } else {
        echo "Gagal menyimpan data pembayaran: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Pembayaran gagal atau dibatalkan.";
}

$conn->close();
?>
