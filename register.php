<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            display: flex;
            width: 70%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .left {
            background-color: #ffffff;
            padding: 50px;
            width: 50%;
        }
        .right {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            padding: 50px;
            color: #fff;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .right h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        .right p {
            font-size: 16px;
            line-height: 1.6;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .form-container label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #6a11cb;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #2575fc;
        }
        .error-message {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="right">
            <h1>Welcome to ILib</h1>
            <p>Your Infinite Library.</p>
        </div>
        <div class="left">
            <h2>User Registration</h2>
            <form class="form-container" action="php/process-register.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="penulis">Penulis</option>
                    <option value="pembaca">Pembaca</option>
                </select>
                <button type="submit">Register</button>
            </form>
            <?php
            if (isset($_SESSION['register_error'])) {
                echo '<p class="error-message">' . $_SESSION['register_error'] . '</p>';
                unset($_SESSION['register_error']);
            }
            ?>
        </div>
    </div>
</body>
</html>
