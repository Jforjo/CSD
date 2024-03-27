<?php
require_once('php/connection.php');
function validateUser($email, $password) {
    $sql = "CALL GetUserFromEmail(:email);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    if ($data === null || $data === false) return false;
    if (!password_verify($password, $data['password'])) return false;
   return$data;
}

if (!isset($_POST['email'])) {
    header("Location: login.php?e=1");
    exit;
}
if (!filter_var($_POST ['email'], FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?e=2");
    exit;
}

if (!isset($_POST['password'])) {
    header("Location: login.php?e=3");
    exit;
}
$password = $_POST['password'];
if (strlen($password) <8){
    header("Location: login.php?e=4");
    exit;
}

if (preg_match('/[a-z]/', $password) == 0) {
    header("Location: login.php?e=5");
    exit;
}

if (preg_match('/[A-Z]/', $password) == 0) {
    header("Location: login.php?e=6");
    exit;
}

if (preg_match('/[0-9]/', $password) == 0)  {
    header("Location: login.php?e=7");
    exit;
}

if (preg_match('/[\'^£$%&*()}{@#~?!<>,|=_+¬-]/', $password) == 0) {
    header("Location: login.php?e=8");
    exit;
} 
$data = validateUser($_POST['email'], $_POST['password']);

if ($data === false) {
    header('Location: login.php?e=5');
} else {
    session_start();
    $_SESSION["userID"] = $data['userID'];
    header('Location: dashboard.php');
}

?>
