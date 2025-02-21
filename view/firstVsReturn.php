<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <nav>
        <a href=".././index.php">Domov</a>
        <a href="./trzby.php">Trzby</a>
        <a href="./peakHours.php">Peak hours</a>
        <a href="./avarageOrder.php">Avarage Order</a>
        <a href="./firstVsReturn.php">First VS Return </a>
    </nav>

    <h1>First VS Return</h1>


<?php

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Spojenie s databázou zlyhalo: " . $conn->connect_error);
}

// Dopyt pre first time buyers
$sql_first_time_buyers = "
    SELECT 
        DATE_FORMAT(FROM_UNIXTIME(time), '%Y-%m') AS mesiac,
        COUNT(DISTINCT user_id) AS pocet_first_time_buyers,
        SUM(price * quantity) AS celkove_trzby_first_time
    FROM order_products_data
    WHERE user_id IN (
        SELECT user_id
        FROM order_products_data
        GROUP BY user_id
        HAVING COUNT(DISTINCT order_id) = 1
    )
    GROUP BY mesiac;
";

// Dopyt pre returning customers
$sql_returning_customers = "
    SELECT 
        DATE_FORMAT(FROM_UNIXTIME(time), '%Y-%m') AS mesiac,
        COUNT(DISTINCT user_id) AS pocet_returning_customers,
        SUM(price * quantity) AS celkove_trzby_returning
    FROM order_products_data
    WHERE user_id IN (
        SELECT user_id
        FROM order_products_data
        GROUP BY user_id
        HAVING COUNT(DISTINCT order_id) > 1
    )
    GROUP BY mesiac;
";


$result_first_time_buyers = $conn->query($sql_first_time_buyers);
$result_returning_customers = $conn->query($sql_returning_customers);


$months = [];
//$first_time_buyers = [];
$first_time_sales = [];
//$returning_customers = [];
$returning_sales = [];

while ($row = $result_first_time_buyers->fetch_assoc()) {
    $months[] = $row['mesiac'];
    $first_time_buyers[] = $row['pocet_first_time_buyers'];
    $first_time_sales[] = $row['celkove_trzby_first_time'];
}

while ($row = $result_returning_customers->fetch_assoc()) {
    $returning_customers[] = $row['pocet_returning_customers'];
    $returning_sales[] = $row['celkove_trzby_returning'];
}

$conn->close();
?>


<canvas id="salesChart" width="1300" height="650"></canvas>

<script>
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'bar',  
        data: {
            labels: <?php echo json_encode($months); ?>,  // x-ová os
            datasets: [
                {
                    label: 'First Time Buyers (€)',
                    data: <?php echo json_encode($first_time_sales); ?>,  // Tržby First Time Buyers
                    borderColor: 'rgb(251, 2, 2)',
                    backgroundColor: 'rgb(239, 12, 12)',
                    fill: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Returning Customers (€)',
                    data: <?php echo json_encode($returning_sales); ?>,  // Tržby Returning Customers
                    borderColor: 'rgb(0, 255, 110)',
                    backgroundColor: 'rgb(122, 255, 99)',
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: false,  // Fixné rozmery 
            maintainAspectRatio: false,  // fixná veľkosť
            scales: {
                y: {
                    type: 'linear',
                    title: {
                        display: true,
                        text: 'Tržby (€)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Mesiac'
                    }
                }
            },
        plugins: {
            title: {
                display: true,
                text: 'First VS Return',  
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