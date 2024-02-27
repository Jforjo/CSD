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

$data = validateUser($_POST['email'], $_POST['password']);

if ($data === false) {
    header('Location: login.php?e=5');
} else {
    session_start();
    $_SESSION["userID"];
    header('Location: dashboard.php');
}

?>
