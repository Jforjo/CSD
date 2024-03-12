<?php
defined('APP_RAN') or header("Location: /logout");

session_start();
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);

if (isset($_POST['jsfetch'])) {
    die(json_encode(array(
        "type" => "refresh"
    )));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
</head>
<body style="background-color:#0f0f0f;display:flex;justify-content:center;align-items:center;flex-direction:column;">
    <h1 style="color:#a3a3a3;">Successfully Logged Out</h1>
</body>
</html>