<?php
    session_start();
    if (isset($_SESSION['iduser'])) {
        session_destroy();
    }
    $conn = require_once "db_connect.php"; 

    $email_err = $password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'];
        $email = $_POST['email'];
        $correct_pass = false;

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Dit emailadres is ongeldig";
        } else {

            $system = $conn->prepare(
                "select email, password, iduser from user where email = ?;"
            );
            $system->bind_param("s", $email);
            $system->execute();

            $user_result = $system->get_result();

            if($row = $user_result->fetch_assoc()) {
                $iduser = $row['iduser'];
                $validate_email = $row['email'];
                $validate_password = $row['password'];

                if (password_verify($password, $validate_password)) {
                    $correct_pass = true;
                } else {
                    $password_err = "Dit wachtwoord is onjuist";
                }
            } else {
                $email_err = "Dit emailadres bestaad niet";
            }

            if ($correct_pass) {
                $_SESSION['iduser'] = $iduser;
                header('location: dashboard.php');
                exit;
            }
        }  
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div id="header">
        <img id="logo" src="img/logo.png" alt="logo">
        <h2 style="color:white;">Tools for ever</h2>
    </div>
    <div id="login_container">
        <form method="POST" id="login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <p class="error"><?php echo $email_err ?></p>
            <input class="input" type="text" id="email" name="email" placeholder="Example@gmail.com...">
            <p class="error"><?php echo $password_err ?></p>
            <input class="input" type="password" id="password" name="password" placeholder="Wachtwoord..." required>
            <button class='login_button' type='submit'>Login</button>
        </form>
    </div>
</body>
</html>