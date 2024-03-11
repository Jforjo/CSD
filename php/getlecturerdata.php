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
if (!CheckUserIDExists($_SESSION['userID'])) {
    session_unset();
    session_destroy();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
// Checks if they have the correct permissions
$role = GetUserRole($_SESSION['userID']);
if ($role != "admin") {
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
if (!CheckUserIDExists($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "User ID does not exist"
)));
// Checks if the passed userID belongs to a lecturer
$role = GetUserRole($_POST['userID']);
// This will also throw an error if the user's role is a student
//  even though an admin has edit permissions for them,
//  but there is a seperate file for getting a student's data.
if ($role != "lecturer") die(json_encode(array(
    "type" => "error",
    "msg" => "You do not have permission to view this user"
)));
unset($role);

// Retrieve the user's data from the database
$lecturerData = GetLecturerData($_POST['userID']);
// Checks if, for some reason, FALSE was returned
if ($lecturerData === false) die(json_encode(array(
    "type" => "error",
    "msg" => "An unknown error occurred while retrieving the user's data"
)));
// Return the user's data
exit(json_encode(array(
    "type" => "data",
    "data" => $lecturerData
)));

?>