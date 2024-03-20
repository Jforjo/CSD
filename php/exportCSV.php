<?php 
    require_once('connection.php');
    session_start();

    $userID = $_SESSION["userID"];
    $conn = newConn();

    $stmt = $conn->prepare("CALL GetStudentData(:userID)");
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    $studentID = $studentData['studentID'];

    $stmt = $conn->prepare("CALL GetStudentsQuizzes(:studentID)");
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();

    $completedTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="user_stats.csv"'); //Set the file name for the CSV

    // Open CSV file
    $fp = fopen('php://output', 'w');

    //Write the row names
    fputcsv($fp, array('Test Name', 'Subject', 'Date Completed', 'Score', 'Percentage', 'Points Earned'));

    //Get each quiz and write it to the CSV
    foreach ($completedTests as $test)
    {
        $date = date('d/m/Y', strtotime($test['dateCompleted']));
        $score = $test['correctCount'] . '/' . $test['questionCount'];
        $percentage = ($test['correctCount'] / $test['questionCount']) * 100;
        fputcsv($fp, array($test['quiz'], $test['subject'], $date, $score, $percentage, $test['points']));
    }

    fclose($fp); //Close the CSV file

?>