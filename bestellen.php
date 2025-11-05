<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    $iduser = $_SESSION['iduser'];
    $idproduct = null;

    if (isset($_POST['idproduct']) && !isset($_POST['aantal'])) {
        $idproduct = $_POST['idproduct'];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aantal'])) {
        $idproduct = $_POST['idproduct'];
        $aantal = $_POST['aantal'];

        $system = $conn->prepare("
            select product.idproduct, locatie.idlocatie from product
            inner join voorraad
            on voorraad.idproduct = product.idproduct
            inner join locatie
            on voorraad.idlocatie = locatie.idlocatie
            where product.idproduct = ?;
        ");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $locatie_result = $system->get_result();
        while ($row = $locatie_result->fetch_assoc()) {
            $idlocatie = htmlspecialchars($row['idlocatie']);
        }
        $system->close();
        
        $system = $conn->prepare("
            insert into bestelling (iduser, idproduct, idlocatie, aantalbestelt, aangekomen, besteldatum) values (?, ?, ?, ?, ?, ?);
        ");

        $aangekomen = 0;
        $datum = date("y-m-d");

        $system->bind_param("iiiiss", $iduser, $idproduct, $idlocatie, $aantal, $aangekomen, $datum);
        $system->execute();
        $system->close();

        $msg = "Product bestelt";
        //send msg to dashboard
        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bestellen</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>    
    <div id="main_container">
        <h2 class="koptekst">Product bestellen</h2>
        <a href="dashboard.php" class="back_button">Back</a>
        <table id="dashboard_table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Fabriek</th>
                    <th>Locatie</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $system = $conn->prepare("
                        select product.idproduct, naam, type, fabriek.fabrieknaam, locatie.locatienaam from product
                        left join fabriek
                        using(idfabriek)
                        inner join voorraad
                        on voorraad.idproduct = product.idproduct
                        inner join locatie
                        on voorraad.idlocatie = locatie.idlocatie
                        where product.idproduct = ?;
                    ");
                    $system->bind_param("i", $idproduct);
                    $system->execute();

                    $table_result = $system->get_result();
                    while ($row = $table_result->fetch_assoc()) {
                        echo("
                            <tr>
                                <td>". htmlspecialchars($row['naam']). "</td>
                                <td>". htmlspecialchars($row['type']). "</td>
                                <td>". htmlspecialchars($row['fabrieknaam']). "</td>
                                <td>". htmlspecialchars($row['locatienaam']). "</td>
                            </tr>
                        ");
                    }
                ?>
            </tbody>
        </table>
        <div id="form_container">
            <form method="POST" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct); ?>">
                <input class="input" type="number" name="aantal" placeholder="Aantal bestelling..." required>
                <input class="form_button" type="submit" name="bestellen" value="Bestellen">
            </form>
        </div>
    </div>
</body>
</html>