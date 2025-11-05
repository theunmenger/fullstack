<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    $fabrieknaam_err = $notif = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fabrieknaam = $_POST['fabrieknaam'] ?? '';

        $system = $conn->prepare("select fabrieknaam from fabriek where fabrieknaam = ?");
        $system->bind_param("s", $fabrieknaam);
        $system->execute();
        
        $fabriek_check = $system->get_result();
        if ($row = $fabriek_check->fetch_assoc()) {
            $fabrieknaam_err = "Deze Fabriek staat al in de database";
        } else {
            $system = $conn->prepare("insert into fabriek (fabrieknaam) values (?);");
            $system->bind_param("s", $fabrieknaam);
            $system->execute(); 
            $system->close();
            
            $msg = "Fabriek toegevoegd";    

            header("location: dashboard.php?edit_notification=". urlencode($msg));

            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabriek toevoegen</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>
    <div id="main_container">
        <h2 class="koptekst">Fabriek toevoegen</h2>
        <div id="form_container">
            <p class="notif"><?php echo $notif;?></p>
            <form method="POST" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <p class="error"><?php echo $fabrieknaam_err;?></p>
                <input class="input" type="text" name="fabrieknaam" placeholder="Fabriek naam..." required> 
                <input class="form_button" type="submit" name="toevoegen" value="Toevoegen">
            </form>
        </div>
    </div>
</body>
</html>