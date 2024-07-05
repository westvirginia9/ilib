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

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);
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

        // Tambahkan informasi konteks umum tentang asisten ilib untuk admin
        $contextData = "Anda adalah asisten untuk admin di ilib, Anda siap membantu pertanyaan terkait data admin seperti income harian, jumlah pengguna, peserta kontes, dan analisis pendapatan. selalu jawab menggunakan bahasa indonesia.";

        // Pertanyaan tentang income harian
        if (strpos($userMessage, 'income harian') !== false) {
            $stmt = $conn->prepare("SELECT date, income FROM daily_income");
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $contextData .= " Data income harian adalah sebagai berikut:";
                while ($row = $result->fetch_assoc()) {
                    $date = htmlspecialchars($row['date']);
                    $income = htmlspecialchars($row['income']);
                    $contextData .= "\n* Tanggal: $date, Total Income: $income";
                }
            } else {
                $contextData .= " Tidak ada data income harian yang ditemukan.";
            }
            $stmt->close();
        }
        // Pertanyaan tentang jumlah pengguna
        else if (strpos($userMessage, 'jumlah pengguna') !== false) {
            $stmt = $conn->prepare("SELECT role, COUNT(*) AS count FROM users GROUP BY role");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $contextData .= " Data jumlah pengguna berdasarkan peran adalah sebagai berikut:";
                while ($row = $result->fetch_assoc()) {
                    $role = htmlspecialchars($row['role']);
                    $count = htmlspecialchars($row['count']);
                    $contextData .= "\n* Peran: $role, Jumlah: $count";
                }
            } else {
                $contextData .= " Tidak ada data jumlah pengguna yang ditemukan.";
            }
            $stmt->close();
        }
        // Pertanyaan tentang peserta kontes
        else if (strpos($userMessage, 'peserta kontes') !== false) {
            $stmt = $conn->prepare("SELECT contest_id, COUNT(*) AS count FROM contest_participants GROUP BY contest_id");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $contextData .= " Data peserta kontes adalah sebagai berikut:";
                while ($row = $result->fetch_assoc()) {
                    $contest_id = htmlspecialchars($row['contest_id']);
                    $count = htmlspecialchars($row['count']);
                    $contextData .= "\n* Kontes ID: $contest_id, Jumlah Peserta: $count";
                }
            } else {
                $contextData .= " Tidak ada data peserta kontes yang ditemukan.";
            }
            $stmt->close();
        } else {
            $contextData .= " Maaf, saya tidak bisa menjawabnya. Berikan pertanyaan seputar data admin di ilib.";
        }

        // Debugging log
        error_log("Context Data: " . $contextData);
        error_log("User Message: " . $userMessage);

        // Gabungkan data konteks dengan pesan pengguna
        $prompt = $contextData . "\n\nPertanyaan pengguna: " . $userMessage;

        // Kirim prompt ke API Groq
        $chatCompletion = $groq->chat()->completions()->create([
            'model'    => 'llama3-8b-8192',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'Anda adalah asisten untuk admin di ilib, Anda siap membantu pertanyaan terkait data admin seperti income harian, jumlah pengguna, peserta kontes, dan analisis pendapatan. selalu jawab menggunakan bahasa indonesia.'
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        if (!empty($chatCompletion['choices'][0]['message']['content'])) {
            $response = $chatCompletion['choices'][0]['message']['content'];
        } else {
            $response = 'Tidak ada jawaban yang dapat diberikan. Silakan coba lagi.';
        }

        // Debugging log
        error_log("Groq Response: " . $response);

        // Simpan percakapan ke database dengan prepared statement
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

$conn->close(); // Tutup koneksi
