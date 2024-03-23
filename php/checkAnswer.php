<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('connection.php');

$quizID = $_POST['quizID'];
$questionID = $_POST['questionID'];
$questionNumber = $_POST['questionNumber'];
$userChoice = $_POST['userChoice'];

$conn = newConn();

// Get the correct answer from the database
$stmt = $conn->prepare("SELECT correctAnswer FROM questions WHERE questionID = :questionID");
$stmt->bindValue(":questionID", $questionID, PDO::PARAM_STR);
$stmt->execute();
$correctAnswer = $stmt->fetchColumn();

// Check if the user's answer is correct
$correct = ($userChoice == $correctAnswer);

// Send a response back to the client
echo json_encode(['correct' => $correct]);
?>