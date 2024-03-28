<?php 
    require_once(__DIR__ . '/../../php/dbfuncs.php');
    //require_once('../../php/connection.php');
    session_start();

    $userID = $_SESSION["userID"];
    try {
        $conn = newConn();
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
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

    //If no student data is found then show error
    if (!$studentData) {
        throw new Exception("Failed to fetch student data.");
    }

    $userName = $studentData['firstname'];
    $studentID = $studentData['studentID'];

   //Query to get the tests
   $stmt = $conn->prepare("CALL GetStudentsQuizzes(:studentID)");
   $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
   $stmt->execute();
   $allTests = $stmt->fetchAll();
   
   //Get the completed tests and the tests to complete
   $completedTests = array_filter($allTests, function($test) {
       return $test['completed'] == 1;
   });
   $testsToComplete = array_filter($allTests, function($test) {
       return $test['completed'] == 0;
   });

    //Get the subjects
    $stmt = $conn->prepare("CALL GetAllSubjects()");
    $stmt->execute();

    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //If no subjects are found then show error
    if (!$subjects) {
        throw new Exception("No subjects found.");
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <link rel="stylesheet" href="../../style.css">
    <title>Dashboard</title>
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
    <section class="welcome-section">
        <h2 class="welcome-message"><?php echo "Welcome, " . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </section>
    <!-- Main Content of Page (Test Boxes) -->
    <div class="main-content">
    <div class="createQuizButtonContainer">
        <button id="createQuizButton">Create Quiz</button>
    </div>
    <!-- Modal that appears when button is clicked -->
    <div id="createQuizModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="createQuizForm" method="post" action="../../php/studentAssignQuiz.php">
            <h2>Create Quiz</h2>
            <p>Select the subject</p>
            <select name="subject">
            <option selected disabled>Select a subject</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= $subject['subjectID'] ?>"><?= $subject['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <select name="question-amount">
            <option value="10">10 Questions</option>
            <option value="15">15 Questions</option>
            <option value="20">20 Questions</option>
        </select>
        <input type="hidden" name="studentID" value="<?= $studentID ?>">
            <input type="submit" class="createQuizConfirmButton" value="Create">
        </form>
        </div>
    </div>
    <!-- Modal that appears after a quiz has successfully been created -->
    <div id="successModal" class="modal">
    <div class="modal-content">
        <p>Quiz successfully created!</p>
        <button id="successButton">OK</button>
    </div>
    </div>
    <div id="errorModal" class="modal">
    <div class="modal-content">
        <p id="errorMessage"></p>
        <button id="errorButton">OK</button>
    </div>
</div>
    <!-- Tests to complete section -->
    <section>
        <h2 class="section-title">Tests to complete</h2>
        <!-- Area of page that contains the test boxes -->
        <div class="test-section">
        <?php if (empty($testsToComplete)): ?>
            <div class="no-test-message">
                <p>No tests to complete!</p>
            </div>
        <?php else: ?>
        <?php foreach ($testsToComplete as $test): ?>
            <div class="test-box">
            <div class="test-contents">
                <h3><?php echo htmlspecialchars($test['subject'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <h5><?php echo htmlspecialchars($test['quiz'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <?php
                //Get the question count for the current test
                $sql = "SELECT COUNT(*) as questionCount FROM quizQuestionLink WHERE quizID = :quizID";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(":quizID", $test['quizID'], PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch();
                $questionCount = $result['questionCount'];
                ?>
                <div class="num-of-questions-label">
                <h6><?php echo $questionCount; ?> Questions</h6>
                </div>
                <div class="date-set-label">
                <h6><?php echo date('d/m/Y', strtotime($test['dateSet'])); ?></h6>
                </div>
                <form method="POST" action="/quiz">
                    <input type="hidden" name="quizID" value="<?php echo htmlspecialchars($test['quizID'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="studentQuizLinkID" value="<?php echo htmlspecialchars($test['studentQuizLinkID'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-primary">Start Test</button>
                </form>
            </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>  
    </section>
    <section>
        <!-- Completed tests section -->
        <h2 class="section-title">Completed Tests</h2>
        <!-- Area of page that contains the test boxes -->
        <div class="test-section">
            <?php if (empty($completedTests)): ?>
            <div class="no-test-message">
                <p>As you complete tests they will show up here</p>
            </div>
        <?php else: ?>
        <?php foreach ($completedTests as $test): ?>
            <div class="test-box">
            <div class="test-contents">
                <h3><?php echo htmlspecialchars($test['subject'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <h5><?php echo htmlspecialchars($test['quiz'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <h6>Completed: <?php echo date('d/m/Y', strtotime($test['dateCompleted'])); ?></h6>
                <?php $score = ($test['correctCount'] / $test['questionCount']) * 100; ?>
                <h6>Score: <?php echo round($score); ?>%</h6>
                <h6><?php echo htmlspecialchars($test['points'], ENT_QUOTES, 'UTF-8'); ?> points</h6>
            </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </section>
    </div>
    <footer></footer>
    <script src="../../assets/js/student-create-quiz.js"></script>
<script>
document.querySelector('.mobile-nav-dropdown').addEventListener('click', function() {
    var dropdownLinks = document.querySelector('.dropdown-links');
    if (dropdownLinks.style.display === 'none' || dropdownLinks.style.display === '') {
        dropdownLinks.style.display = 'block';
    } else {
        dropdownLinks.style.display = 'none';
    }
});

window.addEventListener('resize', function() {
    var dropdownLinks = document.querySelector('.dropdown-links');
    if (window.innerWidth >= 769) {
        dropdownLinks.style.display = 'none';
    }
});
</script>
</body>
</html>