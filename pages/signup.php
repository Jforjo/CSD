<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="stylesheet.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>
<body>

<form method="POST" action="../php/createuser.php">
<h2>Signup</h2>
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

<label for="firstname">firstname:</label>
<input name="firstname" id="firstname" type="text" required>

<label for="lastname">lastname:</label>
<input name="lastname" id="lastname" type="text" required>

<label for="email">Email:</label>
<input name="email" id="email" type="email" placeholder="eg test@test.com" required>

<label for="password">Password:</label>
<input name="password" id="password" type="password" placeholder="password" required>


<button type="submit">Signup</button>

</form>

</body>
</html>


