function displayChart(testNames, percentages)
{
    var ctx = document.getElementById('chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: testNames,
                datasets: [{
                    label: 'Percentage',
                    data: percentages,
                    backgroundColor: ['red'],
                    borderColor: ['red'],
                    borderWidth: 1
                }]
            },
            options: 
            {
                plugins:
                {
                    legend:
                    {
                        labels:
                        {
                            color: 'white'
                        }
                    }
                },
                scales:
                {
                    x:
                    {
                        ticks:
                        {
                            color: 'white'
                        }
                    },
                    y: 
                    {
                        beginAtZero: false,
                        ticks:
                        {
                            color: 'white',
                            stepSize: 25
                        }
                    }
                }
            }
        });
}

    window.onload = function() {
        fetch('user-stats.php?action=getTestData')
        .then(response => response.json())
        .then(data => {
            var percentages = data.percentages.map(parseFloat);
            displayChart(data.testNames, percentages);
        })
        .catch(error => {
            console.error('Error:', error);
            var errorMessageDiv = document.getElementById('errorMessage');
            errorMessageDiv.textContent = 'Failed to load graph';
            errorMessageDiv.style.display = 'block';
        });
    }