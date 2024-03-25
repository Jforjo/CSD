<?php session_start();
require_once('dbfuncs.php');
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

// Checks if the 'questionCount' value was passed to the file
if (!isset($_POST['questionCount'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST questionCount"
)));
// Checks if the 'students[]' value was passed to the file (the button)
if (!isset($_POST['students'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST students[]"
)));
// Checks if the questionCount is an integer
if (!is_numeric($_POST['questionCount']) || intval($_POST['questionCount']) <= 0) die(json_encode(array(
    "type" => "error",
    "msg" => "Question count must be a valid positive integer above 0"
)));
$students = str_getcsv($_POST['students']);
// Checks if the students is an array that isn't empty
if (!is_array($students) || count($students) <= 0) die(json_encode(array(
    "type" => "error",
    "msg" => "At least one student must be selected"
)));
foreach ($students as $studentID) {
    // Checks if the studentID belongs to a valid student
    if (!CheckStudentIDExists($studentID)) die(json_encode(array(
        "type" => "error",
        "msg" => "A student with the ID '" . $studentID . "' does not exists"
    )));
}
foreach ($students as $studentID) {
    if (!AssignQuiz($studentID, $_POST['quizID'], intval($_POST['questionCount']))) die(json_encode(array(
        "type" => "error",
        "msg" => "Failed to set the quiz for the student with the ID of '" . $studentID . "'"
    )));
}

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully set the quiz for the students"
)));

?>