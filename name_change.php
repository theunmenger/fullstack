<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $iduser = $_POST['iduser'];
        $new_username = $_POST['new_username'] ?? '';


        if (!preg_match("/^[a-zA-Z-0-9_]+$/", $new_username)) {
            $msg = "Username can only contain letters, numbers, underscores (_), and hyphens (-)";
            header("location: dashboard.php?error_notification=". urlencode($msg));
            exit;
        } else {
            $system = $conn->prepare("
                update user set username = ? where iduser = ?;
            ");
            $system->bind_param("si", $new_username, $iduser);
            $system->execute();     
            $system->close();

            $msg = "Gebruikersnaam veranderd";    

            header("location: dashboard.php?edit_notification=". urlencode($msg));
            exit;            
        }
    }
?>