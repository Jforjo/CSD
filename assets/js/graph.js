var chart; //Stores the chart to allow it to be refreshed when the toggle is clicked
function displayChart(testNames, percentages, beginAtZero)
{
    var ctx = document.getElementById('chart').getContext('2d');
    if (chart) {
        chart.destroy();
    }
        chart = new Chart(ctx, {
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
                        beginAtZero: beginAtZero,
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
    var toggleYAxis = document.getElementById('toggleYAxis');

    function loadChart() {
        fetch('/pages/student/user-stats.php?action=getTestData')
            .then(response => response.json())
            .then(data => {
                var percentages = data.percentages.map(parseFloat);
                displayChart(data.testNames, percentages, toggleYAxis.checked);
            })
            .catch(error => {
                console.error('Error:', error);
                var errorMessageDiv = document.getElementById('errorMessage');
                errorMessageDiv.textContent = 'Failed to load graph';
                errorMessageDiv.style.display = 'block';
            });
    }

    toggleYAxis.addEventListener('change', loadChart);

    loadChart();
}