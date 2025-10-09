<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    $locatienaam_err = $notif = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $locatienaam = $_POST['locatienaam'] ?? '';

        $system = $conn->prepare("select locatienaam from locatie where locatienaam = ?");
        $system->bind_param("s", $locatienaam);
        $system->execute();
        
        $locatie_check = $system->get_result();
        if ($row = $locatie_check->fetch_assoc()) {
            $locatienaam_err = "Deze locatie staat al in de database";
        } else {
            $system = $conn->prepare("insert into locatie (locatienaam) values (?);");
            $system->bind_param("s", $locatienaam);
            $system->execute();       
            $notif = "Locatie toegevoegd";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locatie toevoegen</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>
    <div id="main_container">
        <h2 class="koptekst">Locatie toevoegen</h2>
        <div id="form_container">
            <p class="notif"><?php echo $notif;?></p>
            <form method="POST" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <p class="error"><?php echo $locatienaam_err;?></p>
                <input class="input" type="text" name="locatienaam" placeholder="Locatie naam..." required> 
                <input class="form_button" type="submit" name="toevoegen" value="Toevoegen">
            </form>
        </div>
    </div>
</body>
</html>