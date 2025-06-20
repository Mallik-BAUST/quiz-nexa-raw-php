<?php
session_start();
require_once('../config/db.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

// Add new question
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    $question = $_POST['question'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct_answer = $_POST['correct_answer'];
    $subject = $_POST['subject'];
    
    $stmt = $conn->prepare("INSERT INTO questions (teacher_id, question, option1, option2, option3, option4, correct_answer, subject) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['teacher_id'], $question, $option1, $option2, $option3, $option4, $correct_answer, $subject]);
    
    $success = "প্রশ্নটি সফলভাবে যোগ করা হয়েছে!";
}

// Get all questions by this teacher
$stmt = $conn->prepare("SELECT * FROM questions WHERE teacher_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['teacher_id']]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>শিক্ষক ড্যাশবোর্ড - কুইজ নেক্সা</title>
    <style>
        body {
            font-family: 'Noto Sans Bengali', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        .questions-list {
            margin-top: 30px;
        }
        .question-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .logout {
            float: right;
            background: #f44336;
        }
        .logout:hover {
            background: #da190b;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php"><button class="logout">লগআউট</button></a>
        <h2>স্বাগতম, <?php echo $_SESSION['teacher_name']; ?></h2>
        
        <?php if(isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <h3>নতুন প্রশ্ন যোগ করুন</h3>
        <form method="POST">
            <div class="form-group">
                <label>বিষয়</label>
                <select name="subject" required>
                    <option value="বাংলা">বাংলা</option>
                    <option value="ইংরেজি">ইংরেজি</option>
                    <option value="গণিত">গণিত</option>
                    <option value="বিজ্ঞান">বিজ্ঞান</option>
                </select>
            </div>
            <div class="form-group">
                <label>প্রশ্ন</label>
                <textarea name="question" required rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>অপশন ১</label>
                <input type="text" name="option1" required>
            </div>
            <div class="form-group">
                <label>অপশন ২</label>
                <input type="text" name="option2" required>
            </div>
            <div class="form-group">
                <label>অপশন ৩</label>
                <input type="text" name="option3" required>
            </div>
            <div class="form-group">
                <label>অপশন ৪</label>
                <input type="text" name="option4" required>
            </div>
            <div class="form-group">
                <label>সঠিক উত্তর</label>
                <select name="correct_answer" required>
                    <option value="1">অপশন ১</option>
                    <option value="2">অপশন ২</option>
                    <option value="3">অপশন ৩</option>
                    <option value="4">অপশন ৪</option>
                </select>
            </div>
            <button type="submit" name="add_question">প্রশ্ন যোগ করুন</button>
        </form>

        <div class="questions-list">
            <h3>আপনার প্রশ্নসমূহ</h3>
            <?php foreach($questions as $question): ?>
                <div class="question-item">
                    <h4><?php echo htmlspecialchars($question['question']); ?></h4>
                    <p>বিষয়: <?php echo htmlspecialchars($question['subject']); ?></p>
                    <p>১) <?php echo htmlspecialchars($question['option1']); ?></p>
                    <p>২) <?php echo htmlspecialchars($question['option2']); ?></p>
                    <p>৩) <?php echo htmlspecialchars($question['option3']); ?></p>
                    <p>৪) <?php echo htmlspecialchars($question['option4']); ?></p>
                    <p>সঠিক উত্তর: অপশন <?php echo $question['correct_answer']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 