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
    <?php require_once('php/connection.php'); ?>
    <?php
    $dsn = DB_DSN;
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;

    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);
    
   // Query for completed tests
$sql = "SELECT quizzes.quizID, quizzes.title, subjects.name, studentQuizLink.TIMESTAMP 
FROM quizzes 
JOIN subjects ON quizzes.subjectID = subjects.subjectID
JOIN studentQuizLink ON quizzes.quizID = studentQuizLink.quizID
WHERE studentQuizLink.completed = 1";
$stmt = $pdo->query($sql);
$completedTests = $stmt->fetchAll();

// Query for tests to complete
$sql = "SELECT quizzes.quizID, quizzes.title, subjects.name, studentQuizLink.TIMESTAMP  
FROM quizzes 
JOIN subjects ON quizzes.subjectID = subjects.subjectID
JOIN studentQuizLink ON quizzes.quizID = studentQuizLink.quizID
WHERE studentQuizLink.completed = 0";
$stmt = $pdo->query($sql);
$testsToComplete = $stmt->fetchAll();
    ?>
    <section class="welcome-section">
        <h2 class="welcome-message">Welcome, Chris</h2>
    </section>
    <!-- Main Content of Page (Test Boxes) -->
    <div class="main-content">
    <!-- Tests to complete section -->
    <section>
        <h2 class="section-title">Tests to complete</h2>
        <!-- Area of page that contains the test boxes -->
        <div class="test-section">
        <?php foreach ($testsToComplete as $test): ?>
            <div class="test-box">
            <div class="test-contents">
                <h3><?php echo htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <h5><?php echo htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <h6>10 Questions</h6>
                <a href="testing-page.php" class="btn btn-primary">Start Test</a>
            </div>
            </div>
            <?php endforeach; ?>
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
                <h6>Score: 80%</h6>
                <h6>250 points</h6>
            </div>
            </div>
        <?php endforeach; ?>
        </div>
    </section>
    </div>
    <footer>Footer</footer>
</body>
</html>