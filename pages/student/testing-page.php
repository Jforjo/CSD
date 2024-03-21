<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Page</title>
    <link rel="stylesheet" href="../../style.css">
</head> 
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Stats</a></li>
                <li><a href="#">Leaderboards</a></li>
            </ul>
        </nav>
    </header>
    <?php 
    require_once('../../php/connection.php');
    session_start();

    $userID = $_SESSION["userID"];
    $conn = newConn();

    //Get the logged in user's details, things like the first name and studentID
    $stmt = $conn->prepare("CALL GetStudentData(:userID)");
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    $userName = $studentData['firstname'];
    $studentID = $studentData['studentID'];

   //Query to get the tests
   $stmt = $conn->prepare("CALL GetStudentsQuizzes(:studentID)");
   $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
   $stmt->execute();
   $allTests = $stmt->fetchAll();

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quizID = $_POST['quizID'];

    //Get Quiz Data
    $stmt = $conn->prepare("CALL GetQuizData(:quizID)");
    $stmt->bindParam(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->execute();
    $quizData = $stmt->fetch(PDO::FETCH_ASSOC);

    $subjectName = $quizData['subject'];
    $quizName = $quizData['title'];

    $subjectName = $quizData['subject'];
    $quizName = $quizData['title'];

    //Get Quiz Set
    $limit = 50;
    $stmt = $conn->prepare("CALL GetQuizSet(:quizID, :limit)");
    $stmt->bindParam(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->bindParam(":limit", $quizID, PDO::PARAM_INT);
    $stmt->execute();
    $allQuestions = $stmt->fetchAll();

    //Get question number and total number of questions
    $questionNumber = 0;
    $totalQuestions = count($allQuestions);
}
   ?>
    <div class="test-info-section">
    <div class="test-info">
        <h1><?php echo htmlspecialchars($subjectName, ENT_QUOTES, 'UTF-8'); ?></h1>
        <h2><?php echo htmlspecialchars($quizName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>
    </div>
    <div class="main-content">
        <!-- Loop through each question -->
        <?php foreach ($allQuestions as $question): ?>
        <!-- Increment the question number -->
        <?php $questionNumber++; ?>
        <div class="question-container" id="question-<?php echo $questionNumber; ?>" style="display: none;">
    <div class="question">
        <p><?php echo htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div class="question-number">
        <p>Question <?php echo $questionNumber; ?>/<?php echo $totalQuestions; ?></p>
    </div>
    <div class="progress-bar">
        <i></i>
    </div>
    <div class="answers">
        <div class="answer"><?php echo htmlspecialchars($question['answerOne'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="answer"><?php echo htmlspecialchars($question['answerTwo'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="answer"><?php echo htmlspecialchars($question['answerThree'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="answer"><?php echo htmlspecialchars($question['answerFour'], ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <button class="next-question" style="display: none;">Next Question</button>
    </div>
    <?php endforeach; ?>
    </div>
    
    <script>
        let currentQuestion = 1;
        const totalQuestions = <?php echo $totalQuestions; ?>;

        // Show the first question
        document.getElementById('question-1').style.display = 'block';

        // Add click event listeners to the answers and next question buttons
        document.querySelectorAll('.answer').forEach(answer => {
            answer.addEventListener('click', () => {
                document.querySelector(`#question-${currentQuestion} .next-question`).style.display = 'block';
            });
        });

        document.querySelectorAll('.next-question').forEach(button => {
            button.addEventListener('click', () => {
                // Hide the current question
                document.getElementById(`question-${currentQuestion}`).style.display = 'none';

                // Show the next question if it exists
                currentQuestion++;
                if (currentQuestion <= totalQuestions) {
                    document.getElementById(`question-${currentQuestion}`).style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>