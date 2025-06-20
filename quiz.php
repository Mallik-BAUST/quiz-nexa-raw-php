<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Check if category is selected
if(!isset($_GET["category"])){
    header("location: dashboard.php");
    exit;
}

$category_id = $_GET["category"];

// Fetch category details
$sql = "SELECT * FROM categories WHERE id = :category_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":category_id", $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$category){
    header("location: dashboard.php");
    exit;
}

// Handle quiz submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $score = 0;
    $total_questions = count($_POST["answers"]);
    
    foreach($_POST["answers"] as $question_id => $answer){
        $sql = "SELECT correct_answer FROM questions WHERE id = :question_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":question_id", $question_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result["correct_answer"] == $answer){
            $score++;
        }
    }
    
    // Save quiz session
    $sql = "INSERT INTO quiz_sessions (user_id, category_id, score, total_questions) VALUES (:user_id, :category_id, :score, :total_questions)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":user_id", $_SESSION["id"]);
    $stmt->bindParam(":category_id", $category_id);
    $stmt->bindParam(":score", $score);
    $stmt->bindParam(":total_questions", $total_questions);
    $stmt->execute();
    
    header("location: result.php?session=" . $conn->lastInsertId());
    exit;
}

// Fetch questions for the category
$sql = "SELECT * FROM questions WHERE category_id = :category_id ORDER BY RAND() LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":category_id", $category_id);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category["name"]); ?> - বাংলা কুইজ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($category["name"]); ?></h1>
            <nav>
                <a href="dashboard.php">ড্যাশবোর্ড</a>
                <a href="logout.php">লগআউট</a>
            </nav>
        </header>

        <main>
            <div class="quiz-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?category=" . $category_id; ?>" method="post">
                    <?php 
                    $question_number = 1;
                    foreach($questions as $question): 
                    ?>
                        <div class="question-card">
                            <h3>প্রশ্ন <?php echo $question_number; ?></h3>
                            <p class="question-text"><?php echo htmlspecialchars($question["question_text"]); ?></p>
                            
                            <div class="options">
                                <label>
                                    <input type="radio" name="answers[<?php echo $question["id"]; ?>]" value="A" required>
                                    <?php echo htmlspecialchars($question["option_a"]); ?>
                                </label>
                                
                                <label>
                                    <input type="radio" name="answers[<?php echo $question["id"]; ?>]" value="B" required>
                                    <?php echo htmlspecialchars($question["option_b"]); ?>
                                </label>
                                
                                <label>
                                    <input type="radio" name="answers[<?php echo $question["id"]; ?>]" value="C" required>
                                    <?php echo htmlspecialchars($question["option_c"]); ?>
                                </label>
                                
                                <label>
                                    <input type="radio" name="answers[<?php echo $question["id"]; ?>]" value="D" required>
                                    <?php echo htmlspecialchars($question["option_d"]); ?>
                                </label>
                            </div>
                        </div>
                    <?php 
                    $question_number++;
                    endforeach; 
                    ?>
                    
                    <button type="submit" class="btn">কুইজ জমা দিন</button>
                </form>
            </div>
        </main>

        <footer>
            <p>&copy; ২০২৪ বাংলা কুইজ - প্রাথমিক শিক্ষা</p>
        </footer>
    </div>
</body>
</html> 