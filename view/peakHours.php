<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <nav>
        <a href=".././index.php">Domov</a>
        <a href="./trzby.php">Trzby</a>
        <a href="./peakHours.php">Peak hours</a>
        <a href="./product/2">Produkt 2</a>
    </nav>
    <h1>Peak Hours</h1>
    
</body>

<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Spojenie s databázou zlyhalo: " . $conn->connect_error);
}

$sql = "SELECT DAYOFWEEK(FROM_UNIXTIME(time)) AS den, 
            HOUR(FROM_UNIXTIME(time)) AS hodina,
            COUNT(*) AS order_count
        FROM order_products_data
        GROUP BY den, hodina
        ORDER BY den, hodina";

$result = $conn->query($sql);

$days_of_week = ["Nedeľa", "Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok", "Sobota"];
$orders = array_fill(0, 7, array_fill(0, 24, 0)); // Počet objednávok pre každý deň a hodinu

while ($row = $result->fetch_assoc()) {
    $day = $row['den'] - 1;  // Deň v týždni (1 = Nedeľa od 0 až 6)
    $hour = $row['hodina'];     //0 ay 23
    $orders[$day][$hour] = $row['order_count']; // počet objednávok pre daný deň a hodinu
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribúcia objednávok podľa času</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Distribúcia objednávok počas dňa</h1>

<canvas id="ordersChart" width="1300" height="650"></canvas>

<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Spojenie s databázou zlyhalo: " . $conn->connect_error);
}

$sql = "SELECT HOUR(FROM_UNIXTIME(time)) AS hodina, COUNT(*) AS pocet_objednavok
        FROM order_products_data
        GROUP BY hodina
        ORDER BY hodina";
$result = $conn->query($sql);

$hours = [];
$order_counts = [];

while ($row = $result->fetch_assoc()) {
    $hours[] = $row['hodina'];
    $order_counts[] = $row['pocet_objednavok'];  
}

$conn->close();
?>

<script>
var ctx = document.getElementById('ordersChart').getContext('2d');
var ordersChart = new Chart(ctx, {
    type: 'bar', // Stlpcový graf
    data: {
        labels: <?php echo json_encode($hours); ?>, // x-ová os
        datasets: [{
            label: 'Počet objednávok',
            data: <?php echo json_encode($order_counts); ?>, // y-ová os
            backgroundColor: 'rgba(218, 41, 25, 0.7)',  
            borderColor: 'rgb(218, 41, 25)',  
            borderWidth: 1
        }]
    },
    options: {
        responsive: false,  // Fixné rozmery 
        maintainAspectRatio: false,  // fixná veľkosť
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Hodina dňa'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Počet objednávok'
                },
                beginAtZero: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Objednávok podľa hodín dňa',  
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



<h1>Distribúcia objednávok počas týždňa</h1>

<canvas id="ordersChart2" width="1300" height="650"></canvas>

<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Spojenie s databázou zlyhalo: " . $conn->connect_error);
}

$sql = "SELECT DAYOFWEEK(FROM_UNIXTIME(time)) AS den_tyzden,  COUNT(*) AS pocet_objednavok
        FROM order_products_data
        GROUP BY den_tyzden
        ORDER BY den_tyzden";

// vykonanie dopytu
$result = $conn->query($sql);


$dni_tyzden = ["", "Nedeľa", "Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok", "Sobota"];

$days = [];
$order_counts = [];

while ($row = $result->fetch_assoc()) {
    $days[] = $dni_tyzden[$row['den_tyzden']];  
    $order_counts[] = $row['pocet_objednavok'];  
}

$conn->close();
?>

<script>
var ctx = document.getElementById('ordersChart2').getContext('2d');
var ordersChart2 = new Chart(ctx, {
    type: 'bar', // Stĺpcový graf
    data: {
        labels: <?php echo json_encode(array_slice($dni_tyzden, 1)); ?>, //x-ová os
        datasets: [{
            label: 'Počet objednávok',
            data: <?php echo json_encode($order_counts); ?>, // y-ová os
            backgroundColor: 'rgba(218, 41, 25, 0.7)',  
            borderColor: 'rgb(218, 41, 25)',  
            borderWidth: 1
        }]
    },
    options: {
        responsive: false,  // Fixné rozmery 
        maintainAspectRatio: false,  // fixná veľkosť
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Deň v týždni'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Počet objednávok'
                },
                beginAtZero: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Objednávky podľa dni v týždni',  
                font: {
                    size: 16
                }
            },legend: {
                        position: 'right', 
                    }
                
        
        }
    }
});
</script>




    <h1>Distribúcia objednávok podľa dní a hodín</h1>
    <canvas id="orderDistributionChart" width="800" height="400"></canvas>

    <script>
        var ctx = document.getElementById('orderDistributionChart').getContext('2d');
        
        var daysOfWeek = <?php echo json_encode($days_of_week); ?>;
        var ordersData = <?php echo json_encode($orders); ?>;

        var chartData = {
            labels: daysOfWeek, // Dni v týždni (x-ová os)
            datasets: []
        };

        for (var i = 0; i < 24; i++) {
            chartData.datasets.push({
                label: i + " hodina",
                data: ordersData.map(function(day) {
                    return day[i];
                }),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            });
        }

        var orderDistributionChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Objednavky v hodino-tyzdni',  
                        font: {
                            size: 16  
                        }
                    },
                    legend: {
                        position: 'right', 
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Deň v týždni'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Počet objednávok'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>



</html>