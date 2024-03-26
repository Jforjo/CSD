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

// Checks if the user's ID to retireve data has been passed to this file
if (!isset($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST User ID"
)));
// Checks if the passed userID belongs to a valid user
if (!CheckUserIDExists($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "User ID does not exist"
)));
// Checks if the passed userID belongs to a student
$role = GetUserRole($_POST['userID']);
if ($role != "student") die(json_encode(array(
    "type" => "error",
    "msg" => "You cannot accept or decline a lecturer"
)));
unset($role);

// Checks if the 'studentID' value was passed to the file
if (!isset($_POST['studentID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST studentID"
)));
// Checks if the 'action' value was passed to the file (the button)
if (!isset($_POST['action'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST action"
)));
// Checks if the passed studentID belongs to a valid student
if (CheckStudentIDExists($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "A student with that ID already exists"
)));

// Checks if the 'studentID' is NULL or empty
$studentID = $_POST['studentID'];
if (ctype_space($studentID) || $studentID == '') die(json_encode(array(
    "type" => "error",
    "input" => "studentID",
    "msg" => "Student ID cannot be NULL or empty"
)));
// Checks if the 'action' is invalid
$action = $_POST['action'];
if ($action != 'accept' && $action != 'decline') die(json_encode(array(
    "type" => "error",
    "input" => "action",
    "msg" => "Form action button is invalid"
)));

// Checks the lenth of the `studentID` string
if (strlen($studentID) > 16) die(json_encode(array(
    "type" => "error",
    "input" => "studentID",
    "msg" => "Student ID is too long"
)));

$state = '';
if ($action == 'accept') $state = 'active';
else $state = 'inactive';

if (!EditUserState($_POST['userID'], $state) || !CreateStudent($_POST['userID'], $studentID)) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to " . $action . " the student"
)));

if ($action == 'accept') $action = 'accepted';
else $action = 'declined';

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully " . $action . " the student"
)));

?>