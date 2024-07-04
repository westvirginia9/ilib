<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
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

$response = [];

// Jumlah Pengguna
$sql = "SELECT COUNT(*) AS count FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$response['jumlah_pengguna'] = $row['count'];

// Pemasukan
$sql = "SELECT SUM(amount) AS total FROM rentals WHERE payment_status = 'paid'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$response['pemasukan'] = $row['total'];

// Peserta kontes
$sql = "SELECT COUNT(*) AS count FROM contest_participants";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$response['peserta_kontes'] = $row['count'];

$conn->close();

echo json_encode($response);
?>
