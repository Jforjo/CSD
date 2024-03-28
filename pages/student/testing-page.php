<?php 
    require_once(__DIR__ . '/../../php/dbfuncs.php');
    //require_once('../../php/connection.php');
    session_start();

    $userID = $_SESSION["userID"];
    try {
        $conn = newConn();
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

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

    if (!$quizData) {
        header("Location: /dashboard");
        exit();
    }

    $subjectName = $quizData['subject'];
    $quizName = $quizData['title'];

    $subjectName = $quizData['subject'];
    $quizName = $quizData['title'];

    //Get Quiz Set
    $limit = 50;
    $stmt = $conn->prepare("CALL GetQuizSet(:quizID, :limit)");
    $stmt->bindParam(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->execute();
    $allQuestions = $stmt->fetchAll();

    if (!$allQuestions) {
        header("Location: /dashboard");
        exit();
    }

    //Get question number and total number of questions
    $questionNumber = 0;
    $totalQuestions = count($allQuestions);
}
   ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Page</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head> 
<body>
    <header>
        <nav>
            <ul>
            <div class="logo">
            <a>
                <span>Qu</span><i>?</i><span>z</span>
            </a>
            </div>
            <button class="mobile-nav-dropdown">&#9776;</button>
            <div class="dropdown-links">
                <a href="/dashboard">Home</a>
                <a href="/stats">Stats</a>
                <a href="/logout">Logout</a>
            </div>
            <div class="nav-links">
                <li><i class="fas fa-home"></i><a href="/dashboard"> Home</a></li>
                <li><i class="fas fa-chart-line"></i><a href="/stats"> Stats</a></li>
            </div>
                <li><a href="/logout" class="btn btn-primary logout-button"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="test-info-section">
    <div class="test-info">
        <?php //Check to see if the subject name and quiz name are set, if not then redirect to student dashboard.
            if (!isset($subjectName) || !isset($quizName)) {
                header("Location: /dashboard");
                exit();
            }
            ?>
        <h1><?php echo htmlspecialchars($subjectName, ENT_QUOTES, 'UTF-8'); ?></h1>
        <h2><?php echo htmlspecialchars($quizName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>
    </div>
    <div class="main-content">
        <!-- Loop through each question -->
        <?php //Check to see if the questions are set, if not then redirect to student dashboard.
        if (!isset($allQuestions)) {
            header("Location: /dashboard");
            exit();
        }
        foreach ($allQuestions as $question): ?>
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

    <div id="complete-modal" class ="modal" style="display: none;">
    <div class="modal-content">
        <h1>Test Complete</h1>
        <button id="show-results-button">View Results</button>
    </div>
</div>

    <div id="results-modal" class ="modal" style="display: none;">
    <div class="modal-content">
        <h1>Results</h1>
        <p id="total-score"><i class="fas fa-trophy"></i>Points Earned: </p>
        <p id="correct-answers"><i class="fas fa-check"></i>Correct questions: </p>
        <p id="percentage"><i class="fas fa-chart-line"></i>Percentage: </p>
        <a href="/dashboard" class="button">Finish</a>
    </div>
</div>
<script>
    //Set the variables
    var totalQuestions = <?php echo $totalQuestions; ?>;
    var studentQuizLinkID = <?php echo json_encode($studentQuizLinkID); ?>;
    var quizID = <?php echo json_encode($quizID); ?>;
</script>
<script src="../../assets/js/testing-page.js"></script>
<footer></footer>
</body>
</html>
