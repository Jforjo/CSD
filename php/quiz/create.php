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
if (!($role == "lecturer" || $role == "admin" || $role == "student")) {
    // They shouldn't have been able to access this file without
    //  these permissions, so log them out just incase.
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}

// Checks if the 'title' value was passed to the file
if (!isset($_POST['title'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST title"
)));
// Checks if the 'subject' value was passed to the file
if (!isset($_POST['subject'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST subject"
)));
if ($role == "student") {
    $available = null;
} else {
    // Checks if the 'available' value was passed to the file
    if (!isset($_POST['available'])) die(json_encode(array(
        "type" => "error",
        "msg" => "Invalid POST available"
    )));
    $available = $_POST['available'];
}

// Checks if the passed subject belongs to a valid subject
if (!CheckSubjectIDExists($_POST['subject'])) die(json_encode(array(
    "type" => "error",
    "msg" => "A subject with that ID does not exists"
)));

// Checks if the 'title' is NULL or empty
$title = $_POST['title'];
if (ctype_space($title) || $title == '') die(json_encode(array(
    "type" => "error",
    "input" => "title",
    "msg" => "Title cannot be NULL or empty"
)));

if (!CreateQuiz($_POST['subject'], $title, $available)) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to create the quiz"
)));

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully Created the quiz"
)));

?>