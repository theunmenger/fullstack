<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellingen</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>  
    <div id="main_container">
        <table id="dashboard_table">
            <thead>
                <tr>
                    <th>Gebruiker</th>
                    <th>Product</th>
                    <th>Locatie</th>
                    <th>Aantal besteld</th>
                    <th>Besteldatum</th>
                    <th>Aankomstdatum</th>
                    <th>Aangekomen</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $system = $conn->prepare("
                        select idbestelling, user.username, product.naam, locatie.locatienaam, aantalbestelt, besteldatum, aankomstdatum, aangekomen from bestelling
                        inner join user 
                        on user.iduser = bestelling.iduser
                        inner join product
                        on product.idproduct = bestelling.idproduct
                        inner join locatie
                        on locatie.idlocatie = bestelling.idlocatie;
                    ");
                    $system->execute();

                    $table_result = $system->get_result();
                    while ($row = $table_result->fetch_assoc()) {
                        if (htmlspecialchars($row['aangekomen']) == 0) {
                            $aangekomen = "Nee";
                            $confirm_arrival = "                                    
                                <form method='POST' action='aankomst.php' style='display:inline;'>
                                    <input type='hidden' name='idbestelling' value='". htmlspecialchars($row['idbestelling']). "'>
                                    <button type='submit' class='dashboard_button'>aankomst bevestigen</button>
                                </form> ";
                        } else {
                            $aangekomen = "Ja";
                            $confirm_arrival = "";
                        }
            
                        if (!isset($row['aankomstdatum'])) {
                            $aankomstdatum = "N/A";
                        } else {
                            $aankomstdatum = htmlspecialchars($row['aankomstdatum']);
                        }
                        echo("
                            <tr>
                                <td>". htmlspecialchars($row['username']). "</td>
                                <td>". htmlspecialchars($row['naam']). "</td>
                                <td>". htmlspecialchars($row['locatienaam']). "</td>
                                <td>". htmlspecialchars($row['aantalbestelt']). "</td>
                                <td>". htmlspecialchars($row['besteldatum']). "</td>
                                <td>". $aankomstdatum. "</td>
                                <td>". $aangekomen. "</td>
                                <td>
                                    ".$confirm_arrival."
                                </td>
                            </tr>
                        ");
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>