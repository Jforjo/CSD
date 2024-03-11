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

// Checks if the user's ID has been passed to this file
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
    "msg" => "You do not have permission to delete this user"
)));
unset($role);

if (!DeleteUser($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to delete the student"
)));

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully deleted the student"
)));

?>