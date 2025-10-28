<?php
    session_start();
    $conn = require_once "db_connect.php";
    $msg = "";
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
        $msg = "you do not have access to the admin panel";

        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require_once "header.php";?>
    <div id="main_container">
        <h2 class="koptekst">Admin omgeving</h2>
        <a href="account_creation.php" class="link">Maak een nieuw account</a>
        <a href="account_list.php" class="link">Bekijk en bewerk accounts</a>
    </div>
</body>
</html>