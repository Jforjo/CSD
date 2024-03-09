<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="graph.js"></script>
    <link rel="stylesheet" href="style.css">
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

    //Get the logged in user's details, things like the first name and studentID
    $stmt = $conn->prepare("CALL GetStudentData(:userID)");
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    $userName = $studentData['firstname'];
    $studentID = $studentData['studentID'];

    //Query to get the tests
   $sql = "SELECT quizzes.quizID, quizzes.title, subjects.name, studentQuizLink.TIMESTAMP, studentQuizLink.correctCount, studentQuizLink.questionCount, studentQuizLink.points, studentQuizLink.completed
   FROM quizzes 
   JOIN subjects ON quizzes.subjectID = subjects.subjectID
   JOIN studentQuizLink ON quizzes.quizID = studentQuizLink.quizID AND studentQuizLink.studentID = :studentID";
   $stmt = $conn->prepare($sql);
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
    ?>
    <section class="welcome-section">
    <h2 class="welcome-message"><?php echo "Stats for " . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </section>
    <div id="chart-container">
        <canvas id="chart"></canvas>
    </div>
    <div class="user-scores">
        <p>Total Points: 100</p>
        <p>Average Score: 60%</p>
    </div>
    <div class="col-md-6 offset-md-3">
        <div class="past-tests">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Test Name</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Date Completed</th>
                        <th scope="col">Score</th>
                        <th scope="col">Percentage</th>
                        <th scope="col">Points Earned</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($completedTests as $test): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($test['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($test['TIMESTAMP'])); ?></td>
                        <td><?php echo $test['correctCount'] . '/' . $test['questionCount']; ?></td>
                        <td><?php echo (($test['correctCount'] / $test['questionCount']) * 100) . '%'; ?></td>
                        <td><?php echo $test['points']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>