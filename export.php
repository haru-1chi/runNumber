<?php
// Set the file encoding to UTF-8
header('Content-Type: text/html; charset=utf-8');
require_once 'config/db.php';
require_once 'config/depart.php';
session_start();

// Check if the user is logged in
if (isset($_SESSION['admin_log'])) {
    $admin = $_SESSION['admin_log'];
}

// Check if the export action is triggered
if (isset($_POST['actAll'])) {
    echo '<head>';
    echo '<meta charset="UTF-8">';

    // Set headers for Excel file download
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=หมายเลขอุปกรณ์.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo '</head>';

    // Output the HTML for the table
    echo '<table style="border: 1px solid black;">';
    echo '<thead>';
    echo '<tr style="text-align:center;">';
    echo '<th style="border: 1px solid black;"  scope="col">หมายเลขศูนย์คอม</th>';
    echo '<th style="border: 1px solid black;"  scope="col">เลขครุภัณฑ์</th>';
    echo '<th style="border: 1px solid black;"  scope="col">รายการ</th>';
    echo '<th style="border: 1px solid black;"  scope="col">หน่วยงาน</th>';
    echo '<th style="border: 1px solid black;"  scope="col">ยี่ห้อ</th>';
    echo '<th style="border: 1px solid black;"  scope="col">รุ่น</th>';
    echo '<th style="border: 1px solid black;"  scope="col">วันเดือนปีที่ซื้อ</th>';
    echo '<th style="border: 1px solid black;"  scope="col">ราคา</th>';
    echo '<th style="border: 1px solid black;"  scope="col">สถานะ</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch data from the database
    $sql = "SELECT *
            FROM device_asset";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $selectDepart = "SELECT * FROM depart WHERE depart_id = :depart_id";
        $stmt = $db->prepare($selectDepart);
        $stmt->bindParam(":depart_id", $row['depart_id']);
        $stmt->execute();
        $depart = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['status'] == 1) {
            $status = "ใช้งาน";
        } else {
            $status = "จำหน่าย";
        }
        echo '<tr style="text-align:center;">';
        echo '<td style="border: 1px solid black;">' . $row['computer_center_number'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $row['asset_number'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $row['list_device'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $depart['depart_name'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $row['brand'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $row['model'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $row['purchase_date'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $row['price'] . '</td>';
        echo '<td style="border: 1px solid black;">' . $status . '</td>';

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // Exit to prevent any further output
    exit();
}


// Check if the export action is triggered
if (isset($_POST['act'])) {
    echo '<head>';
    echo '<meta charset="UTF-8">';

    // Set headers for Excel file download
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=หมายเลขศูนย์คอม.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo '</head>';

    // Output the HTML for the table
    echo '<table style="border: 1px solid black;">';
    echo '<thead>';
    echo '<tr style="text-align:center;">';
    echo '<th style="border: 1px solid black;" scope="col">หมายเลขศูนย์คอม</th>';
    echo '<th style="border: 1px solid black;" scope="col">เลขครุภัณฑ์</th>';
    echo '<th style="border: 1px solid black;" scope="col">ชื่อเครื่อง</th>';
    echo '<th style="border: 1px solid black;" scope="col">หน่วยงาน</th>';
    echo '<th style="border: 1px solid black;" scope="col">ที่ตั้งอุปกรณ์</th>';
    echo '<th style="border: 1px solid black;" scope="col">วันเดือนปีซื้อ</th>';
    echo '<th style="border: 1px solid black;" scope="col">วันที่อัพเกรด</th>';
    echo '<th style="border: 1px solid black;" scope="col">ผู้อัพเกรด</th>';
    echo '<th style="border: 1px solid black;" scope="col">สถานะ</th>';
    echo '<th style="border: 1px solid black;" scope="col">CPU</th>';
    echo '<th style="border: 1px solid black;" scope="col">Socket</th>';
    echo '<th style="border: 1px solid black;" scope="col">Memory Type</th>';
    echo '<th style="border: 1px solid black;" scope="col">Memory capacity</th>';
    echo '<th style="border: 1px solid black;" scope="col">Motherboard brand</th>';
    echo '<th style="border: 1px solid black;" scope="col">Motherboard</th>';
    echo '<th style="border: 1px solid black;" scope="col">Storage</th>';
    echo '<th style="border: 1px solid black;" scope="col">Storage capacity</th>';
    echo '<th style="border: 1px solid black;" scope="col">Storage2</th>';
    echo '<th style="border: 1px solid black;" scope="col">Storage capacity2</th>';
    echo '<th style="border: 1px solid black;" scope="col">VGA</th>';
    echo '<th style="border: 1px solid black;" scope="col">OS</th>';
    // ... (other table headers)
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch data from the database
    $sql = "SELECT * 
    FROM computer_assets 
    WHERE (computer_center_number, id) IN (
        SELECT computer_center_number, MAX(id) AS max_id
        FROM computer_assets
        GROUP BY computer_center_number
    )
    ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output the table rows
    foreach ($result as $row) {
        $selectDepart = "SELECT * FROM depart WHERE depart_id = :depart_id";
        $stmt = $db->prepare($selectDepart);
        $stmt->bindParam(":depart_id", $row['department']);
        $stmt->execute();
        $depart = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['status'] == 1) {
            $status = "ใช้งาน";
        } else {
            $status = "จำหน่าย";
        }
        echo '<tr style="text-align: center;"';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['computer_center_number'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['asset_number'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['computer_name'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $depart['depart_name'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['equipment_location'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['purchase_date'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['upgrade_date'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['upgrading_person'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $status . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['CPU'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['socket'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['memory_type'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['memory_capacity'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['motherboard_brand'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['motherboard'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['storage'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['storage_capacity'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['storage2'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['storage_capacity2'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['VGA'] . '</td>';
        echo '<td style="border: 1px solid black;" scope="row">' . $row['OS'] . '</td>';

        // ... (other table data)
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // Exit to prevent any further output
    exit();
}

// Function to format Thai date
function toMonthThai($m)
{
    $monthNamesThai = array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );
    return $monthNamesThai[$m];
}

function formatDateThai($date)
{
    if ($date == null || $date == "") {
        return ""; // ถ้าวันที่เป็นค่าว่างให้คืนค่าว่างเปล่า
    }

    // แปลงวันที่ในรูปแบบ Y-m-d เป็น timestamp
    $timestamp = strtotime($date);

    // ดึงปีไทย
    $yearThai = date('Y', $timestamp);

    // ดึงเดือน
    $monthNumber = date('n', $timestamp);

    // แปลงเดือนเป็นภาษาไทย
    $monthThai = toMonthThai($monthNumber);

    // ดึงวันที่
    $day = date('d', $timestamp);

    // สร้างรูปแบบวันที่ใหม่
    $formattedDate = "$day $monthThai $yearThai";

    return $formattedDate;
}
