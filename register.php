<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <form action="php/process-register.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="penulis">Penulis</option>
                <option value="pembaca">Pembaca</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <?php
        if (isset($_SESSION['register_error'])) {
            echo '<p style="color:red;">' . $_SESSION['register_error'] . '</p>';
            unset($_SESSION['register_error']);
        }
        ?>
    </div>
</body>
</html>
