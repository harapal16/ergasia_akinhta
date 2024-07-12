<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ds_estate";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if($conn->connect_error){
        die("Connection failled: $conn->connect_error ");
    }

?>