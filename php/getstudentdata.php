<?php session_start();
// Checks if they are logged in
if (!isset($_SESSION['userID'])) header("Location: /admin");
require_once('dbfuncs.php');
// Checks if their session id is valid
if (!CheckUserExists($_SESSION['userID'])) header("Location: /admin");
// Checks if they have the correct permissions
$role = GetUserRole($_SESSION['userID']);
if (!($role == "lecturer" || $role == "admin")) header("Location: /admin");
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