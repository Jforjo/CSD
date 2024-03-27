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

// Checks if the quiz's ID to retireve data has been passed to this file
if (!isset($_POST['quizID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST Quiz ID"
)));
// Checks if the passed quizID belongs to a valid quiz
if (!CheckQuizIDExists($_POST['quizID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Quiz ID does not exist"
)));

// Retrieve the quiz's data from the database
$quizData = GetQuizData($_POST['quizID']);
// Checks if, for some reason, FALSE was returned
if ($quizData === false) die(json_encode(array(
    "type" => "error",
    "msg" => "An unknown error occurred while retrieving the quiz's data"
)));
// Return the quiz's data
exit(json_encode(array(
    "type" => "data",
    "data" => $quizData
)));

?>