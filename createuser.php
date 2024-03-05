<?php

session_start();
if(isset($_SESSION["auth"])){  

if($_SESSION["auth"] == "2")  
{
 
}
else
{
    header("Location: login.php?e=4");
 die("You are not Signed in.");  }  
}

$sql = "UPDATE
`users`
SET
`firstname` = '$f_name',
`lastname` = '$l_name',
`email` = '$email',
`password` = '$password'
WHERE
`users`.`userid` = $uid";

if(mysqli_query($db_connect,$sql))
{
    header("Location: login.php");
}

echo mysqli_error($db_connect);

header("Location: signup.php");

if(isset($_GET["userid"]))
{
    require_once("includes/_connect.php");//database connection
    $uid = $_GET["userid"];
    $sql = "SELECT * FROM `users` WHERE `userID` = '$uid' LIMIT 1"; //SQL Query
    $query = mysqli_query($db_connect,$sql);//get the results
    $result = mysqli_fetch_assoc($query);


}

else { die("No user selected!");}
?>
<h2>Edit user:       <?php echo $result["email"]; ?>              </h2>


    