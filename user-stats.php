<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
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
    <section class="welcome-section">
        <h2 class="welcome-message">Stats for Chris</h2>
    </section>
    <div id="chart-container">
        <canvas id="chart"></canvas>
    </div>
    <div class="user-scores">
        <p>Total Points: 100</p>
        <p>Average Score: 60%</p>
    </div>
    <div class="col-md-6 offset-md-3">
        <div class="past-tests">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Test Name</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Date Completed</th>
                        <th scope="col">Score</th>
                        <th scope="col">Percentage</th>
                        <th scope="col">Points Earned</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Maths Quiz 1</td>
                        <td>Maths</td>
                        <td>31/01/2024</td>
                        <td>10/10</td>
                        <td>100%</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td>Maths Quiz 2</td>
                        <td>Maths</td>
                        <td>31/01/2024</td>
                        <td>17/20</td>
                        <td>85%</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td>Physics Test</td>
                        <td>Science</td>
                        <td>31/01/2024</td>
                        <td>42/50</td>
                        <td>84%</td>
                        <td>100</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        var ctx = document.getElementById('chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar', // specify the type of chart
            data: {
                labels: ['Maths Quiz 1', 'Maths Quiz 2', 'Physics Test'],
                datasets: [{
                    label: 'Percentage',
                    data: [100, 85, 84],
                    backgroundColor: [
                        'darkred',
                        'darkred',
                        'darkred'
                    ],
                    borderColor: [
                        'darkred',
                        'darkred',
                        'darkred'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>