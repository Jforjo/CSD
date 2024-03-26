<?php session_start();
require_once('../dbfuncs.php');
// Checks if they are logged in
// Header will not work as what it would link to
//  would be sent to the JavaScript instead of actually
//  redirecting to the desired link.
if (!isset($_SESSION['userID'])) {
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if their session id is valid
if (!CheckUserIDExists($_SESSION['userID'])) {
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if their account is active
if (GetUserState($_SESSION['userID']) !== "active") {
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if they have the correct permissions
$role = GetUserRole($_SESSION['userID']);
if (!($role == "lecturer" || $role == "admin")) {
    // They shouldn't have been able to access this file without
    //  these permissions, so log them out just incase.
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
unset($role);

// Checks if the question's ID to retireve data has been passed to this file
if (!isset($_POST['questionID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST Question ID"
)));
// Checks if the passed questionID belongs to a valid question
if (!CheckQuestionIDExists($_POST['questionID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Question ID does not exist"
)));

// Checks if the 'subject' value was passed to the file
if (!isset($_POST['subject'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST subject"
)));
// Checks if the 'question' value was passed to the file
if (!isset($_POST['question'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST question"
)));
// Checks if the 'correctAnswer' value was passed to the file
if (!isset($_POST['correctAnswer'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST correctAnswer"
)));
// Checks if the 'answerOne' value was passed to the file
if (!isset($_POST['answerOne'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST answerOne"
)));
// Checks if the 'answerTwo' value was passed to the file
if (!isset($_POST['answerTwo'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST answerTwo"
)));

// Checks if the passed subject belongs to a valid subject
if (!CheckSubjectIDExists($_POST['subject'])) die(json_encode(array(
    "type" => "error",
    "msg" => "A subject with that ID does not exists"
)));
// Checks if the 'question' is NULL or empty
$question = $_POST['question'];
if (ctype_space($question) || $question == '') die(json_encode(array(
    "type" => "error",
    "input" => "question",
    "msg" => "Question cannot be NULL or empty"
)));
// Checks if the 'correctAnswer' is NULL or empty
$correctAnswer = $_POST['correctAnswer'];
if (ctype_space($correctAnswer) || $correctAnswer == '') die(json_encode(array(
    "type" => "error",
    "input" => "correctAnswer",
    "msg" => "Correct Answer cannot be NULL or empty"
)));

// Checks if the 'answerOne' is NULL or empty
$answerOne = $_POST['answerOne'];
if (ctype_space($answerOne) || $answerOne == '') die(json_encode(array(
    "type" => "error",
    "input" => "answerOne",
    "msg" => "Answer One cannot be NULL or empty"
)));
// Checks if the 'answerTwo' is NULL or empty
$answerTwo = $_POST['answerTwo'];
if (ctype_space($answerTwo) || $answerTwo == '') die(json_encode(array(
    "type" => "error",
    "input" => "answerTwo",
    "msg" => "Answer Two cannot be NULL or empty"
)));

$answerThree = '';
if (!isset($_POST['answerThree']) || ctype_space($_POST['answerThree']) || $_POST['answerThree'] == '') {
    // Checks if the 'correctAnswer' is an empty answer
    if ($correctAnswer == "3") die(json_encode(array(
        "type" => "error",
        "input" => "correctAnswer",
        "msg" => "Correct Answer cannot be an empty answer"
    )));
} else {
    $answerThree = $_POST['answerThree'];
}
$answerFour = '';
if (!isset($_POST['answerFour']) || ctype_space($_POST['answerFour']) || $_POST['answerFour'] == '') {
    // Checks if the 'correctAnswer' is an empty answer
    if ($correctAnswer == "3") die(json_encode(array(
        "type" => "error",
        "input" => "correctAnswer",
        "msg" => "Correct Answer cannot be an empty answer"
    )));
} else {
    $answerFour = $_POST['answerFour'];
}

if (!EditQuestion($_POST['questionID'], $_POST['subject'], $question, $answerOne, $answerTwo, $answerThree, $answerFour, intval($correctAnswer))) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to edit the question"
)));

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully edited the question"
)));

?>