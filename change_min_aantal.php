<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idproduct = $_SESSION['idproduct'];
        $new_minimumaantal = $_POST['new_min_aantal'];

        //get product minimumaantal for msg
        $system = $conn->prepare("select minimumaantal from voorraad where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $name = "";
        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $minimumaantal = htmlspecialchars($row['minimumaantal']);
        }
        $system->close();

        //change product minimumaantal
        $system = $conn->prepare("update voorraad set minimumaantal = ? where idproduct = ?");
        $system->bind_param("ii", $new_minimumaantal, $idproduct);
        $system->execute();
        $system->close();

        $msg = "Minimumaantal succesvol bijgewerkt van ". $minimumaantal. " naar ". $new_minimumaantal;
        //send msg to dashboard
        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>