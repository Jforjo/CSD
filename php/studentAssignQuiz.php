<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subjectID = $_POST['subject'];
    $numQuestions = $_POST['question-amount'];

    $conn = newConn();

    $stmt = $conn->prepare("CALL GetQuizSetFromSubject(?, ?)");
    $stmt->bindParam(1, $subjectID);
    $stmt->bindParam(2, $numQuestions, PDO::PARAM_INT);
    $stmt->execute();

    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    var_dump($questions);
}
?>