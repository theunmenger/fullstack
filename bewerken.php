<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 


    $_SESSION['idproduct'] = $_POST['idproduct'];
    $idproduct = $_SESSION["idproduct"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewerken</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>  
    <div id="main_container">
        <a href="dashboard.php" class="back_button">Back</a>
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
                    $system = $conn->prepare("
                        select product.idproduct, naam, type, prijs, verkoopprijs, fabriek.fabrieknaam, voorraad.aantal, voorraad.minimumaantal, locatie.locatienaam from product
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
                                    <form method='POST' action='delete_product.php' style='display:inline;'>
                                        <input type='hidden' name='idproduct' value='<?php echo htmlspecialchars($idproduct);?>'>
                                        <button type='submit' class='delete_button'>Verwijder product</button>
                                    </form>                                
                                </td>
                            </tr>
                        ");
                    }
                ?>
            </tbody>
        </table>
        <div id="update_form_container">
            <form method="POST" class="update_form" action="change_product_name.php">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct);?>">
                <input class="update_input" type="text" name="new_product_name" placeholder="Nieuwe product naam...">
                <input class="update_form_button" type="submit" name="update" value="Verander product naam">
            </form>
            <form method="POST" class="update_form" action="change_product_type.php">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct);?>">
                <input class="update_input" type="text" name="new_product_type" placeholder="Nieuwe product type...">
                <input class="update_form_button" type="submit" name="update" value="Verander product type">
            </form>
            <form method="POST" class="update_form" action="change_inkoopprijs.php">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct);?>">
                <input class="update_input" step="0.01" type="number" name="new_inkoopprijs" placeholder="Nieuwe inkoopprijs...">
                <input class="update_form_button" type="submit" name="update" value="Verander inkoopprijs">
            </form>
            <form method="POST" class="update_form" action="change_verkoopprijs.php">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct);?>">
                <input class="update_input" step="0.01" type="number" name="new_verkoopprijs" placeholder="Nieuwe verkoopprijs...">
                <input class="update_form_button" type="submit" name="update" value="Verander verkoopprijs">
            </form>
            <form method="POST" class="update_form" action="change_aantal.php">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct);?>">
                <input class="update_input" step="1" type="number" name="new_aantal" placeholder="Nieuw aantal...">
                <input class="update_form_button" type="submit" name="update" value="Verander aantal">
            </form>
            <form method="POST" class="update_form" action="change_min_aantal.php">
                <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($idproduct);?>">
                <input class="update_input" step="1" type="number" name="new_min_aantal" placeholder="Nieuw minimum aantal...">
                <input class="update_form_button" type="submit" name="update" value="Verander minimum aantal">
            </form>
        </div>
    </div>
</body>
</html>