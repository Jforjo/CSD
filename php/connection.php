<?php
require_once("config.php");

function newConn(){
    $conn = "";
   
    try {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        //set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
    catch(PDOException $e) {
        $conn = null;
        die("Connection failed: " . $e->getMessage());
    }
}
  
?>