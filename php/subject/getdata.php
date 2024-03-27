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

// Checks if the subject's ID to retireve data has been passed to this file
if (!isset($_POST['subjectID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST Subject ID"
)));
// Checks if the passed subjectID belongs to a valid subject
if (!CheckSubjectIDExists($_POST['subjectID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Subject ID does not exist"
)));

// Retrieve the subject's data from the database
$subjectData = GetSubjectData($_POST['subjectID']);
// Checks if, for some reason, FALSE was returned
if ($subjectData === false) die(json_encode(array(
    "type" => "error",
    "msg" => "An unknown error occurred while retrieving the subject's data"
)));
// Return the subject's data
exit(json_encode(array(
    "type" => "data",
    "data" => $subjectData
)));

?>