<?php
$servername = "localhost";
$username = "administratorsmhcc";
$password = "msh10723@maesot";

try {
    $db = new PDO("mysql:host=$servername;dbname=OrderIT", $username, $password);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
