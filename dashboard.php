<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Fetch categories
$sql = "SELECT * FROM categories ORDER BY name";
$categories = $conn->query($sql);

// Fetch user's recent quiz sessions
$user_id = $_SESSION["id"];
$sql = "SELECT qs.*, c.name as category_name 
        FROM quiz_sessions qs 
        JOIN categories c ON qs.category_id = c.id 
        WHERE qs.user_id = :user_id 
        ORDER BY qs.completed_at DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$recent_sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ড্যাশবোর্ড - বাংলা কুইজ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>স্বাগতম, <?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
            <nav>
                <a href="dashboard.php">ড্যাশবোর্ড</a>
                <?php if($_SESSION["role"] == "teacher" || $_SESSION["role"] == "admin"): ?>
                    <a href="teacher/manage_questions.php">প্রশ্ন পরিচালনা</a>
                <?php endif; ?>
                <a href="logout.php">লগআউট</a>
            </nav>
        </header>

        <main>
            <section class="categories">
                <h2>কুইজ ক্যাটাগরি</h2>
                <div class="category-grid">
                    <?php while($category = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="category-card">
                            <h3><?php echo htmlspecialchars($category["name"]); ?></h3>
                            <p><?php echo htmlspecialchars($category["description"]); ?></p>
                            <a href="quiz.php?category=<?php echo $category["id"]; ?>" class="btn">কুইজ শুরু করুন</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <section class="recent-sessions">
                <h2>সাম্প্রতিক কুইজ ফলাফল</h2>
                <div class="session-list">
                    <?php if(count($recent_sessions) > 0): ?>
                        <?php foreach($recent_sessions as $session): ?>
                            <div class="session-item">
                                <h3><?php echo htmlspecialchars($session["category_name"]); ?></h3>
                                <p>স্কোর: <?php echo $session["score"]; ?>/<?php echo $session["total_questions"]; ?></p>
                                <p>তারিখ: <?php echo date("d/m/Y", strtotime($session["completed_at"])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>এখনও কোন কুইজ সম্পন্ন করা হয়নি।</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; ২০২৪ বাংলা কুইজ - প্রাথমিক শিক্ষা</p>
        </footer>
    </div>
</body>
</html> 