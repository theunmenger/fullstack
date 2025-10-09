<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 


    $iduser = $_SESSION['iduser'];

    $name_notif = $error = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>
    <div id="main_container">
        <h2 class="koptekst">Settings</h2>
        <div id="form_container">
            <form method="POST" class="form" action="name_change.php">
                <input type="hidden" name="iduser" value="<?php echo htmlspecialchars($iduser);?>">
                <input class="input" type="text" id="new_username" name="new_username" placeholder="Nieuwe gebruikersnaam..." minlength="3" required>
                <button class="form_button" type="submit">Verander gebruikersnaam</button>
            </form>
        </div>
    </div>
</body>
</html>