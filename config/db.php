<?php
// $servername = "172.16.190.17";
$servername = "localhost";
$username = "AchirayaJ";
$password = "Haru1chi_KzhsLov3r";
$dbname = "run_number";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
