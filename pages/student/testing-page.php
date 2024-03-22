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
    //$limit = 50;
    $stmt = $conn->prepare("CALL GetAllQuestions(:quizID)");
    $stmt->bindParam(":quizID", $quizID, PDO::PARAM_STR);
    //$stmt->bindParam(":limit", $quizID, PDO::PARAM_INT);
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
        <?php $correctAnswer = intval($question['correctAnswer']);?>
        <div class="question-container" id="question-<?php echo $questionNumber; ?>" data-correct-answer="<?php echo $correctAnswer; ?>" style="display: none;">
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

    <div id="complete-modal" class ="complete-modal" style="display: none;">
    <div class="complete-content">
        <h1>Test Complete</h1>
        <button id="show-results-button">View Results</button>
    </div>
</div>

    <div id="results-modal" class ="results-modal" style="display: none;">
    <div class="results-content">
        <h1>Results</h1>
        <p id="total-score">Score: </p>
        <p id="correct-answers">Correct questions: </p>
        <p id="percentage">Percentage: </p>
        <button onclick="window.location.href='index.php'">Finish</button>
    </div>
</div>
    
    <script>
    let currentQuestion = 1;
    const totalQuestions = <?php echo $totalQuestions; ?>;
    let score = 0;
    let userChoice;
    let correctQuestions = 0;

    // Show the first question
    document.getElementById('question-1').style.display = 'block';

    // Add click event listeners to the answers and next question buttons
    document.querySelectorAll('.question-container').forEach((questionContainer) => {
        const answers = questionContainer.querySelectorAll('.answer');
        answers.forEach((answer, index) => {
            answer.addEventListener('click', () => {
                // Store the number of the clicked answer
                userChoice = index + 1;

                console.log('User choice:', userChoice);

                // Display the "Next Question" button
                questionContainer.querySelector('.next-question').style.display = 'block';
            });
        });

        // Add click event listener to the "Next Question" button
        const nextQuestionButton = questionContainer.querySelector('.next-question');
        nextQuestionButton.addEventListener('click', () => {
            // Get the correct answer from the question's data attribute
            const correctAnswer = parseInt(questionContainer.dataset.correctAnswer, 10);

            // Compare the user's choice with the correct answer
            if (userChoice === correctAnswer) {
                score += 30;
                correctQuestions++;
            } else {
                score -= 10;
            }
            console.log('Correct answer:', correctAnswer);
            console.log('Score:', score);

            // Go to the next question
            if (currentQuestion < totalQuestions) {
                document.getElementById(`question-${currentQuestion}`).style.display = 'none';
                currentQuestion++;
                document.getElementById(`question-${currentQuestion}`).style.display = 'block';
            }
            else{ //Display the results
                const percentage = (correctQuestions / totalQuestions) * 100;
                document.getElementById(`question-${currentQuestion}`).style.display = 'none';
                document.getElementById('complete-modal').style.display = 'block';
                document.getElementById('total-score').textContent += score;
                document.getElementById('correct-answers').textContent += `${correctQuestions}/${totalQuestions}`;
                document.getElementById('percentage').textContent += `${percentage}%`;
            }

            document.getElementById('show-results-button').addEventListener('click', () => {
            // Hide the complete modal and show the results modal
            document.getElementById('complete-modal').style.display = 'none';
            document.getElementById('results-modal').style.display = 'flex';
        });

            // Calculate the progress percentage for the progress bar
            const progressPercentage = ((currentQuestion - 1) / totalQuestions) * 100;

            // Set the width of the progress bar div to the progress percentage
            document.querySelector(`#question-${currentQuestion} .progress-bar i`).style.width = `${progressPercentage}%`;
        });
    });

    // Set progress bar to 0% on question 1
    document.querySelector('#question-1 .progress-bar i').style.width = '0%';
</script>
</body>
</html>