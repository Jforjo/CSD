<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Stats</title>
    <style>
    *
    {
        font-family: Arial, Helvetica, sans-serif;
        color: white;
        background-color: #0f0f0f;
    }
    
    nav ul 
    {
        display: flex;
        list-style-type: none;
        justify-content: center;
    }

    nav ul li
    {
        margin: 0 20px;
    }

    #chart-container
    {
        width: 400px;
        height: 400px;
        margin: 0 auto;
        background-color: #555555;
        
    }

    .user-scores
    {
        width: 400px;
        margin: 0 auto;
        text-align: center;
    }

    .past-tests
    {
        padding: 10px;
        text-align: center;
    }

    .tests
    {
        border: 1px solid white;
        padding: 10px;
        margin: 10px;
        width: 80%;
        height: 50px;
        margin: 10px auto;
        font-size: 20px;
    }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Stats</a></li>
                <li><a href="#">Leaderboards</a></li>
            </ul>
        </nav>
    </header>
    <div id="chart-container">

    </div>
    <div class="user-scores">
        <p>Total Points: 37</p>
        <p>Average Score: 13.5</p>
    </div>
    <div class="past-tests">
    
        <div class="tests">Test 1</div>
        <div class="tests">Test 2</div>
    </div>
</body>
</html>