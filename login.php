<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="adminn.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>

<form method= "POST"action="_auth.php">
<h2>Login</h2>
<label for="email">Email:</label>
<input name="email" id="email" type="email" placeholder="eg test@test.com" required>

<label for="password">Password:</label>
<input name="password" id="password" type="password" placeholder="password" required>

<button type="submit"> You are logged in!</button>

</form>

<?php 
if(isset($_GET["e"]))
{
    
if ($_GET["e"]==5)
{
    echo "<span class= 'error'>Invalid Username or Password.</span>";
}

if ($_GET["e"]==6)
{
    echo "<span class= 'error'>No username or Password.</span>";
}

if ($_GET["e"]==7)
{
    echo "<span class= 'error'>You are not logged in.</span>";
}



}

?>

</body>
</html>