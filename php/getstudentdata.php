<?php session_start();
// Checks if they are logged in
// Header will not work as what it would link to
//  would be sent to the JavaScript instead of actually
//  redirecting to the desired link.
if (!isset($_SESSION['userID'])) {
    session_unset();
    session_destroy();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
require_once('dbfuncs.php');
// Checks if their session id is valid
if (!CheckUserExists($_SESSION['userID'])) {
    session_unset();
    session_destroy();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if they have the correct permissions
$role = GetUserRole($_SESSION['userID']);
if (!($role == "lecturer" || $role == "admin")) {
    // They shouldn't have been able to access this file without
    //  these permissions, so log them out just incase.
    session_unset();
    session_destroy();
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
if (!CheckUserExists($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "User ID does not exist"
)));
// Checks if the passed userID belongs to a student
$role = GetUserRole($_POST['userID']);
if ($role != "student") die(json_encode(array(
    "type" => "error",
    "msg" => "You do not have permission to view this user"
)));
unset($role);

// Retrieve the user's data from the database
$studentData = GetStudentData($_POST['userID']);
// Checks if, for some reason, FALSE was returned
if ($studentData === false) die(json_encode(array(
    "type" => "error",
    "msg" => "An unknown error occurred while retrieving the user's data"
)));
// Return the user's data
exit(json_encode($studentData));

?>