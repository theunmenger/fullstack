<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $iduser = $_POST['iduser'];
        $new_email = $_POST['new_email'] ?? '';

        $system = $conn->prepare(
            "select email from user where email = ?;"
        );
        $system->bind_param("s", $new_email);
        $system->execute();

        $user_result = $system->get_result();
        if($row = $user_result->fetch_assoc()) {
            $msg = "Dit emailadres is al gebruikt";

            header("location: dashboard.php?edit_notification=". urlencode($msg));
            exit;    
        } else if(!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Ongeldig emailadres";

            header("location: dashboard.php?edit_notification=". urlencode($msg));
            exit;            
            
        } else {
            $system = $conn->prepare("
                update user set email = ? where iduser = ?;
            ");
            $system->bind_param("si", $new_email, $iduser);
            $system->execute();     
            $system->close();

            $msg = "Email veranderd";    

            header("location: dashboard.php?edit_notification=". urlencode($msg));
            exit;            
        }   
    }
?>