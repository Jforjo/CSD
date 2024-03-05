<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
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
    require_once('php/connection.php');
    session_start();
    $dsn = DB_DSN;
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;

    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

    $userID = $_SESSION["userID"];
    $conn = newConn();

    //Get the logged in user's name
    $sql = "SELECT firstname FROM users WHERE userID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_INT);
    $stmt->execute();
    $userName = $stmt->fetchColumn();
    
    //Get the student ID
    $sql = "SELECT studentID FROM students WHERE userID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_INT);
    $stmt->execute();
    $studentID = $stmt->fetchColumn();

   // Query for completed tests
    $sql = "SELECT quizzes.quizID, quizzes.title, subjects.name, studentQuizLink.TIMESTAMP, studentQuizLink.correctCount, studentQuizLink.questionCount, studentQuizLink.points
    FROM quizzes 
    JOIN subjects ON quizzes.subjectID = subjects.subjectID
    JOIN studentQuizLink ON quizzes.quizID = studentQuizLink.quizID AND studentQuizLink.studentID = :studentID
    WHERE studentQuizLink.completed = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $completedTests = $stmt->fetchAll();

    // Query for tests to complete
    $sql = "SELECT quizzes.quizID, quizzes.title, subjects.name, studentQuizLink.TIMESTAMP  
    FROM quizzes 
    JOIN subjects ON quizzes.subjectID = subjects.subjectID
    JOIN studentQuizLink ON quizzes.quizID = studentQuizLink.quizID AND studentQuizLink.studentID = :studentID
    WHERE studentQuizLink.completed = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $testsToComplete = $stmt->fetchAll();
    ?>
    <section class="welcome-section">
        <h2 class="welcome-message"><?php echo "Welcome, " . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </section>
    <!-- Main Content of Page (Test Boxes) -->
    <div class="main-content">
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
                <h3><?php echo htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <h5><?php echo htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <h6>10 Questions</h6>
                <h6><?php echo date('d/m/Y', strtotime($test['TIMESTAMP'])); ?></h6>
                <form method="POST" action="testing-page.php">
                    <input type="hidden" name="quizID" value="<?php echo htmlspecialchars($test['quizID'], ENT_QUOTES, 'UTF-8'); ?>">
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
        <?php foreach ($completedTests as $test): ?>
            <div class="test-box">
            <div class="test-contents">
                <h3><?php echo htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <h5><?php echo htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <h6>Completed: <?php echo date('d/m/Y', strtotime($test['TIMESTAMP'])); ?></h6>
                <?php $score = ($test['correctCount'] / $test['questionCount']) * 100; ?>
                <h6>Score: <?php echo round($score); ?>%</h6>
                <h6><?php echo htmlspecialchars($test['points'], ENT_QUOTES, 'UTF-8'); ?> points</h6>
            </div>
            </div>
        <?php endforeach; ?>
        </div>
    </section>
    </div>
    <footer>Footer</footer>
</body>
</html>