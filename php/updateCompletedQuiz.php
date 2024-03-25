<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['studentQuizLinkID']) && isset($_POST['completed'])) {
    $studentQuizLinkID = $_POST['studentQuizLinkID'];
    $completed = $_POST['completed'];
    $questionCount = $_POST['questionCount'];
    $correctCount = $_POST['correctCount'];
    $points = $_POST['points'];
    $dateCompleted = date("Y-m-d H:i:s");

    $conn = newConn();

    $stmt = $conn->prepare("CALL UpdateQuizResults(:studentQuizLinkID, :completed, :questionCount, :correctCount, :points, :dateCompleted)");
    $stmt->bindValue(":studentQuizLinkID", $studentQuizLinkID, PDO::PARAM_STR);
    $stmt->bindValue(":completed", $completed, PDO::PARAM_INT);
    $stmt->bindValue(":questionCount", $questionCount, PDO::PARAM_INT);
    $stmt->bindValue(":correctCount", $correctCount, PDO::PARAM_INT);
    $stmt->bindValue(":points", $points, PDO::PARAM_INT);
    $stmt->bindValue(":dateCompleted", $dateCompleted, PDO::PARAM_STR);
    $stmt->execute();
}
?>