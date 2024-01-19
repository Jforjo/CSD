<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Page</title>
    <!--<link rel="stylesheet" href="assets/css/style.css">-->
</head>
<style>
    .question
     {
        text-align: center;
        margin-bottom: 20px;
    }

    .answers
    {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        width: 50%;
    }
    .answer
    {
        border: 1px solid black;
        padding: 10px;
        text-align: center;
    }
    .answer:hover
    {
        background-color: #e6e6e6;
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
    <p>Subject<p>
    <p>Test Name<p>

    <div class="question">
        <p>Question text box</p>
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