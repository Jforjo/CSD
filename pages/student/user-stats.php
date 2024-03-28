<?php 
    require_once(__DIR__ . '/../../php/dbfuncs.php');
    //require_once('../../php/connection.php');
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

    $_SESSION['userName'] = $userName;

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
    $stmt = $conn->prepare("CALL GetStudentsQuizPercentages(:studentID)");
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $allTests = $stmt->fetchAll();

    $testNames = array_column($allTests, 'title');
    $percentages = array_column($allTests, 'percentage');

    // Round the percentages
    $percentages = array_map('round', $percentages);

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

    $_SESSION['userName'] = $userName;
    $_SESSION['completedTests'] = $completedTests;
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
    <script defer src="../../assets/js/graph.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.0.0-rc.7/html2canvas.min.js"></script>
    <link rel="stylesheet" href="../../style.css">
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
    <h2 class="welcome-message"><?php echo "Stats for " . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h2>
    </section>
    <div id="chart-container">
        <canvas id="chart"></canvas>
        <div id="errorMessage" style="display: none;"></div>
        <input type="checkbox" id="toggleYAxis" checked> Start y-axis at 0
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
                        <td><?php echo round(($test['correctCount'] / $test['questionCount']) * 100) . '%'; ?></td>
                        <td><?php echo $test['points']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="exportButtonContainer">
        <button id="exportButton">Export Data</button>
    </div>

    <!-- Modal that appears when button is clicked -->
    <div id="exportModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Export Data</h2>
            <p>Choose the format you would like to export your data in:</p>
            <div class="export-buttons">
            <form action="../../php/exportCSV.php" method="post">
                <button type="submit" id="exportCSV">CSV</button>
            </form>
            <form action="../../php/exportPDF.php" method="post">
                <button type="submit" id="exportPDF">PDF</button>
            </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
$(document).ready(function() {
    $('.table').DataTable({
        "searching": false,
        "pageLength": 10,
        "lengthChange": false,
        "order": [[2, "desc"]]
    });
});
</script>
<script>
    var modal = document.getElementById("exportModal");
    var btn = document.getElementById("exportButton");
    var span = document.getElementsByClassName("close")[0];
    btn.onclick = function() {
        modal.style.display = "flex";
    }
    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
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
<footer></footer>
</body>
</html>