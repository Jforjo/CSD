<?php require_once('connection.php');

function CheckUserIDExists($userID) {
    $sql = "SELECT CheckUserIDExists(:userID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['exists'];
}
function CheckStudentIDExists($studentID) {
    $sql = "SELECT CheckUserIDExists(:studentID) AS 'exists';";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}
function GetUserRole($userID) {
    $sql = "CALL GetUserRole(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data['role'];
}

function GetActiveStudentCount() {
    $sql = "CALL GetActiveStudentCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
function GetPendingUserCount() {
    $sql = "CALL GetPendingUserCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}
function GetInactiveStudentCount() {
    $sql = "CALL GetInactiveStudentCount();";
    $conn = newConn();
    $data = $conn->query($sql)->fetch();
    $conn = null;
    return $data['count'];
}

function GetAllStudentsData() {
    $sql = "CALL GetAllStudentsData();";
    $conn = newConn();
    $data = $conn->query($sql)->fetchAll();
    $conn = null;
    return $data;
}
function GetStudentData($userID) {
    $sql = "CALL GetStudentData(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}
function GetLecturerData($userID) {
    $sql = "CALL GetLecturerData(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}

function EditUser($userID, $firstname, $lastname, $email) {
    $sql = "CALL EditStudentID(:userID, :firstname, :lastname, :email);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":firstname", $firstname, PDO::PARAM_STR);
    $stmt->bindValue(":lastname", $lastname, PDO::PARAM_STR);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
function EditUserState($userID, $state) {
    $sql = "CALL EditStudentID(:userID, :state);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":state", $state, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
function EditUserRole($userID, $role) {
    $sql = "CALL EditStudentID(:userID, :role);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":role", $role, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
function EditUserPassword($userID, $password) {
    $sql = "CALL EditStudentID(:userID, :password);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
function EditUserData($userID, $firstname, $lastname, $email, $state, $password) {
    $success = true;
    if (!EditUser($userID, $firstname, $lastname, $email)) $success = false;
    if (!EditUserState($userID, $state)) $success = false;
    if ($password != null) {
        if (!EditUserPassword($userID, $password)) $success = false;
    }
    return $success;
}
function EditStudentID($userID, $studentID) {
    $sql = "CALL EditStudentID(:userID, :studentID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->bindValue(":studentID", $studentID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}
function EditStudent($userID, $firstname, $lastname, $studentID, $email, $state, $password) {
    return EditUserData($userID, $firstname, $lastname, $email, $state, $password)
        && EditStudentID($userID, $studentID);
}

function DeleteUser($userID) {
    $sql = "CALL DeleteUser(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $success = $stmt->execute();
    $conn = null;
    return $success;
}

?>