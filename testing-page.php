<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Page</title>
    <!--<link rel="stylesheet" href="assets/css/style.css">-->
</head>
<style>
    *
    {
        font-family: Arial, Helvetica, sans-serif;
        color: white;
        background-color: #0f0f0f;
    }
    
    .question
     {
        text-align: center;
        margin-bottom: 20px;
        width: 50%;
        margin: 0 auto;
        margin-top: 90px;
    }

    .question-number
     {
        text-align: center;
        margin-bottom: 20px;
    }

    .answers
    {
        position: absolute;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        width: 50%;
        bottom: 25%;
        left: 50%;
        transform: translateX(-50%)
        
    }
    .answer
    {
        border: 1px solid white;
        padding: 10px;
        text-align: center;
    }
    .answer:hover
    {
        background-color: #666666;
    }
</style>  
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
    <div class="test-info">
        <p>Subject</p>
        <p>Test Name</p>
    </div>

    <div class="question">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>

    <div class="question-number">
        <p>Question 1/10</p>
    </div>

    <div class="answers">
        <div class="answer">Answer 1</div>
        <div class="answer">Answer 2</div>
        <div class="answer">Answer 3</div>
        <div class="answer">Answer 4</div>
    </div>
    
    
    
</body>
</html>