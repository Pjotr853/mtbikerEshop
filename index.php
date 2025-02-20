<!DOCTYPE html>
<html>
<body>

<h1>hello world</h1>

<?php
echo "Vitaj v mojom e-shope!";

// dáta potrebné k pripojeniu k databáze
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mtbiker';

// Vytvorenie pripojenia s databazou mtbiker
$conn = new mysqli($host, $user, $password, $dbname);

// Kontrola pripojenia k databaze
if ($conn->connect_error) {
    die("Spojenie zlyhalo: " . $conn->connect_error);
}
else{
    echo "Spojenie sa podarilo!"; 
}

// sql dotaz na ziskanie dat
$sql = "
    SELECT * 
    FROM order_products_data 
    LIMIT 10;";

$result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Výpis údajov
        echo "<table border='1'>";
        echo "<tr><th>Order ID</th><th>Product ID</th><th>Category ID</th><th>Quantity</th><th>Price</th><th>Time</th><th>User ID</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['product_id']}</td>
                    <td>{$row['category_id']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['price']}</td>
                    <td>" . date('Y-m-d H:i:s', $row['time']) . "</td>
                    <td>{$row['user_id']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Žiadne údaje na zobrazenie.";
    }
    
    // Zatvorenie pripojenia
    $conn->close();
?>

</body>
</html>
