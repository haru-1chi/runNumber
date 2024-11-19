<?php
session_start();
require_once '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบและรับค่าจากฟอร์ม
    $computer_center_number = $_POST['computer_center_number'];
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
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("location: ../createDevice");
    }
}
