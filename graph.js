var ctx = document.getElementById('chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Maths Quiz 1', 'Maths Quiz 2', 'Physics Test', 'Test', 'Test', 'Test', 'Test', 'Test'],
                datasets: [{
                    label: 'Percentage',
                    data: [100, 85, 84, 50, 57, 81, 73, 65],
                    backgroundColor: 
                    [
                        'red'
                    ],
                    borderColor: 
                    [
                        'red'
                    ],
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