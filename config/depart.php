<?php
// $servername = "172.16.190.17";
$servername = "localhost";
$username = "AchirayaJ";
$password = "Haru1chi_KzhsLov3r";

try {
    $db = new PDO("mysql:host=$servername;dbname=OrderIT", $username, $password);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
