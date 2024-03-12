<?php
$error = null;
if (isset($_POST['email']) && isset($_POST['password'])) {
    require_once('php/connection.php');
    $sql = "CALL GetUserFromEmail(:email);";
    $conn = newConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":email", $_POST['email'], PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    $conn = null;
    if ($data == false) {
        $error = "Incorrect Email";
    } else if ($data['state'] !== "active") {
        $error = "Account isn't active";
    } else if (password_verify($_POST['password'], $data['password'])) {
        session_start();
        $_SESSION['userID'] = $data['userID'];
        header("Location: /admin");
    } else {
        $error = "Incorrect Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style="display: flex; justify-content: center; align-items: center; flex-direction: column; margin: 0; height: 100vh;">
    <?php if ($error != null) { ?>
    <h3 style="margin-bottom: 16px;">Error: <?php echo $error; ?></h3>
    <?php } ?>
    <form method="POST">
        <fieldset style="display: flex; flex-direction: column; gap: 8px;">
            <legend>Login</legend>
            <label style="display: flex; flex-direction: column;">
                Email:
                <input type="email" name="email" placeholder="Email" required style="width: auto;">
            </label>
            <label style="display: flex; flex-direction: column;">
                Password:
                <input type="password" name="password" placeholder="Password" required style="width: auto;">
            </label>
            <button type="submit">Submit</button>
        </fieldset>
    </form>
</body>
</html>