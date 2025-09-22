<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    $totaalverkoopwaarde = 0;
    $totaalinkoopwaarde = 0;
    $selected_locatie = "alles";

    //prepare sql for worth calculation
    $system = $conn->prepare(
        "select prijs, verkoopprijs, voorraad.aantal from product
        left join voorraad
        using(idproduct);"
    );
    //run sql
    $system->execute();
    
    //get results
    $worth_result = $system->get_result();
    while ($row = $worth_result->fetch_assoc()) {
        $prijs = $row['prijs'];
        $verkoopprijs = $row['verkoopprijs'];
        $aantal = $row['aantal'];

        $totaalverkoopwaarde += $verkoopprijs * $aantal;
        $totaalinkoopwaarde += $prijs * $aantal;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $selected_locatie = $_POST["locatie"];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div id="header">
        <a href="dashboard.php" id="current_page"><img id="logo" src="img/logo.png" alt="logo"></a>
        <a href="adding.php"><h3>Toevoegen</h3></a>
        <a href="settings.php"><h3>Settings</h3></a>
        <a href="account_creation.php"><h3>Nieuwe gebruiker</h3></a>
        <a href="index.php"><h3>Logout</h3></a>
    </div>
    
    <div id="main_container">
        <div id="locatie_container">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <select name="locatie">
                    <option hidden value="Selecteer locatie:">Selecteer locatie:</option>
                    <option value="alles">Alles</option>
                    <?php
                        $system = $conn->prepare(
                            "select locatienaam from locatie;"
                        );
                        //run sql
                        $system->execute();
                        
                        //get results
                        $locatie_result = $system->get_result();
                        while ($row = $locatie_result->fetch_assoc()) {
                            echo("
                                <option value=". htmlspecialchars($row['locatienaam']).">". htmlspecialchars($row['locatienaam'])."</option>
                            ");
                        }
                    ?>
                </select>
                <button class='dashboard_button' type='submit'>Sorteer</button>
            </form>
        </div>
        <div id="waarde_container">
            <p><?php echo("Totaal inkoop waarde: €".$totaalinkoopwaarde) ?></p>
            <p><?php echo("Totaal verkoop waarde: €".$totaalverkoopwaarde) ?></p>            
        </div>

        <table id="dashboard_table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Fabriek</th>
                    <th>Aantal</th>
                    <th>Prijs</th>
                    <th>Verkoopprijs</th>
                    <th>Locatie</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    //prepare sql for table
                    //kijk of alles is geselecteerd
                    if ($selected_locatie == "alles") {
                        $system = $conn->prepare(
                            "select product.idproduct, naam, type, prijs, verkoopprijs, fabriek.fabrieknaam, voorraad.aantal, voorraad.minimumaantal, locatie.locatienaam from product
                            left join fabriek
                            using(idfabriek)
                            inner join voorraad
                            on voorraad.idproduct = product.idproduct
                            inner join locatie
                            on voorraad.idlocatie = locatie.idlocatie
                            order by locatienaam;"
                        );   
                    } else {
                        $system = $conn->prepare(
                            "select product.idproduct, naam, type, prijs, verkoopprijs, fabriek.fabrieknaam, voorraad.aantal, voorraad.minimumaantal, locatie.locatienaam from product
                            left join fabriek
                            using(idfabriek)
                            inner join voorraad
                            on voorraad.idproduct = product.idproduct
                            inner join locatie
                            on voorraad.idlocatie = locatie.idlocatie
                            where locatienaam = ?;"
                        );
                        $system->bind_param("s", $selected_locatie);
                    }
                    //run sql
                    $system->execute();

                    //get results and put them in the table
                    $table_result = $system->get_result();
                    while ($row = $table_result->fetch_assoc()) {
                        echo("
                            <tr>
                                <td>". htmlspecialchars($row['naam']). "</td>
                                <td>". htmlspecialchars($row['type']). "</td>
                                <td>". htmlspecialchars($row['fabrieknaam']). "</td>
                                <td>". htmlspecialchars($row['aantal']). "</td>
                                <td> €". htmlspecialchars($row['prijs']). "</td>
                                <td> €". htmlspecialchars($row['verkoopprijs']). "</td>
                                <td>". htmlspecialchars($row['locatienaam']). "</td>
                                <td>
                                    <form method='POST' action='bewerken.php' style='display:inline;'>
                                        <input type='hidden' name='email' value='". htmlspecialchars($row["idproduct"]). "'>
                                        <button class='dashboard_button' type='submit'>Bewerken</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='POST' action='bekijken.php' style='display:inline;'>
                                        <input type='hidden' name='email' value='". htmlspecialchars($row["idproduct"]). "'>
                                        <button class='dashboard_button' type='submit'>Bekijken</button>
                                    </form>
                                </td>
                            </tr>
                        ");
                    }
                    //close system after echoing tables
                    $system->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>