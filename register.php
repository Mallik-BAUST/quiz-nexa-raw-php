<?php
require_once "config/database.php";

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if(empty(trim($_POST["username"]))) {
        $username_err = "দয়া করে একটি ইউজারনেম দিন।";
    } else {
        $sql = "SELECT id FROM users WHERE username = :username";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $param_username);
            $param_username = trim($_POST["username"]);
            $stmt->execute();
            
            if($stmt->rowCount() == 1) {
                $username_err = "এই ইউজারনেমটি ইতিমধ্যে নেওয়া হয়েছে।";
            } else {
                $username = trim($_POST["username"]);
            }
        } catch(PDOException $e) {
            echo "কিছু সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।";
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))) {
        $email_err = "দয়া করে একটি ইমেইল দিন।";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))) {
        $password_err = "দয়া করে একটি পাসওয়ার্ড দিন।";     
    } elseif(strlen(trim($_POST["password"])) < 6) {
        $password_err = "পাসওয়ার্ড কমপক্ষে ৬টি অক্ষর হতে হবে।";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "দয়া করে পাসওয়ার্ড নিশ্চিত করুন।";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "পাসওয়ার্ড মিলছে না।";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {
        $sql = "INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, 'student')";
         
        try {
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", password_hash($password, PASSWORD_DEFAULT));
            $stmt->bindParam(":email", $email);
            
            if($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "কিছু সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।";
            }
        } catch(PDOException $e) {
            echo "কিছু সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>রেজিস্টার - বাংলা কুইজ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>রেজিস্টার করুন</h1>
            <p>নতুন অ্যাকাউন্ট তৈরি করুন</p>
        </header>

        <main>
            <div class="auth-container">
                <div class="auth-box">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="username">ইউজারনেম</label>
                            <input type="text" id="username" name="username" value="<?php echo $username; ?>">
                            <span class="error"><?php echo $username_err; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">ইমেইল</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
                            <span class="error"><?php echo $email_err; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">পাসওয়ার্ড</label>
                            <input type="password" id="password" name="password">
                            <span class="error"><?php echo $password_err; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">পাসওয়ার্ড নিশ্চিত করুন</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <span class="error"><?php echo $confirm_password_err; ?></span>
                        </div>
                        
                        <button type="submit">রেজিস্টার</button>
                    </form>
                    <p>ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="index.php">লগইন করুন</a></p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; ২০২৪ বাংলা কুইজ - প্রাথমিক শিক্ষা</p>
        </footer>
    </div>
</body>
</html> 