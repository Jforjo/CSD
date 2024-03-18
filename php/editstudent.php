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
    "msg" => "You do not have permission to edit this user"
)));
unset($role);

// Checks if the 'firstname' value was passed to the file
if (!isset($_POST['firstname'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST firstname"
)));
// Checks if the 'lastname' value was passed to the file
if (!isset($_POST['lastname'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST lastname"
)));
// Checks if the 'studentID' value was passed to the file
if (!isset($_POST['studentID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST studentID"
)));
// Checks if the 'email' value was passed to the file
if (!isset($_POST['email'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST email"
)));
// Checks if the 'state' value was passed to the file
if (!isset($_POST['state'])) die(json_encode(array(
    "type" => "error",
    "msg" => "Invalid POST state"
)));

// Checks if the passed studentID belongs to a valid student
if (!CheckStudentIDExists($_POST['userID'])) die(json_encode(array(
    "type" => "error",
    "msg" => "A student with that ID does not exists"
)));

// Checks if the 'firstname' is NULL or empty
$firstname = $_POST['firstname'];
if (ctype_space($firstname) || $firstname == '') die(json_encode(array(
    "type" => "error",
    "input" => "firstname",
    "msg" => "Firstname cannot be NULL or empty"
)));
// Checks if the 'lastname' is NULL or empty
$lastname = $_POST['lastname'];
if (ctype_space($lastname) || $lastname == '') die(json_encode(array(
    "type" => "error",
    "input" => "lastname",
    "msg" => "Lastname cannot be NULL or empty"
)));
// Checks if the 'studentID' is NULL or empty
$studentID = $_POST['studentID'];
if (ctype_space($studentID) || $studentID == '') die(json_encode(array(
    "type" => "error",
    "input" => "studentID",
    "msg" => "Student ID cannot be NULL or empty"
)));
// Checks if the 'email' is NULL or empty
$email = $_POST['email'];
if (ctype_space($email) || $email == '') die(json_encode(array(
    "type" => "error",
    "input" => "email",
    "msg" => "Email cannot be NULL or empty"
)));
// Checks if the 'state' is NULL or empty
$state = $_POST['state'];
if (ctype_space($state) || $state == '') die(json_encode(array(
    "type" => "error",
    "input" => "state",
    "msg" => "state cannot be NULL or empty"
)));

// Checks the lenth of the `firstname` string
if (strlen($firstname) > 36) die(json_encode(array(
    "type" => "error",
    "input" => "firstname",
    "msg" => "Firstname is too long"
)));
// Checks the lenth of the `lastname` string
if (strlen($lastname) > 36) die(json_encode(array(
    "type" => "error",
    "input" => "lastname",
    "msg" => "Lastname is too long"
)));
// Checks the lenth of the `studentID` string
if (strlen($studentID) > 16) die(json_encode(array(
    "type" => "error",
    "input" => "studentID",
    "msg" => "Student ID is too long"
)));
// Checks the lenth of the `email` string
if (strlen($email) > 64) die(json_encode(array(
    "type" => "error",
    "input" => "email",
    "msg" => "Email is too long"
)));

// Checks if the 'firstname' only contains letters (a-z and A-Z)
if (preg_match('/^[a-zA-Z]+$/', $firstname) == 0) die(json_encode(array(
    "type" => "error",
    "input" => "firstname",
    "msg" => "Firstname may only contain letters"
)));
// Checks if the 'lastname' only contains letters (a-z and A-Z)
if (preg_match('/^[a-zA-Z]+$/', $lastname) == 0) die(json_encode(array(
    "type" => "error",
    "input" => "lastname",
    "msg" => "Lastname may only contain letters"
)));
// Checks if the 'email' is in a valid format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die(json_encode(array(
    "type" => "error",
    "input" => "email",
    "msg" => "Invalid email"
)));
// Checks if the 'state' is valid
if ($state != "active" && $state != "inactive" && $state != "pending") die(json_encode(array(
    "type" => "error",
    "input" => "state",
    "msg" => "Invalid state"
)));

$password = null;
if (isset($_POST['password']) && !ctype_space($_POST['password']) && $_POST['password'] != '') {
    $password = $_POST['password'];
    // Checks the lenth of the `password` string
    if (strlen($password) < 8) die(json_encode(array(
        "type" => "error",
        "input" => "password",
        "msg" => "Password must be at least 8 characters long"
    )));
    // Checks if the 'password' contains at least 1 lowercase letter
    if (preg_match('/[a-z]/', $password) == 0) die(json_encode(array(
        "type" => "error",
        "input" => "password",
        "msg" => "Password must contain at least 1 lowercase letter"
    )));
    // Checks if the 'password' contains at least 1 uppercase letter
    if (preg_match('/[A-Z]/', $password) == 0) die(json_encode(array(
        "type" => "error",
        "input" => "password",
        "msg" => "Password must contain at least 1 uppercase letter"
    )));
    // Checks if the 'password' contains at least 1 number
    if (preg_match('/[0-9]/', $password) == 0) die(json_encode(array(
        "type" => "error",
        "input" => "password",
        "msg" => "Password must contain at least 1 number"
    )));
    // Checks if the 'password' contains at least 1 special character
    if (preg_match('/[\'^£$%&*()}{@#~?!<>,|=_+¬-]/', $password) == 0) die(json_encode(array(
        "type" => "error",
        "input" => "password",
        "msg" => "Password must contain at least 1 special character"
    )));
}

if (!EditStudent($_POST['userID'], $firstname, $lastname, $studentID, $email, $state, $password)) die(json_encode(array(
    "type" => "error",
    "msg" => "Failed to edit the student"
)));

exit(json_encode(array(
    "type" => "success",
    "msg" => "Successfully edited the student"
)));

?>