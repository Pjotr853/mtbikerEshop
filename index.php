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
           /* foreach ($_SESSION['kosik'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    $item['quantity'] += 1; 
                    $found = true;
                    echo "<p>foreach</p>";
                    break;
                }
            }*/
            
            // Ak produkt neexistuje v košíku, pridáme ho
           // if ($found ==false) {
              //  echo "<p>nenasiel sa</p>";
                $_SESSION['kosik'][] = [
                    'product_id' => $product_id,
                    'category_id' => $product['category_id'],
                    'quantity' => 1, 
                ];
           // }
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
//session_start();

// Inicializácia $kosik2
$kosik2 = [];

// Skontrolujeme, či existuje $_SESSION['kosik']
if (isset($_SESSION['kosik']) && !empty($_SESSION['kosik'])) {
    foreach ($_SESSION['kosik'] as $item) {
        $product_id = $item['product_id'];

        // Ak už produkt existuje v $kosik2, zvýšime jeho quantity
        if (isset($kosik2[$product_id])) {
            $kosik2[$product_id]['quantity'] += 1;
        } else {
            // Ak produkt ešte nie je v $kosik2, pridáme ho
            $kosik2[$product_id] = [
                'product_id' => $product_id,
                'category_id' => $item['category_id'],
                'quantity' => 1
            ];
        }
    }
}

// Uloženie spracovaného košíka do session
$_SESSION['kosik2'] = $kosik2;

// Výpis obsahu košíka
if (!empty($kosik2)) {
    echo "<ul>";
    foreach ($kosik2 as $item) {
        echo "<li>Produkt ID: {$item['product_id']}, Kategória: {$item['category_id']}, Počet: {$item['quantity']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Košík je prázdny.</p>";
}

if (!empty($kosik2)) {
    $max_quantity = 0;
    $max_product = null;

    foreach ($kosik2 as $item) {
        if ($item['quantity'] > $max_quantity) {
            $max_quantity = $item['quantity'];
            $max_product = $item;
        }
    }

    // Zobrazenie produktu 
    if ($max_product) {
        echo "<li>Najziadanejsi produkt: {$max_product['product_id']}, Kategória: {$max_product['category_id']}, Počet: {$max_product['quantity']}</li>";
    }
}
?>
</ul>


</body>
</html>
