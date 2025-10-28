<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idproduct = $_SESSION['idproduct'];
        $new_product_type = $_POST['new_product_type'];

        //get product type for msg
        $system = $conn->prepare("select type from product where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $name = "";
        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $type = htmlspecialchars($row['type']);
        }
        $system->close();

        //change product name
        $system = $conn->prepare("update product set type = ? where idproduct = ?");
        $system->bind_param("si", $new_product_type, $idproduct);
        $system->execute();
        $system->close();

        $msg = "Producttype succesvol bijgewerkt van ". $type. " naar ". $new_product_type;

        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>