<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idproduct = $_SESSION['idproduct'];
        $new_aantal = $_POST['new_aantal'];

        //get product aantal for msg
        $system = $conn->prepare("select aantal from voorraad where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $name = "";
        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $aantal = htmlspecialchars($row['aantal']);
        }
        $system->close();

        //change product aantal
        $system = $conn->prepare("update voorraad set aantal = ? where idproduct = ?");
        $system->bind_param("ii", $new_aantal, $idproduct);
        $system->execute();
        $system->close();

        $msg = "Aantal succesvol bijgewerkt van ". $aantal. " naar ". $new_aantal;
        //send msg to dashboard
        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>