<?php require_once('connection.php');

function CheckUserExists($userID) {
    $sql = "CALL CheckUserIDExists(:userID);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":userID", $userID, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    return $data;
}
function GetUserRole($userID) {
    $sql = "CALL GetUserRole();";
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

?>