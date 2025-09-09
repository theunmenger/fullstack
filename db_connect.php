<?php
    $servername = "mysql";
    $server_username = "root";
    $server_password = "password";
    $database = "fullstack";
    
    //connect
    $conn = new mysqli($servername, $server_username, $server_password, $database);
    //check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
?>