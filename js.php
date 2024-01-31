<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
header('Content-Type: application/json');

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charts.js Example</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Create canvas element for the chart -->
    <canvas id="myChart" width="400" height="200"></canvas>

    <script>
        // Use AJAX to fetch data from PHP script
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
                createChart(data);
            }
        };
        xmlhttp.open("GET", "process1.php", true);
        xmlhttp.send();

        // Function to create the chart using Charts.js
        function createChart(data) {
            var ctx = document.getElementById('myChart').getContext('2d');
            var datasets = [];

            // Iterate through the data and create datasets for each site
            for (var site in data) {
                var siteData = data[site];
                datasets.push({
                    label: site,
                    data: siteData.map(item => item.count_data),
                    borderColor: getRandomColor(),
                    borderWidth: 2,
                    fill: false,
                });
            }

            var myChart = new Chart(ctx, {
                type: 'line', // You can change the chart type (bar, line, etc.)
                data: {
                    labels: siteData.map(item => monthNames[item.month - 1]),
                    datasets: datasets,
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        }

        // Function to generate random color for chart lines
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Array to map month numbers to month names
        var monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
    </script>
</body>

</html>