    <?php 
    require_once('php/connection.php');
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
   
   //Get the completed tests and the tests to complete
   $completedTests = array_filter($allTests, function($test) {
       return $test['completed'] == 1;
   });
   $testsToComplete = array_filter($allTests, function($test) {
       return $test['completed'] == 0;
   });

   if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTestData') {
    // Get test names
    $stmt = $conn->prepare("CALL GetStudentsQuizzes(:studentID)");
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $allTests = $stmt->fetchAll();
    $testNames = array_column($allTests, 'quiz');

    // Get percentages
    $stmt = $conn->prepare("CALL GetStudentsQuizPercentages(:studentID)");
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $percentages = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fetch only the first column of each row

    header('Content-Type: application/json');
    echo json_encode(['testNames' => $testNames, 'percentages' => $percentages]);
    exit;
}

    //Calculate the total points and average score
    $totalPoints = 0;
    $totalScore = 0;
    $NoOfTests = 0;

    foreach ($completedTests as $test) {
        $totalPoints += $test['points'];
        $score = ($test['correctCount'] / $test['questionCount']) * 100;
        $totalScore += $score;
        $NoOfTests++;
    }

    $averageScore = $NoOfTests > 0 ? round($totalScore / $NoOfTests) : 0;
    ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="graph.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Stats</a></li>
                <li><a href="#">Leaderboards</a></li>
                <li><a href="/logout" class="btn btn-primary logout-button">Logout</a></li>
            </ul>
        </nav>
    </header>
    <section class="welcome-section">
    <h2 class="welcome-message"><?php echo "Stats for " . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </section>
    <div id="chart-container">
        <canvas id="chart"></canvas>
    </div>
    <div class="user-scores">
        <div class="total-points" title="Total Points">
        <p><i class="fas fa-trophy"></i> <?php echo $totalPoints; ?></p>
        </div>
        <div class="average-score" title="Average Score">
        <p><i class="fas fa-chart-line"></i> <?php echo $averageScore; ?>%</p>
        </div>
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
                        <td><?php echo htmlspecialchars($test['quiz'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($test['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($test['dateCompleted'])); ?></td>
                        <td><?php echo $test['correctCount'] . '/' . $test['questionCount']; ?></td>
                        <td><?php echo (($test['correctCount'] / $test['questionCount']) * 100) . '%'; ?></td>
                        <td><?php echo $test['points']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">
$(document).ready(function() {
    $('.table').DataTable({
        "searching": false,
        "pageLength": 10,
        "lengthChange": false
    });
});
</script>
</body>
</html>