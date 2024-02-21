<?php session_start();
// Checks if they are logged in
if (!isset($_SESSION['userID'])) header("Location: /admin");
require_once('dbfuncs.php');
// Checks if their session id is valid
if (!CheckUserExists($_SESSION['userID'])) header("Location: /admin");
// Checks if they have the correct permissions
$role = GetUserRole($_SESSION['userID']);
if ($role != "admin") header("Location: /admin");
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
// Checks if the passed userID belongs to a lecturer
$role = GetUserRole($_POST['userID']);
// This will also throw an error if the user's role is a student
// even though an admin has edit permissions for them,
// but there is a seperate file for getting a student's data.
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
exit(json_encode($lecturerData));

?>