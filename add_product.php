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
        $idlocatie = $_POST['idlocatie'];
        $idfabriek = $_POST['idfabriek'];

        if (empty($_POST['idfabriek']) || empty($_POST['idlocatie'])) {
            $msg = "Selecteer een fabriek en een locatie.";
            header("Location: dashboard.php?error_notification=" . urlencode($msg));
            exit;
        }

        //insert into product table
        $system = $conn->prepare("
            insert into product (naam, type, prijs, idfabriek, verkoopprijs) values (?, ?, ?, ?, ?);
        ");
        $system->bind_param("ssdid", $product_name, $product_type, $prijs, $idfabriek, $verkoopprijs);
        $system->execute();
        $system->close();
        
        $idproduct = $conn->insert_id;

        $system = $conn->prepare("
            insert into voorraad (idlocatie, idproduct, aantal, minimumaantal) values (?, ?, ?, ?);
        ");
        $system->bind_param("iiii", $idlocatie, $idproduct, $aantal, $min_aantal);
        $system->execute();
        $system->close();
        
        $msg = "Succesfully added product";

        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
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
    <?php require_once "header.php";?>
    <div id="main_container">
        <h2 class="koptekst">Producten toevoegen</h2>
        <div id="form_container">
            <form method="POST" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <input class="input" type="text" name="product_name" placeholder="Product naam..." required> 
                <input class="input" type="text" name="product_type" placeholder="Product type" required> 
                <input class="input" type="number" name="prijs" step="0.01" placeholder="Inkoopprijs..." required>
                <input class="input" type="number" name="verkoopprijs" step="0.01" placeholder="Verkoopprijs..." required>
                <input class="input" type="number" name="aantal" placeholder="Aantal..." required>
                <input class="input" type="number" name="min_aantal" placeholder="Minimum aantal" required>
                <select name="idlocatie" id="select">
                    <option hidden value="" required>Selecteer locatie:</option>
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
                    <option hidden value="" required>Selecteer fabriek:</option>
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