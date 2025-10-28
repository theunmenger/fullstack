<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idproduct = $_SESSION['idproduct'];
        $new_verkoopprijs = $_POST['new_verkoopprijs'];

        //get product price for msg
        $system = $conn->prepare("select verkoopprijs from product where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $name = "";
        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $verkoopprijs = htmlspecialchars($row['verkoopprijs']);
        }
        $system->close();

        //change product price
        $system = $conn->prepare("update product set verkoopprijs = ? where idproduct = ?");
        $system->bind_param("ii", $new_verkoopprijs, $idproduct);
        $system->execute();
        $system->close();

        $msg = "Verkoopprijs succesvol bijgewerkt van ". $verkoopprijs. " naar ". $new_verkoopprijs;
        //send msg to dashboard
        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>