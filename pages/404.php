<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #181818;
        }
        section {
            padding: 32px;
            border-radius: 16px;
            background-color: #2c2c2c;
        }
        section > h1 {
            color: #fff;
            margin-bottom: 16px;
        }
        section > p {
            color: #a3a3a3;
        }
    </style>
</head>
<body>
    <section>
        <h1>This page does not exist</h1>
        <p><?php echo '/' . $_GET['url']; ?></p>
    </section>
</body>
</html>