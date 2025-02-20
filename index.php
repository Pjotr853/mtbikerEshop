<!DOCTYPE html>
<html>
<body>

    <nav>
        <a href="./index.php">Domov</a>
        <a href="./view/shop.php">Obchod</a>
        <a href="/product/1">Produkt 1</a>
        <a href="/product/2">Produkt 2</a>
    </nav>

    <h1>Vitaj v e-shope MTBiker!</h1>

    <form method="post"> 
        <label>Zadaj ID produktu:</label>
        <input type="number" name="product_id" id="product_id" required>
        <button type="submit">Pridať</button>
    </form>
    <h3>Obsah objednávky:</h3>

    
<ul>
    <?php

    /*************************************zachztenie product id y form a ulozenie do premennej product_id*************  */
    session_start(); // Spustenie session, umožňuje uchovávať údaje medzi viacerými požiadavkami 
    //$_SESSION['kosik'] = []; // Vyprázdni celé pole

    /*if (!isset($_SESSION['kosik'])) { //kontroluje, či už existuje pole kosik
        $_SESSION['kosik'] = [];      //ak nie vztvori pole kosik
    }
*/  $zmena=false;
    if ($_SERVER["REQUEST_METHOD"] == "POST") { //spracovanie post poziadavky z formulara
        if (!empty($_POST['product_id']) && is_numeric($_POST['product_id'])) { //kontrolujem ci kosik je prazdny a ma ciselny vstup
            $product_id = intval($_POST['product_id']); //beriem vstup pola s id product_id a konvert na cele cislo 
            //$_SESSION['kosik'][] = $product_id; //vkladam product_id na koniec pola kosik
        
    

    /***************************************pripojenie k databaze********************************************* */
        // Pripojenie k databáze
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $dbname = 'mtbiker';

        // Vytvorenie pripojenia k databáze
        $conn = new mysqli($host, $user, $password, $dbname);

        // Kontrola pripojenia k databaze
        if ($conn->connect_error) {
            die("Spojenie s databazou zlyhalo: " . $conn->connect_error);
        }
        

        // SQL dotaz na kontrolu existencie produktu v databáze
        $sql = "SELECT * FROM order_products_data WHERE product_id = $product_id LIMIT 1";
        $result = $conn->query($sql);

        

        if ($result->num_rows == 1) {
            
            $zmena=false;  
            if (!isset($_SESSION['kosik'])) { //kontroluje, či už existuje pole kosik
                $_SESSION['kosik'] = [];      //ak nie vztvori pole kosik
            }
            // Produkt existuje
            $product = $result->fetch_assoc();
            $zmena=true;
         if($zmena==true){ 
            
            $found = false;
            // Prechádzame produkty v košíku a ak už existuje, zvyšujeme množstvo
            foreach ($_SESSION['kosik'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    $item['quantity'] += 1; 
                    $found = true;
                    echo "<p>foreach</p>";
                    break;
                }
            }
            
            // Ak produkt neexistuje v košíku, pridáme ho
            if ($found ==false) {
                echo "<p>nenasiel sa</p>";
                $_SESSION['kosik'][] = [
                    'product_id' => $product_id,
                    'category_id' => $product['category_id'],
                    'quantity' => 1, 
                ];
            }
        }
            
            echo "<p>Produkt ID: $product_id, Kategória: {$product['category_id']} bol pridaný do košíka.</p>";
        } else {
            // Produkt neexistuje v databáze
            echo "<p>Produkt s ID $product_id nie je v databáze.</p>";
        }
    

        // Zatvorenie pripojenia
        $conn->close();
        }
    }

    ?>
</ul>



<ul>
    <?php
    /***************************************vzpis kosiku************************************** */
    if (!empty($_SESSION['kosik'])) { // Ak košík nie je prázdny, vypíšeme jeho obsah
        foreach ($_SESSION['kosik'] as $item) {
            echo "<li>Produkt ID: {$item['product_id']}, Kategória: {$item['category_id']}, Počet: {$item['quantity']}</li>";
        }
    } else {
        echo "<p>Košík je prázdny.</p>";
    }
    ?>
</ul>


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
