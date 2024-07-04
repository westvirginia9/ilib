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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

require '../vendor/autoload.php';

use LucianoTonet\GroqPHP\Groq;

$response = '';

try {
    $groq = new Groq('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');

    if (!empty($_POST['message'])) {
        $userMessage = htmlspecialchars($_POST['message']);
        $contextData = "Anda bernama Asisten ilib, Anda siap membantu penulis di ilib untuk melakukan analisis atau menjawab pertanyaan dari penulis dari ilib. Jawablah semua pertanyaan dalam bahasa Indonesia.";

        if (strpos($userMessage, 'genre buku apa yang paling banyak dibaca') !== false || strpos($userMessage, 'jenis buku apa') !== false) {
            $stmt = $conn->prepare("SELECT b.genre, COUNT(r.id) AS count 
                                    FROM books b 
                                    JOIN rentals r ON b.id = r.book_id 
                                    WHERE b.author_id = ? 
                                    GROUP BY b.genre 
                                    ORDER BY count DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $contextData = "Berdasarkan analisis, genre buku yang paling banyak dibaca adalah:";
                while ($row = $result->fetch_assoc()) {
                    $genre = htmlspecialchars($row['genre']);
                    $count = htmlspecialchars($row['count']);
                    $contextData .= "\n* **$genre** ($count kali dibaca)";
                }
            } else {
                $contextData = "Tidak ada data genre buku yang ditemukan.";
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'berapa banyak buku nonfiksi yang saya miliki') !== false) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM books WHERE author_id = ? AND genre = 'nonfiksi'");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $count = $row['count'];
                $contextData = "Anda memiliki $count buku nonfiksi.";
            } else {
                $contextData = "Tidak ada data buku nonfiksi yang ditemukan.";
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'berapa kali buku saya disewa') !== false) {
            $stmt = $conn->prepare("SELECT COUNT(r.id) AS count FROM rentals r 
                                    JOIN books b ON r.book_id = b.id 
                                    WHERE b.author_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $count = $row['count'];
                $contextData = "Buku Anda telah disewa sebanyak $count kali.";
            } else {
                $contextData = "Tidak ada data penyewaan buku yang ditemukan.";
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'berapa pendapatan saya dari buku') !== false) {
            $stmt = $conn->prepare("SELECT SUM(r.amount) AS total_income FROM rentals r 
                                    JOIN books b ON r.book_id = b.id 
                                    WHERE b.author_id = ? AND r.payment_status = 'paid'");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_income = $row['total_income'];
                $contextData = "Total pendapatan Anda dari buku adalah Rp $total_income.";
            } else {
                $contextData = "Tidak ada data pendapatan yang ditemukan.";
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'bagaimana performa penjualan buku saya') !== false) {
            $stmt = $conn->prepare("SELECT b.title, COUNT(r.id) AS rentals, SUM(r.amount) AS income
                                    FROM books b
                                    JOIN rentals r ON b.id = r.book_id
                                    WHERE b.author_id = ?
                                    GROUP BY b.title");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $contextData = "Berikut adalah performa penjualan buku Anda:";
                while ($row = $result->fetch_assoc()) {
                    $title = htmlspecialchars($row['title']);
                    $rentals = htmlspecialchars($row['rentals']);
                    $income = htmlspecialchars($row['income']);
                    $contextData .= "\n* **$title** - Disewa $rentals kali, Pendapatan: Rp $income";
                }
            } else {
                $contextData = "Tidak ada data penjualan buku yang ditemukan.";
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'apa tren penjualan buku saya selama 6 bulan terakhir') !== false) {
            $stmt = $conn->prepare("SELECT MONTH(r.rental_date) AS month, COUNT(r.id) AS rentals, SUM(r.amount) AS income
                                    FROM rentals r
                                    JOIN books b ON r.book_id = b.id
                                    WHERE b.author_id = ? AND r.rental_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                                    GROUP BY MONTH(r.rental_date)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $contextData = "Tren penjualan buku Anda selama 6 bulan terakhir adalah sebagai berikut:";
                while ($row = $result->fetch_assoc()) {
                    $month = htmlspecialchars($row['month']);
                    $rentals = htmlspecialchars($row['rentals']);
                    $income = htmlspecialchars($row['income']);
                    $contextData .= "\n* Bulan $month: Disewa $rentals kali, Pendapatan: Rp $income";
                }
            } else {
                $contextData = "Tidak ada data tren penjualan yang ditemukan.";
            }
            $stmt->close();
        } else {
            $contextData = "Maaf, saya tidak bisa menjawabnya. Berikan pertanyaan seputar aplikasi ini.";
        }

        error_log("Context Data: " . $contextData);
        error_log("User Message: " . $userMessage);

        $prompt = $contextData . "\n\nPertanyaan pengguna: " . $userMessage;

        $chatCompletion = $groq->chat()->completions()->create([
            'model'    => 'gemma-7b-it',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'Anda bernama Asisten ilib, Anda siap membantu penulis di ilib untuk melakukan analisis atau menjawab pertanyaan dari penulis dari ilib. Anda berhak mengakses ke data buku pengguna, Jawablah semua pertanyaan dalam bahasa Indonesia.'
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        $response = $chatCompletion['choices'][0]['message']['content'];

        error_log("Groq Response: " . $response);

        $stmt = $conn->prepare("INSERT INTO chat (user_id, user_message, bot_response) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $userMessage, $response);
        $stmt->execute();
        $stmt->close();
    }
} catch (Exception $e) {
    $response = 'Kesalahan: ' . $e->getMessage();
    error_log("Exception: " . $e->getMessage());
}

echo json_encode(['reply' => $response]);

$conn->close();
?>
