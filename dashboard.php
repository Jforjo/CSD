<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
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
    <section>
        <h2 class="section-title">Tests to complete</h2>
        <div class="test-box">
            <h3>Test Name</h3>
            <p>Test Subject</P>
        </div>
    </section>
    <section>
        <h2 class="section-title">Completed Tests</h2>
        <div class="test-box">
            <h3>Test Name</h3>
            <p>Completed: 22/01/2024</p>
            <p>Score: 80%</p>
            <p>250 points</p>
        </div>
    </section>
</body>
</html>