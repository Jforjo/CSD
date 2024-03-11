<?php
require_once('php/connection.php');
function CreateUser($firstname,$lastname,$email, $password) {
    $sql = "CALL CreateUser(:ID,:firstname,:lastname,:email,:password);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    
    $stmt->bindValue(":ID",bin2hex(random_bytes(16)), PDO::PARAM_STR);
    $stmt->bindValue(":firstname", $firstname, PDO::PARAM_STR);
    $stmt->bindValue(":lastname", $lastname, PDO::PARAM_STR);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $stmt->bindValue(":password", password_hash($password,PASSWORD_DEFAULT), PDO::PARAM_STR);
    $success=$stmt->execute();
   
    $conn = null;
    
    
   return$success;
}

$data = CreateUser($_POST["firstname"],$_POST["lastname"],$_POST['email'], $_POST['password']);

if ($data === false) {
    header('Location: signup.php?e=5');
} else {
  
    header('Location: login.php');
}

?>

    