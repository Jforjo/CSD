<?php 
    require_once('connection.php');
    session_start();

    $userID = $_SESSION["userID"];
    $userName = $_SESSION['userName'];
    $conn = newConn();

    $stmt = $conn->prepare("CALL GetStudentData(:userID)");
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    $studentID = $studentData['studentID'];

    $stmt = $conn->prepare("CALL GetStudentsQuizzes(:studentID)");
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $allTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $completedTests = array_filter($allTests, function($test) {
        return $test['completed'] == 1;
    });

    $date = date('dMY'); //Get current date in the format dMY (e.g. 24Mar2024)
    $filename = $userName . "_Stats_" . $date . ".csv"; // Generate the filename

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"'); //Set the file name for the CSV

    // Open CSV file
    $fp = fopen('php://output', 'w');

    //Write the row names
    fputcsv($fp, array('Test Name', 'Subject', 'Date Completed', 'Score', 'Percentage', 'Points Earned'));

    //Get each quiz and write it to the CSV
    foreach ($completedTests as $test)
    {
        $date = date('d/m/Y', strtotime($test['dateCompleted']));
        $score = $test['correctCount'] . '/' . $test['questionCount'];
        $percentage = round(($test['correctCount'] / $test['questionCount']) * 100);
        fputcsv($fp, array($test['quiz'], $test['subject'], $date, $score, $percentage, $test['points']));
    }

    fclose($fp); //Close the CSV file

?>