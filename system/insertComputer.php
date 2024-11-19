<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบและรับค่าจากฟอร์ม
    $computer_center_number = $_POST['computer_center_number'];
    $asset_number = $_POST['asset_number'];
    $computer_name = $_POST['computer_name'];
    $department = $_POST['depart_id'];
    $equipment_location = $_POST['equipment_location'];
    $purchase_date = $_POST['purchase_date'];
    $upgrade_date = $_POST['upgrade_date'];
    $upgrading_person = $_POST['upgrading_person'];
    $cpu = $_POST['cpu'];
    $socket = $_POST['socket'];
    $memory_type = $_POST['memory_type'];
    $memory_capacity = $_POST['memory_capacity'];
    $motherboard_brand = $_POST['motherboard_brand'];
    $motherboard = $_POST['motherboard'];
    $storage = $_POST['storage'];
    $storage_capacity = $_POST['storage_capacity'];
    $storage2 = $_POST['storage2'];
    $storage_capacity2 = $_POST['storage_capacity2'];
    $vga = $_POST['vga'];
    $os = $_POST['os'];

    // สร้าง SQL query
    $sql = "INSERT INTO computer_assets 
        (computer_center_number, asset_number, computer_name, department, equipment_location, purchase_date, upgrade_date, upgrading_person, cpu, socket, memory_type, memory_capacity, motherboard_brand, motherboard, storage, storage_capacity, storage2, storage_capacity2 , VGA, OS) 
        VALUES (:computer_center_number, :asset_number, :computer_name, :department, :equipment_location, :purchase_date, :upgrade_date, :upgrading_person, :cpu, :socket, :memory_type, :memory_capacity, :motherboard_brand, :motherboard, :storage, :storage_capacity, :storage2, :storage_capacity2, :vga, :os)";


    // ทำการ execute query
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':computer_center_number', $computer_center_number);
    $stmt->bindParam(':asset_number', $asset_number);
    $stmt->bindParam(':computer_name', $computer_name);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':equipment_location', $equipment_location);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':upgrade_date', $upgrade_date);
    $stmt->bindParam(':upgrading_person', $upgrading_person);
    $stmt->bindParam(':cpu', $cpu);
    $stmt->bindParam(':socket', $socket);
    $stmt->bindParam(':memory_type', $memory_type);
    $stmt->bindParam(':memory_capacity', $memory_capacity);
    $stmt->bindParam(':motherboard_brand', $motherboard_brand);
    $stmt->bindParam(':motherboard', $motherboard);
    $stmt->bindParam(':storage', $storage);
    $stmt->bindParam(':storage_capacity', $storage_capacity);
    $stmt->bindParam(':storage2', $storage2);
    $stmt->bindParam(':storage_capacity2', $storage_capacity2);
    $stmt->bindParam(':vga', $vga);
    $stmt->bindParam(':os', $os);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
        header("location: ../create");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("location: ../create");
    }
}
