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
    <title>Bewerken</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>  
    <div id="main_container">
        <a href="dashboard.php" class="back_button">Back</a>
    </div>
</body>
</html>