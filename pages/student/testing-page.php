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
        <?php //$correctAnswer = $question['correctAnswer'];?>
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
    <!-- Displays the answer boxes for the question, if an answer box is empty, then it won't be displayed -->
    <div class="answers">
    <?php if (!empty($question['answerOne'])): ?>
        <div class="answer"><?php echo htmlspecialchars($question['answerOne'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($question['answerTwo'])): ?>
        <div class="answer"><?php echo htmlspecialchars($question['answerTwo'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($question['answerThree'])): ?>
        <div class="answer"><?php echo htmlspecialchars($question['answerThree'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($question['answerFour'])): ?>
        <div class="answer"><?php echo htmlspecialchars($question['answerFour'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    </div>
    <button class="next-question" style="display: none;">Next Question</button>
    </div>
    <?php endforeach; ?>
    </div>
    
    <script>
    let currentQuestion = 1;
    const totalQuestions = <?php echo $totalQuestions; ?>;
    let score = 0;

    // Show the first question
    document.getElementById('question-1').style.display = 'block';

    // Add click event listeners to the answers and next question buttons
    document.querySelectorAll('.answer').forEach((answer, index) => {
        answer.addEventListener('click', () => {
            // Set the user's choice to the index of the clicked answer plus 1
            const userChoice = index + 1;

            // Get the correct answer from the question's data attribute
            const correctAnswer = parseInt(answer.parentNode.dataset.correctAnswer, 10);

            // Compare the user's choice with the correct answer
            if (userChoice === correctAnswer) {
                score++;
            }

            // Log the user's choice and the correct answer
            console.log('User choice:', userChoice);
            console.log('Correct answer:', correctAnswer);

            document.querySelector(`#question-${currentQuestion} .next-question`).style.display = 'block';
        });
    });

    // Set progress bar to 0% on question 1
    document.querySelector('#question-1 .progress-bar i').style.width = '0%';

    document.querySelectorAll('.next-question').forEach(button => {
        button.addEventListener('click', () => {
            // Hide the current question
            document.getElementById(`question-${currentQuestion}`).style.display = 'none';

            // Show the next question
            currentQuestion++;
            if (currentQuestion <= totalQuestions) {
                document.getElementById(`question-${currentQuestion}`).style.display = 'block';
            }
            else{
                document.body.innerHTML = '<h1>Quiz complete</h1>'; //After the user has completed all the questions
            }

            // Calculate the progress percentage
            const progressPercentage = ((currentQuestion - 1) / totalQuestions) * 100;

            // Set the width of the progress bar div to the progress percentage
            document.querySelector(`#question-${currentQuestion} .progress-bar i`).style.width = `${progressPercentage}%`;
        });
    });
</script>
</body>
</html>