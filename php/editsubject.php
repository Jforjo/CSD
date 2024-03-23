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
if ($role != "admin") {
    // They shouldn't have been able to access this file without
    //  these permissions, so log them out just incase.
    DestroySession();
    die(json_encode(array(
        "type" => "refresh"
    )));
}
unset($role);

// Checks if the 'name' value was passed to the file
if (!isset($_POST['name'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST name"
)));

// Checks if the 'name' is NULL or empty
$name = $_POST['name'];
if (ctype_space($name) || $name == '') die(json_encode(array(
    "type" => "error",
    "input" => "name",
    "msg" => "Name cannot be NULL or empty"
)));

if (!EditSubject($_POST['subjectID'], $name)) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to edit the subject"
)));

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully edited the subject"
)));

?>