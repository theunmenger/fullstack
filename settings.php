<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 


    $iduser = $_SESSION['iduser'];

    $name_notif = $error = "";

    $system = $conn->prepare("select username, email from user where iduser = ?");
    $system->bind_param("i", $iduser);
    $system->execute();
    $username_result = $system->get_result();
    while ($row = $username_result->fetch_assoc()) {
        $username = htmlspecialchars($row['username']);
        $email = htmlspecialchars($row['email']);
    }
    $system->close();
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
        <div id="userinfo_container">
            <?php
                echo("<P>username: ". $username. "</p>");
                echo("</p>email: ". $email. "</p>")
            ?>
        </div>
        <div id="form_container">
            <form method="POST" class="form" action="name_change.php">
                <input type="hidden" name="iduser" value="<?php echo htmlspecialchars($iduser);?>">
                <input class="input" type="text" id="new_username" name="new_username" placeholder="Nieuwe gebruikersnaam..." minlength="3" required>
                <button class="form_button" type="submit">Verander gebruikersnaam</button>
            </form>
            <br>
            <form method="POST" class="form" action="email_change.php">
                <input type="hidden" name="iduser" value="<?php echo htmlspecialchars($iduser);?>">
                <input class="input" type="text" id="new_email" name="new_email" placeholder="Nieuw emailadres..." required>
                <button class="form_button" type="submit">Verander emailadres</button>
            </form>
        </div>
    </div>
</body>
</html>