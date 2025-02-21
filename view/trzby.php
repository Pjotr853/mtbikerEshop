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
        <a href="./avarageOrder.php">Avarage Order</a>
    </nav>
    <h1>Dashboard</h1>
    
</body>

<?php
// Pripojenie k databáze
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

// pripojenie k databáze
$conn = new mysqli($host, $user, $password, $dbname);

// overenie pripojenia k databyke
if ($conn->connect_error) {
    die("Spojenie s databázou zlyhalo: " . $conn->connect_error);
}
//sql dopyt 
$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(time), '%Y-%m') AS month, SUM(price * quantity) AS total_sales
        FROM order_products_data
        GROUP BY month
        ORDER BY month";

$result = $conn->query($sql);

//polia na ukladanie mesiacov a trzieb
$months = [];
$sales = [];
while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];  
    $sales[] = $row['total_sales'];
}

// ukoncenie spojenia s databazou
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Tržby po mesiacoch</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <h1>Dashboard - Tržby po mesiacoch</h1>
    <canvas id="salesChart" width="1300" height="650"></canvas>

    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',  //  ciarovy graf
            data: {
                labels: <?php echo json_encode($months); ?>,  // Mesiace (x-ová os)
                datasets: [{
                    data: <?php echo json_encode($sales); ?>,  // Tržby (y-ová os)
                    label: 'Tržby (€)',
                    borderColor: 'rgb(218, 41, 25)',  
                    fill: false  
                }]
            },
            options: {
                responsive: false,  // Fixné rozmery 
                maintainAspectRatio: false,  // fixná veľkosť
                plugins: {
                    title: {
                        display: true,
                        text: 'Tržby po mesiacoch',  
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
                            text: 'Mesiac'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Tržby (€)'
                        }
                    }
                }
            }
        });
    </script>

</body>



</html>