<?php
session_start();
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subjectID = $_POST['subject'];
    $numQuestions = $_POST['question-amount'];
    $title = "Custom Quiz";
    $available = date('Y-m-d H:i:s');

    $conn = newConn();

    $stmt = $conn->prepare("CALL GetQuizSetFromSubject(?, ?)");
    $stmt->bindParam(1, $subjectID);
    $stmt->bindParam(2, $numQuestions, PDO::PARAM_INT);
    $stmt->execute();

    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt->closeCursor();

    //var_dump($questions);

    $conn->beginTransaction();

$stmt = $conn->prepare("CALL StudentCreateQuiz(:quizID, :subjectID, :title, :available)");
$quizID = bin2hex(random_bytes(16));
$stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
$stmt->bindValue(":subjectID", $subjectID, PDO::PARAM_STR);
$stmt->bindValue(":title", $title, PDO::PARAM_STR);
$stmt->bindValue(":available", $available, PDO::PARAM_STR);
$stmt->execute();

$stmt = $conn->prepare("CALL StudentCreateQuizQuestionLink(:quizQuestionLinkID, :quizID, :questionID)");
foreach ($questions as $question) {
    $quizQuestionLinkID = bin2hex(random_bytes(16));
    $stmt->bindValue(":quizQuestionLinkID", $quizQuestionLinkID, PDO::PARAM_STR);
    $stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
    $stmt->bindValue(":questionID", $question['questionID'], PDO::PARAM_STR);
    $stmt->execute();
}

    $studentID = $_POST['studentID'];

$stmt = $conn->prepare("CALL StudentCreateStudentQuizLink(:studentQuizLinkID, :studentID, :quizID, :questionCount)");
$studentQuizLinkID = bin2hex(random_bytes(16));
$stmt->bindValue(":studentQuizLinkID", $studentQuizLinkID, PDO::PARAM_STR);
$stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
$stmt->bindValue(":quizID", $quizID, PDO::PARAM_STR);
$stmt->bindValue(":questionCount", $numQuestions, PDO::PARAM_INT);
$stmt->execute();

$conn->commit();


}
?>