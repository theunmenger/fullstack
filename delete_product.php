<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $idproduct = $_SESSION["idproduct"];
        $system = $conn->prepare("select naam from product where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();

        $name = "";

        $name_result = $system->get_result();
        while ($row = $name_result->fetch_assoc()) {
            $name = htmlspecialchars($row['naam']);
        }

        $system = $conn->prepare("delete from voorraad where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();

        $system = $conn->prepare("delete from product where idproduct = ?");
        $system->bind_param("i", $idproduct);
        $system->execute();
        $system->close();

        $msg = "Product ". $name. " verwijderd";

        header("location: dashboard.php?edit_notification=". urlencode($msg));
        exit();
    }
?>