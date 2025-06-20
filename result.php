<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Check if session ID is provided
if(!isset($_GET["session"])){
    header("location: dashboard.php");
    exit;
}

$session_id = $_GET["session"];

// Fetch quiz session details
$sql = "SELECT qs.*, c.name as category_name 
        FROM quiz_sessions qs 
        JOIN categories c ON qs.category_id = c.id 
        WHERE qs.id = :session_id AND qs.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":session_id", $session_id);
$stmt->bindParam(":user_id", $_SESSION["id"]);
$stmt->execute();
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$session){
    header("location: dashboard.php");
    exit;
}

// Calculate percentage
$percentage = ($session["score"] / $session["total_questions"]) * 100;

// Determine grade
$grade = "";
if($percentage >= 90) {
    $grade = "A+";
} elseif($percentage >= 80) {
    $grade = "A";
} elseif($percentage >= 70) {
    $grade = "B";
} elseif($percentage >= 60) {
    $grade = "C";
} else {
    $grade = "F";
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ফলাফল - বাংলা কুইজ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>কুইজ ফলাফল</h1>
            <nav>
                <a href="dashboard.php">ড্যাশবোর্ড</a>
                <a href="logout.php">লগআউট</a>
            </nav>
        </header>

        <main>
            <div class="result-container">
                <div class="result-card">
                    <h2><?php echo htmlspecialchars($session["category_name"]); ?></h2>
                    
                    <div class="score-circle">
                        <div class="score-number"><?php echo $percentage; ?>%</div>
                        <div class="score-grade"><?php echo $grade; ?></div>
                    </div>
                    
                    <div class="score-details">
                        <p>সঠিক উত্তর: <?php echo $session["score"]; ?></p>
                        <p>মোট প্রশ্ন: <?php echo $session["total_questions"]; ?></p>
                    </div>
                    
                    <div class="feedback">
                        <?php if($percentage >= 70): ?>
                            <p class="success">অভিনন্দন! আপনি খুব ভালো করেছেন!</p>
                        <?php elseif($percentage >= 50): ?>
                            <p class="warning">ভালো চেষ্টা! আরও ভালো করার জন্য অনুশীলন করুন।</p>
                        <?php else: ?>
                            <p class="error">দুঃখিত, আপনি পাস করতে পারেননি। অনুগ্রহ করে আবার চেষ্টা করুন।</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="quiz.php?category=<?php echo $session["category_id"]; ?>" class="btn">আবার চেষ্টা করুন</a>
                        <a href="dashboard.php" class="btn btn-secondary">ড্যাশবোর্ডে ফিরে যান</a>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; ২০২৪ বাংলা কুইজ - প্রাথমিক শিক্ষা</p>
        </footer>
    </div>
</body>
</html> 