<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_name = $_POST['product_name'] ?? '';
        $product_type = $_POST['product_type'] ?? '';
        $prijs = $_POST['prijs'] ?? '';
        $verkoopprijs = $_POST['verkoopprijs'] ?? '';
        $aantal = $_POST['aantal'] ?? '';
        $min_aantal = $_POST['min_aantal'] ?? '';
        $idlocatie = $_POST['idlocatie'] ?? '';
        $idfabriek = $_POST['idfabriek'] ?? '';

        //insert into product table
        $system = $conn->prepare("
            insert into product (naam, type, prijs, idfabriek, verkoopprijs) values (?, ?, ?, ?, ?);
        ");
        $system->bind_param("ssdid", $product_name, $product_type, $prijs, $idfabriek, $verkoopprijs);
        $system->execute();
        
        $idproduct = $conn->insert_id;

        $system = $conn->prepare("
            insert into voorraad (idlocatie, idproduct, idfabriek, aantal, minimumaantal) values (?, ?, ?, ?, ?);
        ");
        $system->bind_param("iiiii", $idlocatie, $idproduct, $idfabriek, $aantal, $min_aantal);
        $system->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product toevoegen</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div id="header">
        <a href="dashboard.php"><img id="logo" src="img/logo.png" alt="logo"></a>
        <a href="adding.php" id="current_page"><h3>Toevoegen</h3></a>
        <a href="settings.php"><h3>Settings</h3></a>
        <a href="account_creation.php"><h3>Nieuwe gebruiker</h3></a>
        <a href="index.php"><h3>Logout</h3></a>
    </div>
    <div id="main_container">
        <div id="form_container">
            <form method="POST" id="product_toevoegen" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <input class="input" type="text" name="product_name" placeholder="Product naam..."> 
                <input class="input" type="text" name="product_type" placeholder="Product type"> 
                <input class="input" type="number" name="prijs" step="0.01" placeholder="Prijs...">
                <input class="input" type="number" name="verkoopprijs" step="0.01" placeholder="Verkoopprijs...">
                <input class="input" type="number" name="aantal" placeholder="Aantal...">
                <input class="input" type="number" name="min_aantal" placeholder="Minimum aantal">
                <select name="idlocatie" id="select">
                    <option hidden value="Selecteer locatie:">Selecteer locatie:</option>
                    <?php
                        $system = $conn->prepare(
                            "select locatienaam, idlocatie from locatie;"
                        );
                        //run sql
                        $system->execute();
                        
                        //get results
                        $locatie_result = $system->get_result();
                        while ($row = $locatie_result->fetch_assoc()) {
                            echo("
                                <option value=". htmlspecialchars($row['idlocatie']).">". htmlspecialchars($row['locatienaam'])."</option>
                            ");
                        }
                    ?>
                </select>
                <select name="idfabriek" id="select">
                    <option hidden value="Selecteer locatie:">Selecteer fabriek:</option>
                    <?php
                        $system = $conn->prepare(
                            "select fabrieknaam, idfabriek from fabriek;"
                        );
                        //run sql
                        $system->execute();
                        
                        //get results
                        $locatie_result = $system->get_result();
                        while ($row = $locatie_result->fetch_assoc()) {
                            echo("
                                <option value=". htmlspecialchars($row['idfabriek']).">". htmlspecialchars($row['fabrieknaam'])."</option>
                            ");
                        }
                    ?>
                </select>
                <input class="form_button" type="submit" name="toevoegen" value="Toevoegen">
            </form>
        </div>
    </div>
</body>
</html>