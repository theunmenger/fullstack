<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idproduct = $_SESSION['idproduct'];
        $new_inkoopprijs = $_POST['new_inkoopprijs'];

        //get product price for msg
        $system = $conn->prepare("select prijs from product where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $name = "";
        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $prijs = htmlspecialchars($row['prijs']);
        }
        $system->close();

        //change product price
        $system = $conn->prepare("update product set prijs = ? where idproduct = ?");
        $system->bind_param("ii", $new_inkoopprijs, $idproduct);
        $system->execute();
        $system->close();

        $msg = "Inkoopprijs succesvol bijgewerkt van ". $prijs. " naar ". $new_inkoopprijs;
        //send msg to dashboard
        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>