<?php
session_start();
require_once "config/database.php";

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বাংলা কুইজ - প্রাথমিক শিক্ষা</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>বাংলা কুইজ</h1>
            <p>প্রাথমিক শিক্ষার্থীদের জন্য বাংলা ভাষা শিক্ষার কুইজ</p>
        </header>

        <main>
            <div class="auth-container">
                <div class="auth-box">
                    <h2>লগইন করুন</h2>
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label for="username">ইউজারনেম</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">পাসওয়ার্ড</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit">লগইন</button>
                    </form>
                    <p>অ্যাকাউন্ট নেই? <a href="register.php">রেজিস্টার করুন</a></p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; ২০২৪ বাংলা কুইজ - প্রাথমিক শিক্ষা</p>
        </footer>
    </div>
</body>
</html> 