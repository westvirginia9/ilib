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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contest_id = $_POST['contest_id'];
    $reader_id = $_SESSION['user_id'];
    $check_sql = "SELECT * FROM contest_participants WHERE contest_id = ? AND reader_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $contest_id, $reader_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(["status" => "exists", "message" => "Anda sudah bergabung dengan kontes ini."]);
    } else {
        $sql = "INSERT INTO contest_participants (contest_id, reader_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $contest_id, $reader_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Berhasil bergabung dengan kontes!"]);
        } else {
            error_log("Error: " . $stmt->error);
            echo json_encode(["status" => "error", "message" => "Gagal bergabung dengan kontes: " . $stmt->error]);
        }

        $stmt->close();
    }
    $check_stmt->close();
    $conn->close();
}
?>
