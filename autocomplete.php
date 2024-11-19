<?php

$servername = "localhost";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host=$servername;dbname=OrderIT", $username, $password);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$term = $_GET['term']; // คำที่ผู้ใช้ป้อน

$sql = "SELECT depart_id, depart_name FROM depart WHERE depart_name LIKE :term";
$stmt = $db->prepare($sql);
$stmt->bindValue(':term', '%' . $term . '%', PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = array();
foreach ($result as $row) {
    $data[] = array(
        'label' => $row['depart_name'],
        'value' => $row['depart_id']
    );
}

echo json_encode($data);
