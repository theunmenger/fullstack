<?php
   $conn = require_once "db_connect.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div id="header">
        
    </div>
    <div id="login_container">
        <form method="POST" id="login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input class="input" type="email" id="email" name="email" placeholder="Example@gmail.com..." required>
            <input class="input" type="password" id="password" name="password" placeholder="Password..." required>
            <button class='login_button' type='submit'>Login</button>
        </form>
    </div>
</body>
</html>