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

// Ambil data kontes dari database
$sql = "SELECT * FROM contests";
$result = $conn->query($sql);

$contests = [];
while ($row = $result->fetch_assoc()) {
    $contests[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="kontes.css">
    <title>Kontes</title>
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .kontes-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin: 10px;
            text-align: center;
        }

        .kontes-card img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .kontes-card h3 {
            margin: 10px 0;
        }

        .join-button {
            background-color: #5415BC;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
    <div class="navbar">
        <img class="gambar" src="../gambar/image.png" alt="Logo">
        <ul>
            <li><a class="active" href="kontes.php">Kontes</a></li>
            <li><a href="trend.php">Trend</a></li>
            <li><a href="buku.php">Buku</a></li>
            <li><a href="tentang.php">Tentang</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <h1 id="dashboard-title">Kontes yang Tersedia</h1>
            <div class="search-profile">
                <input type="text" placeholder="Cari...">
                <img class="profile-pic" src="../gambar/22965342.jpg" alt="Profile Picture">
            </div>
        </div>
        <!-- Konten utama halaman disini -->
        <div class="kontes-container">
            <div class="kontes-section">
                <h2>Pencarian baru-baru ini</h2>
                <div class="kontes-grid">
                    <?php foreach ($contests as $contest): ?>
                    <div class="kontes-card">
                        <img src="../uploads/<?= htmlspecialchars($contest['contest_image']) ?>" alt="Kontes Image">
                        <h3><?= htmlspecialchars($contest['contest_name']) ?></h3>
                        <div class="rating">Rating: ★★★★☆</div>
                        <button class="join-button" onclick='showContestDetails(<?= json_encode($contest) ?>)'>Bergabung Sekarang</button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Tambahkan lebih banyak bagian jika diperlukan -->
        </div>
    </div>

    <!-- Modal untuk detail kontes -->
    <div id="contestModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="contestTitle"></h2>
            <p id="contestStartDate"></p>
            <p id="contestDeadline"></p>
            <button id="confirmJoinButton">Bergabung Sekarang</button>
        </div>
    </div>

    <script>
    function showContestDetails(contest) {
        document.getElementById('contestTitle').innerText = contest.contest_name;
        document.getElementById('contestStartDate').innerText = "Mulai: " + contest.start_date;
        document.getElementById('contestDeadline').innerText = "Berakhir: " + contest.deadline;

        document.getElementById('confirmJoinButton').onclick = function() {
            joinContest(contest.id);
        };

        document.getElementById('contestModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('contestModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('contestModal')) {
            closeModal();
        }
    }

    function joinContest(contest_id) {
        fetch('join_contest.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'contest_id=' + contest_id,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log the response for debugging
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                });
            } else if (data.status === 'exists') {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: data.message,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                });
            }
            closeModal();
        })
        .catch(error => {
            console.error('Error:', error); // Log the error for debugging
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat bergabung dengan kontes.',
            });
            closeModal();
        });
    }
</script>


</body>
</html>
