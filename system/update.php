<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['update'])) {

    $asset_number = $_POST['asset_number'];
    // $computer_center_number = $_POST['computer_center_number'];
    $computer_name = $_POST['computer_name'];
    $depart_id = $_POST['depart_id'];
    $equipment_location = $_POST['equipment_location'];
    $purchase_date = $_POST['purchase_date'];
    $upgrading_person = $_POST['upgrading_person'];
    $upgrade_date = $_POST['upgrade_date'];
    $CPU = $_POST['CPU'];
    $socket = $_POST['socket'];
    $memory_type = $_POST['memory_type'];
    $memory_capacity = $_POST['memory_capacity'];
    $motherboard_brand = $_POST['motherboard_brand'];
    $motherboard = $_POST['motherboard'];
    $storage = $_POST['storage'];
    $storage_capacity = $_POST['storage_capacity'];
    $storage2 = $_POST['storage2'];
    $storage_capacity2 = $_POST['storage_capacity2'];
    $VGA = $_POST['VGA'];
    $OS = $_POST['OS'];
    $computer_id = $_POST['computer_id'];

    // สร้างคำสั่ง SQL สำหรับการอัพเดทข้อมูล
    // $sql = "INSERT INTO computer_assets (
    //     asset_number, 
    //     computer_center_number, 
    //     computer_name, 
    //     department, 
    //     equipment_location, 
    //     purchase_date, 
    //     upgrading_person, 
    //     upgrade_date, 
    //     CPU, 
    //     socket, 
    //     memory_type, 
    //     memory_capacity, 
    //     motherboard_brand, 
    //     motherboard, 
    //     storage, 
    //     storage_capacity, 
    //     storage2, 
    //     storage_capacity2, 
    //     VGA, 
    //     OS
    // ) VALUES (
    //     :asset_number, 
    //     :computer_center_number, 
    //     :computer_name, 
    //     :depart_id, 
    //     :equipment_location, 
    //     :purchase_date, 
    //     :upgrading_person, 
    //     :upgrade_date, 
    //     :CPU, 
    //     :socket, 
    //     :memory_type, 
    //     :memory_capacity, 
    //     :motherboard_brand, 
    //     :motherboard, 
    //     :storage, 
    //     :storage_capacity, 
    //     :storage2, 
    //     :storage_capacity2, 
    //     :VGA, 
    //     :OS
    // );";
    $sql = "UPDATE computer_assets SET 
            asset_number = :asset_number, 
            computer_name = :computer_name, 
            department = :depart_id, 
            equipment_location = :equipment_location, 
            purchase_date = :purchase_date, 
            upgrading_person = :upgrading_person, 
            upgrade_date = :upgrade_date, 
            CPU = :CPU, 
            socket = :socket, 
            memory_type = :memory_type, 
            memory_capacity = :memory_capacity, 
            motherboard_brand = :motherboard_brand, 
            motherboard = :motherboard, 
            storage = :storage, 
            storage_capacity = :storage_capacity, 
            storage2 = :storage2, 
            storage_capacity2 = :storage_capacity2, 
            VGA = :VGA, 
            OS = :OS 
            WHERE id = :computer_id";

    // ทำการเตรียมคำสั่ง SQL และทำการ execute
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':computer_id', $computer_id);

    $stmt->bindParam(':asset_number', $asset_number);
    // $stmt->bindParam(':computer_center_number', $computer_center_number);
    $stmt->bindParam(':computer_name', $computer_name);
    $stmt->bindParam(':depart_id', $depart_id);
    $stmt->bindParam(':equipment_location', $equipment_location);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':upgrading_person', $upgrading_person);
    $stmt->bindParam(':upgrade_date', $upgrade_date);
    $stmt->bindParam(':CPU', $CPU);
    $stmt->bindParam(':socket', $socket);
    $stmt->bindParam(':memory_type', $memory_type);
    $stmt->bindParam(':memory_capacity', $memory_capacity);
    $stmt->bindParam(':motherboard_brand', $motherboard_brand);
    $stmt->bindParam(':motherboard', $motherboard);
    $stmt->bindParam(':storage', $storage);
    $stmt->bindParam(':storage_capacity', $storage_capacity);
    $stmt->bindParam(':storage2', $storage2);
    $stmt->bindParam(':storage_capacity2', $storage_capacity2);
    $stmt->bindParam(':VGA', $VGA);
    $stmt->bindParam(':OS', $OS);

    if ($stmt->execute()) {
        $submittedPrograms = $_POST['programs'] ?? [];

        // 1. Get all currently stored program names for this computer
        $stmt = $conn->prepare("SELECT program_name FROM installed_programs WHERE computer_assets = :id");
        $stmt->bindParam(":id", $computer_id);
        $stmt->execute();
        $currentPrograms = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 2. Find programs to delete
        $programsToDelete = array_diff($currentPrograms, $submittedPrograms);

        // 3. Delete unchecked ones
        if (!empty($programsToDelete)) {
            $deleteStmt = $conn->prepare("DELETE FROM installed_programs WHERE computer_assets = :id AND program_name = :program");
            foreach ($programsToDelete as $progName) {
                $deleteStmt->execute([':id' => $computer_id, ':program' => $progName]);
            }
        }
        if (isset($_POST['programs'])) {
            $programs = $_POST['programs'];
            $insertProgramStmt = $conn->prepare("INSERT INTO installed_programs (computer_assets, program_name) VALUES (:computer_assets, :program_name)");

            foreach ($programs as $program) {
                if (!in_array($program, $currentPrograms)) {
                    $insertProgramStmt->bindParam(':computer_assets', $computer_id);
                    $insertProgramStmt->bindParam(':program_name', $program);
                    $insertProgramStmt->execute();
                }
            }
        }

        $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
        header("Location: ../index");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("Location: ../index");
    }
    exit();
}
if (isset($_POST['updateDevice'])) {

    $id = $_POST['id'];
    $asset_number = $_POST['asset_number'];
    $list_device = $_POST['list_device'];
    $depart_id = $_POST['depart_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $purchase_date = $_POST['purchase_date'];
    $price = $_POST['price'];
    $computer_center_number = $_POST['computer_center_number'];
    date_default_timezone_set('Asia/Bangkok');
    $timestamp = date('Y-m-d H:i:s');

    // เตรียมคำสั่ง SQL UPDATE
    $sql = "UPDATE device_asset
            SET computer_center_number = :computer_center_number,
                asset_number = :asset_number,
                list_device = :list_device,
                depart_id = :depart_id,
                brand = :brand,
                model = :model,
                purchase_date = :purchase_date,
                price = :price,
                timestamp = :timestamp
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':asset_number', $asset_number);
    $stmt->bindParam(':list_device', $list_device);
    $stmt->bindParam(':depart_id', $depart_id);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':model', $model);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':timestamp', $timestamp);
    $stmt->bindParam(':computer_center_number', $computer_center_number);

    if ($stmt->execute()) {

        $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
        header("Location: ../index");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("Location: ../index");
    }

    exit();
}


if (isset($_POST['updateStatus'])) {

    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = "UPDATE computer_assets
    SET status = :status
    WHERE id = :id";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {

        $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
        header("Location: ../index");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("Location: ../index");
    }

    exit();
}

if (isset($_POST['updateStatusDevice'])) {

    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = "UPDATE device_asset
    SET status = :status
    WHERE id = :id";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {

        $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
        header("Location: ../index");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาด";
        header("Location: ../index");
    }

    exit();
}
