<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if (isset($_SESSION['idproduct'])) {
        unset($_SESSION['idproduct']);
    }

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

<script>
    //delete notification na dat het weg fade
    window.addEventListener('DOMContentLoaded', () => {
        const notification = document.querySelector('.notification');
        if (notification) {
            setTimeout(() => {
                notification.remove();

                const url = new URL(window.location);
                url.searchParams.delete('edit_notification');
                history.replaceState(null, '', url);
            }, 5000);
        }
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>    

    <?php
    //notification code
        if (isset($_GET['edit_notification'])) {
            $msg = urldecode($_GET['edit_notification']);
            echo "<div class='notification'>$msg</div>";
        }
    ?>
    <div id="main_container">
        <h2 class="koptekst">Voorraad</h2>
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
            <p class="error" style="align-self: flex-start;">* is onder het minimum aantal</p>
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
                        $class = (htmlspecialchars($row['aantal']) <= htmlspecialchars($row['minimumaantal'])) ? 'error' : '';
                        echo("
                            <tr>
                                <td>". htmlspecialchars($row['naam']). "</td>
                                <td>". htmlspecialchars($row['type']). "</td>
                                <td>". htmlspecialchars($row['fabrieknaam']). "</td>
                                <td class='$class'>". htmlspecialchars($row['aantal']). "</td>
                                <td> €". htmlspecialchars($row['prijs']). "</td>
                                <td> €". htmlspecialchars($row['verkoopprijs']). "</td>
                                <td>". htmlspecialchars($row['locatienaam']). "</td>
                                <td>
                                    <form method='POST' action='bewerken.php' style='display:inline;'>
                                        <input type='hidden' name='idproduct' value='". htmlspecialchars($row["idproduct"]). "'>
                                        <button class='dashboard_button' type='submit'>Bewerken</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='POST' action='bestellen.php' style='display:inline;'>
                                        <input type='hidden' name='idproduct' value='". htmlspecialchars($row["idproduct"]). "'>
                                        <button class='dashboard_button' type='submit'>Bestellen</button>
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