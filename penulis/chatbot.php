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

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Ambil riwayat chat dari database
$chatHistory = [];
$stmt = $conn->prepare("SELECT user_message, bot_response FROM chat WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $chatHistory[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../css/chatbot.css">
  <title>Chatbot</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .navbar {
      display: flex;
      background-color: #fff;
      padding: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .navbar .gambar {
      width: 40px;
      height: 40px;
      margin-right: 20px;
    }
    .navbar ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
    }
    .navbar ul li {
      margin-right: 20px;
    }
    .navbar ul li a {
      text-decoration: none;
      color: #333;
      font-weight: bold;
    }
    .navbar ul li a:hover {
      color: #007bff;
    }
    .content {
      padding: 20px;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .header #dashboard-title {
      font-size: 24px;
      font-weight: bold;
    }
    .search-profile {
      display: flex;
      align-items: center;
    }
    .search-profile input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-right: 10px;
    }
    .search-profile .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    .chat-container {
      display: flex;
      flex-direction: column;
      height: 70vh;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    .chat-box {
      flex: 1;
      overflow-y: auto;
      margin-bottom: 20px;
    }
    .chat-message {
      padding: 10px;
      margin: 5px 0;
      border-radius: 5px;
    }
    .chat-message.user {
      background-color: #007bff;
      color: white;
      align-self: flex-end;
    }
    .chat-message.bot {
      background-color: #f1f1f1;
      color: black;
      align-self: flex-start;
    }
    .input-box {
      display: flex;
    }
    .input-box input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-right: 10px;
    }
    .input-box button {
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .input-box button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <img class="gambar" src="../gambar/image.png" alt="Logo">
    <ul>
      <li><a href="dashbord.php">Dashboard</a></li>
      <li><a href="buku.php">Buku</a></li>
      <li><a href="databuku.php">Data Buku</a></li>
      <li><a href="biaya.php">Biaya</a></li>
      <li><a href="chatbot.php" class="active">Chatbot</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1 id="dashboard-title">Chatbot</h1>
      <div class="search-profile">
        <input type="text" placeholder="Search...">
        <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
      </div>
    </div>
    <div class="chat-container">
      <div class="chat-box" id="chat-box">
        <?php foreach ($chatHistory as $chat): ?>
          <div class="chat-message user"><?php echo htmlspecialchars($chat['user_message']); ?></div>
          <div class="chat-message bot"><?php echo htmlspecialchars($chat['bot_response']); ?></div>
        <?php endforeach; ?>
      </div>
      <div class="input-box">
        <input type="text" id="user-input" placeholder="Ketik pesan...">
        <button id="send-btn">Kirim</button>
      </div>
    </div>
  </div>
  <script>
    document.getElementById('send-btn').addEventListener('click', function() {
      const userInput = document.getElementById('user-input').value;
      if (userInput.trim() !== '') {
        const chatBox = document.getElementById('chat-box');
        const userMessage = document.createElement('div');
        userMessage.className = 'chat-message user';
        userMessage.innerText = userInput;
        chatBox.appendChild(userMessage);
        chatBox.scrollTop = chatBox.scrollHeight;

        fetch('../php/chatbot.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'message=' + encodeURIComponent(userInput),
        })
        .then(response => response.json())
        .then(data => {
          const botMessage = document.createElement('div');
          botMessage.className = 'chat-message bot';
          botMessage.innerText = data.reply;
          chatBox.appendChild(botMessage);
          chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
          console.error('Error:', error);
        });

        document.getElementById('user-input').value = '';
      }
    });
  </script>
</body>
</html>
