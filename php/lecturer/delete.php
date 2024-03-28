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
if ($role != "admin") {
    // They shouldn't have been able to access this file without
    //  these permissions, so log them out just incase.
    DestroySession();
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
// Checks if the passed userID belongs to a lecturer
$role = GetUserRole($_POST['userID']);
// This will also throw an error if the user's role is a student
//  even though an admin has edit permissions for them,
//  but there is a seperate file for deleting a student.
if ($role != "lecturer") die(json_encode(array(
    "type" => "error",
    "msg" => "You do not have permission to delete this user"
)));
unset($role);

if (!DeleteUser($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to delete the lecturer"
)));

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully deleted the lecturer"
)));

?>