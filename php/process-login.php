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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data user dari database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['name']; // Pastikan Anda memiliki field username di database
            $_SESSION['email'] = $user['email']; // Pastikan Anda memiliki field email di database

            if ($user['role'] == 'admin') {
                header('Location: ../admin/dashboard-admin.php');
            } else if ($user['role'] == 'penulis') {
                header('Location: ../penulis/dashbord.php');
            } else if ($user['role'] == 'pembaca') {
                header('Location: ../pengguna/buku.php');
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password.";
        }
    } else {
        $_SESSION['login_error'] = "Invalid email.";
    }

    $stmt->close();
    $conn->close();
    header('Location: ../login.php');
    exit();
} else {
    header('Location: ../login.php');
    exit();
}
?>
