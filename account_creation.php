<?php
    $conn = require_once "db_connect.php";
    $email_err = $username_err = "";
    $password_verify_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $email = $_POST["email"];
        $verify_password = $_POST["verify_password"];
        $name_error = false;

        if (!preg_match("/^[a-zA-Z-0-9_]+$/", $username)) {
            $username_err = "Gebruikersnaam mag alleen letters, cijfers, underscores (_) en streepjes (-) bevatten";
            $name_error = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe gebruiker</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div id="header">
        <a href="dashboard.php"><img id="logo" src="img/logo.png" alt="logo"></a>
        <a href="adding.php"><h3>Toevoegen</h3></a>
        <a href="settings.php"><h3>Settings</h3></a>
        <a href="account_creation.php" id="current_page"><h3>Nieuwe gebruiker</h3></a>
        <a href="index.php"><h3>Logout</h3></a>
    </div>
    <div id="main_container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <p class="error"><?php echo $email_err;?></p> 
        <input class="input" type="text" id="email" name="email" placeholder="Example@gmail.com...">
        <p class="error"><?php echo $username_err;?></p>
        <input class="input" type="text" id="username" name="username" placeholder="Gebruikersnaam..." required minlength="3"> 
        <p class="error"><?php echo $password_verify_err;?></p>
        <input class="input" type="password" id="password" name="password" placeholder="Wachtwoord..." required minlength="6"><br>
        <input class="input" type="password" id="verify_password" name="verify_password" placeholder="Bevestig wachtwoord..." required>
        <br>
        <input class="login_button" type="submit" name="register" value="Toevoegen">
      </form>
    </div>
</body>
</html>