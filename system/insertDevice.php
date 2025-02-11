<?php
session_start();
require_once '../config/db.php';

function generateNewComputerNumber($lastNumber)
{
    if (preg_match('/^P(\d+)$/', $lastNumber, $matches)) {
        $currentNumber = (int)$matches[1];
        $newNumber = $currentNumber + 1;
        $newComputerNumber = "P" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        return $newComputerNumber;
    } else {
        return "P00001";
    }
}

$sql = "SELECT computer_center_number FROM device_asset ORDER BY computer_center_number DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lastComputerNumber = $stmt->fetchColumn() ?: "P00000";

$newComputerNumber = generateNewComputerNumber($lastComputerNumber);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบและรับค่าจากฟอร์ม
    $computer_center_number = $newComputerNumber;
    $list_device = $_POST['list_device'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $purchase_date = $_POST['purchase_date'];
    $price = $_POST['price'];
    $depart_id = $_POST['depart_id'];
    $asset_number = $_POST['asset_number'];

    // สร้างคำสั่ง SQL สำหรับการเพิ่มข้อมูล
    $sql = "INSERT INTO device_asset (computer_center_number,list_device,brand, model, purchase_date, price, depart_id, asset_number) 
                VALUES (:computer_center_number,:list_device,:brand, :model, :purchase_date, :price, :depart_id, :asset_number)";

    // ทำการเตรียมคำสั่ง SQL และทำการ execute
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':computer_center_number', $computer_center_number);
    $stmt->bindParam(':list_device', $list_device);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':model', $model);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':depart_id', $depart_id);
    $stmt->bindParam(':asset_number', $asset_number);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
        header("location: ../index");
        exit();
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("location: ../createDevice");
        exit();
    }
}
