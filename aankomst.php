<?php
    session_start();
    if (!isset($_SESSION['iduser'])) {
        header('location: index.php');
    }
    //connect to db
    $conn = require_once "db_connect.php"; 

    $idbestelling = $_POST['idbestelling'];
    $datum = date("Y-m-d");

    $system = $conn->prepare("select idproduct, aantalbestelt from bestelling where idbestelling = ?");
    $system->bind_param("i", $idbestelling);
    $system->execute();
    $idproduct_result = $system->get_result();
    while ($row = $idproduct_result->fetch_assoc()) {
        $idproduct = htmlspecialchars($row['idproduct']);
        $aantalbestelt = htmlspecialchars($row['aantalbestelt']);
    }
    $system->close();

    $system = $conn->prepare("update voorraad set aantal = aantal + ? where idproduct = ?");
    $system->bind_param("ii", $aantalbestelt, $idproduct);
    $system->execute();
    $system->close();

    $system = $conn->prepare("update bestelling set aangekomen = '1', aankomstdatum = ? where idbestelling = ?;");
    $system->bind_param("si", $datum, $idbestelling);
    $system->execute();
    $system->close();

    header("location: bestellingen.php");
?>