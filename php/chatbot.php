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

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Masukkan autoload Composer
require '../vendor/autoload.php';

use LucianoTonet\GroqPHP\Groq;

$response = '';

try {
    $groq = new Groq('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');

    if (!empty($_POST['message'])) {
        $userMessage = htmlspecialchars($_POST['message']);

        // Ambil data dari database sesuai dengan pertanyaan
        $contextData = '';

        // Tambahkan informasi konteks umum tentang asisten ilib
        $contextData = "Anda sedang berbicara dengan asisten ilib. Saya di sini untuk membantu menjawab pertanyaan Anda sebagai penulis.";

        if (strpos($userMessage, 'genre buku apa yang paling banyak dibaca') !== false || strpos($userMessage, 'jenis buku apa') !== false) {
            // Mengambil data genre buku yang paling banyak dibaca
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
        }

        // Gabungkan data konteks dengan pesan pengguna
        $prompt = $contextData . "\n\nPertanyaan pengguna: " . $userMessage;

        // Kirim prompt ke API Groq
        $chatCompletion = $groq->chat()->completions()->create([
            'model'    => 'gemma-7b-it',
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $prompt
                ],
            ]
        ]);

        $response = $chatCompletion['choices'][0]['message']['content'];

        // Simpan percakapan ke database dengan prepared statement
        $stmt = $conn->prepare("INSERT INTO chat (user_id, user_message, bot_response) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $userMessage, $response);
        $stmt->execute();
        $stmt->close();
    }
} catch (Exception $e) {
    $response = 'Kesalahan: ' . $e->getMessage();
}

echo json_encode(['reply' => $response]);

$conn->close(); // Tutup koneksi
?>
