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
    return array(
        "userID" => $data['userID'],
        "state" => $data['state']
    );
}

$data = validateUser($_POST['email'], $_POST['password']);

if ($data === false) {
    header('Location: error.html');
} else {
    header('Location: success.html');
}

?>