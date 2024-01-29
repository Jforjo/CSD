<?php
session_start();  //start session
//auth.php - checks user login credentials


if (isset($_POST["email"]) and isset($_POST["password"]))  //if usernae and pasword HAVE been entered 
{
    include_once("php/connection.php");
    
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM `t_users` WHERE `email` = '$email'";
    $run = mysqli_query($db_connect, $sql);

    $count =  mysqli_num_rows($run);

    if ($count === 0) {
        header("Location:login.php?e=3");
        die("Username not found.");
    }
    else
    {
        $result = mysqli_fetch_assoc($run);
        if (password_verify($password, $result["password"])) {
            $_SESSION["auth"] = $result["Access_level"];  
            $_SESSION["email"] = $result["email"];  
            header("Location:index.php");  
            die("correct");
        } else {
            die("Invalid Password");
        }
    }
}
else
{ 
    die("No username or password entered");
}