<?php
    session_start();
    $conn = require_once "db_connect.php";
    

    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
        exit;
    }

    $system = $conn->prepare("select admin from user where iduser = ?");
    $system->bind_param("i", $_SESSION['iduser']);
    $system->execute();
    $system->bind_result($admin_status);
    $system->fetch();
    $system->close();

    $admin_status = (bool)$admin_status;

    if (!$admin_status) {
        header('location: dashboard.php');
        exit;
    }

    $email_err_notif = $username_err = "";
    $password_verify_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $verify_password = $_POST["verify_password"];
        $admin = $_POST["admin"];
        $name_error = false;
        $email_error = false;
        $password_error = false;

        if (!preg_match("/^[a-zA-Z-0-9_]+$/", $username)) {
            $username_err = "Gebruikersnaam mag alleen letters, cijfers, underscores (_) en streepjes (-) bevatten";
            $name_error = true;
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err_notif = "Dit emailadres is ongeldig";
            $email_error = true;
        }

        $system = $conn->prepare(
            "select email from user where email = ?;"
        );
        $system->bind_param("s", $email);
        $system->execute();

        $user_result = $system->get_result();
        if($row = $user_result->fetch_assoc()) {
            $email_err_notif = "Dit emailadres is al gebruikt";
            $email_error = true;
        }

        if ($password !== $verify_password) {
            $password_verify_err = "Wachtwoorden zijn niet hetzelfde";
            $password_error = true;
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!$password_error && !$email_error && !$name_error) {
            $system = $conn->prepare(
                "insert into user (username, email, password, admin) values (?, ?, ?, ?)"
            );
            $system->bind_param("sssi", $username, $email, $hashed_password, $admin);
            $system->execute();
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
    <?php require_once "header.php";?>
    <div id="main_container">
        <div id="form_container">
            <a class="back_button" href="admin.php">Back</a>
            <form method="POST" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <p class="error"><?php echo $email_err_notif;?></p> 
                <input class="input" type="text" id="email" name="email" placeholder="Example@gmail.com...">
                <p class="error"><?php echo $username_err;?></p>
                <input class="input" type="text" id="username" name="username" placeholder="Gebruikersnaam..." required minlength="3"> 
                <p class="error"><?php echo $password_verify_err;?></p>
                <input class="input" type="password" id="password" name="password" placeholder="Wachtwoord..." required minlength="6"><br>
                <input class="input" type="password" id="verify_password" name="verify_password" placeholder="Bevestig wachtwoord..." required>
                <br>
                <select id="select" name="admin" id="admin">
                    <option value="1">Admin account</option>
                    <option value="0">Normale gebruiker</option>
                </select><br>
                <input class="form_button" type="submit" name="register" value="Toevoegen">
            </form>
        </div>
    </div>
</body>
</html>