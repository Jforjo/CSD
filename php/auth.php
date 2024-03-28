<?php
require_once('connection.php');
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
    return $data;
}

if (!isset($_POST['email'])) {
    header("Location: /login?e=1");
    exit;
}
if (!filter_var($_POST ['email'], FILTER_VALIDATE_EMAIL)) {
    header("Location: /login?e=2");
    exit;
}

if (!isset($_POST['password'])) {
    header("Location: /login?e=3");
    exit;
}
$password = $_POST['password'];
if (strlen($password) < 8){
    header("Location: /login?e=4");
    exit;
}
if (preg_match('/[a-z]/', $password) == 0) {
    header("Location: /login?e=5");
    exit;
}
if (preg_match('/[A-Z]/', $password) == 0) {
    header("Location: /login?e=6");
    exit;
}
if (preg_match('/[0-9]/', $password) == 0)  {
    header("Location: /login?e=7");
    exit;
}
if (preg_match('/[\'^£$%&*()}{@#~?!<>,|=_+¬-]/', $password) == 0) {
    header("Location: /login?e=8");
    exit;
}
$data = validateUser($_POST['email'], $_POST['password']);

if ($data === false) {
    header('Location: /login?e=5');
    exit;
}
if ($data['state'] != 'active') {
    header('Location: /login?e=5');
    exit;
}
if ($data['role'] == 'student') {
    session_start();
    $_SESSION["userID"] = $data['userID'];
    header('Location: /dashboard');
    exit;
} else if ($data['role'] == 'lecturer' || $data['role'] == 'admin') {
    session_start();
    $_SESSION["userID"] = $data['userID'];
    header('Location: /admin');
    exit;
} else {
    header('Location: /login?e=5');
    exit;
}

?>
