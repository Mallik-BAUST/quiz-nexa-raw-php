<?php
session_start();
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    $sql = "SELECT id, username, password, role FROM users WHERE username = :username";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row["password"])) {
                session_start();
                
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row["id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["role"] = $row["role"];
                
                header("location: dashboard.php");
                exit;
            } else {
                $login_err = "ভুল ইউজারনেম বা পাসওয়ার্ড।";
            }
        } else {
            $login_err = "ভুল ইউজারনেম বা পাসওয়ার্ড।";
        }
    } catch(PDOException $e) {
        echo "কিছু সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।";
    }
}
?> 