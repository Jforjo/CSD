<?php
// require_once('php/connection.php');
// function validateUser($email, $password) {
//     $sql = "CALL GetUserFromEmail(:email);";
//     $conn = newConn();
//     $stmt = $conn->prepare($sql);
//     $stmt->bindValue(":email", $email, PDO::PARAM_STR);
//     $stmt->execute();
//     $data = $stmt->fetch();
//     $conn = null;
//     if ($data === null || $data === false) return false;
//     if (!password_verify($password, $data['password'])) return false;
//     return array(
//         "userID" => $data['userID'],
//         "state" => $data['state']
//     );
// }

// $data = validateUser($_POST['email'], $_POST['password']);

// if ($data === false) {
//     header('Location: error.html');
// } else {
//     header('Location: success.html');
// }

// ?>

// <?php
// session_start();  //start session
// //auth.php - checks user login credentials


// if (isset($_POST["email"]) and isset($_POST["password"]))  //if usernae and pasword HAVE been entered 
// {
    
    
//     $email = $_POST["email"];
//     $password = $_POST["password"];

//     $sql = "SELECT * FROM `t_users` WHERE `email` = '$email'";
//     $run = mysqli_query($db_connect, $sql);

//     $count =  mysqli_num_rows($run);

//     if ($count === 0) {
//         header("Location:login.php?e=3");
//         die("Username not found.");
//     }
//     else
//     {
//         $result = mysqli_fetch_assoc($run);
//         if (password_verify($password, $result["password"])) {
//             $_SESSION["auth"] = $result["Access_level"];  
//             $_SESSION["email"] = $result["email"];  
//             header("Location:index.php");  
//             die("correct");
//         } else {
//             die("Invalid Password");
//         }
//     }
// }
// else
// { 
//     die("No username or password entered");
// } 
header("Location:dashboard it will not work for now I will pull request on github ");