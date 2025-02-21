<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<body>
    
    <nav>
        <a href=".././index.php">Domov</a>
        <a href="./trzby.php">Trzby</a>
        <a href="./peakHours.php">Peak hours</a>
        <a href="./avarageOrder.php">Avarage Order</a>
    </nav>
    
</body>
    <h1>Avarage Order</h1>

    <canvas id="aoChart" width="1300" height="650"></canvas>

<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Spojenie s databázou zlyhalo: " . $conn->connect_error);
}

$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(time), '%Y-%m') AS mesiac, 
    SUM(price * quantity) / COUNT(DISTINCT order_id) AS priemerna_hodnota
    FROM order_products_data 
    GROUP BY mesiac";

$result = $conn->query($sql);

// Polia na ukladanie dát
$months = [];
$average_order_values = [];

while ($row = $result->fetch_assoc()) {
    $months[] = $row['mesiac'];  
    $average_order_values[] = round($row['priemerna_hodnota'], 2);  
}

$conn->close();
?>

<script>
var ctx = document.getElementById('aoChart').getContext('2d');
var aoChart = new Chart(ctx, {
    type: 'bar', 
    data: {
        labels: <?php echo json_encode($months); ?>, //x-ová os
        datasets: [{
            label: 'Priemerná hodnota objednávky (€)',
            data: <?php echo json_encode($average_order_values); ?>, // y-ová os
            borderColor: 'rgb(238, 8, 0)',  
            backgroundColor: 'rgba(249, 32, 8, 0.88)',  
            fill: true 
        }]
    },
    options: {
        responsive: false,  // Fixné rozmery 
        maintainAspectRatio: false,  // fixná veľkosť
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Mesiac'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Priemerná hodnota objednávky (€)'
                },
                beginAtZero: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Priemerná hodnota objednávky po mesiacoch',  
                font: {
                    size: 16
                }
            },
            legend: {
                position: 'right', 
            }
        }
    }
});
</script>
    
</body>
</html>