<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idproduct = $_SESSION['idproduct'];
        $new_product_name = $_POST['new_product_name'];

        //get product name for msg
        $system = $conn->prepare("select naam from product where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $name = "";
        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $name = htmlspecialchars($row['naam']);
        }
        $system->close();

        //change product name
        $system = $conn->prepare("update product set naam = ? where idproduct = ?");
        $system->bind_param("si", $new_product_name, $idproduct);
        $system->execute();
        $system->close();

        $msg = "Productnaam succesvol bijgewerkt van ". $name. " naar ". $new_product_name;

        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>