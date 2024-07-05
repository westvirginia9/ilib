<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilib3";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT contest_name, start_date, deadline, contest_image FROM contests";
$result = $conn->query($sql);

$contests = [];
while ($row = $result->fetch_assoc()) {
    $contests[] = $row;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(['contests' => $contests]);
?>
