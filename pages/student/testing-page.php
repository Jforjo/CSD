<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Page</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
    require_once(__DIR__ . '/../../php/dbfuncs.php');
    session_start();

    $userID = $_SESSION["userID"];
    $conn = newConn();

    //Check to see if user is logged in, if not then redirect to login page
    if (!isset($_SESSION['userID']))
    {
        header("Location: /login");
        exit();    
    }


    //Check to see if the user is a student, if not then display error message
    $role = GetUserRole($_SESSION['userID']);
if (!($role == "student")) {
    die("You do not have permission to view this page.");
}

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
    $studentQuizLinkID = $_POST['studentQuizLinkID'];

    //echo "Student Quiz Link ID: " . $studentQuizLinkID;

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
        <div class="question-container" id="question-<?php echo $questionNumber; ?>" data-question-id="<?php echo $question['questionID']; ?>" style="display: none;">
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
        <p id="total-score"><i class="fas fa-trophy"></i>Points Earned: </p>
        <p id="correct-answers"><i class="fas fa-check"></i>Correct questions: </p>
        <p id="percentage"><i class="fas fa-chart-line"></i>Percentage: </p>
        <a href="index.php" class="button">Finish</a>
    </div>
</div>
<script>
// Set the variables
    let currentQuestion = 1;
    let questionID = document.getElementById(`question-${currentQuestion}`).dataset.questionId;
    const totalQuestions = <?php echo $totalQuestions; ?>;
    let score = 0;
    let userChoice;
    let correctQuestions = 0;
    let studentQuizLinkID = <?php echo json_encode($studentQuizLinkID); ?>;
    let quizID = <?php echo json_encode($quizID); ?>;

// Show the first question
document.getElementById('question-1').style.display = 'block';

// Add click event listeners to the answers and next question buttons
document.querySelectorAll('.question-container').forEach((questionContainer) => {
    const answers = questionContainer.querySelectorAll('.answer');
    answers.forEach((answer, index) => {
        answer.addEventListener('click', () => {
            // Remove the "selected" class from all answers
            answers.forEach((answer) => {
                answer.classList.remove('selected');
            });

            // Add the "selected" class to the clicked answer
            answer.classList.add('selected');
            // Store the number of the clicked answer
            userChoice = index + 1;

            // Display the "Next Question" button
            questionContainer.querySelector('.next-question').style.display = 'block';
        });
    });

    // Add click event listener to the "Next Question" button
    const nextQuestionButton = questionContainer.querySelector('.next-question');
    nextQuestionButton.addEventListener('click', () => {
        checkAnswerAndUpdateProgress();
    });
});

// Set progress bar to 0% on question 1
document.querySelector('#question-1 .progress-bar i').style.width = '0%';

function checkAnswerAndUpdateProgress() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../php/checkAnswer.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(`quizID=${quizID}&questionNumber=${currentQuestion}&userChoice=${userChoice}&questionID=${questionID}`);
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // The server has responded
            var response = JSON.parse(this.responseText);

            // Check if the user's answer was correct
            if (response.correct) {
                score += 30;
                correctQuestions++;
            } else {
                score -= 10;
            }

            // Go to the next question
            if (currentQuestion < totalQuestions) {
                document.getElementById(`question-${currentQuestion}`).style.display = 'none';
                currentQuestion++;
                questionID = document.getElementById(`question-${currentQuestion}`).dataset.questionId;
                document.getElementById(`question-${currentQuestion}`).style.display = 'block';
                // Calculate the progress percentage for the progress bar
                const progressPercentage = ((currentQuestion - 1) / totalQuestions) * 100;
                // Set the width of the progress bar div to the progress percentage
                document.querySelector(`#question-${currentQuestion} .progress-bar i`).style.width = `${progressPercentage}%`;
            }
            else {
                displayResults();
            }
        }
    };
}

function displayResults() {
    //Display the results
    let percentage = (correctQuestions / totalQuestions) * 100;
    percentage = Math.round(percentage);
    //document.getElementById(`question-${currentQuestion}`).style.display = 'none';
    document.getElementById('complete-modal').style.display = 'flex';
    document.getElementById('total-score').textContent += score;
    document.getElementById('correct-answers').textContent += `${correctQuestions}/${totalQuestions}`;
    document.getElementById('percentage').textContent += `${percentage}%`;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../php/updateCompletedQuiz.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(`studentQuizLinkID=${studentQuizLinkID}&completed=1&questionCount=${totalQuestions}&correctCount=${correctQuestions}&points=${score}`);
}

document.getElementById('show-results-button').addEventListener('click', () => {
    // Hide the complete modal and show the results modal
    document.getElementById('complete-modal').style.display = 'none';
    document.getElementById('results-modal').style.display = 'flex';
});
</script>
</body>
</html>