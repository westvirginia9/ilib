<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilib3";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contest_name = $_POST['contest_name'];
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    $contest_image = $_POST['contest_image'];
    $created_by = $_SESSION['user_id']; // Asumsikan admin sudah login

    $sql = "INSERT INTO contests (contest_name, start_date, deadline, contest_image, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $contest_name, $start_date, $deadline, $contest_image, $created_by);

    if ($stmt->execute()) {
        echo "Kontes berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan kontes: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header('Location: kontes.php');
    exit();
}
?>
