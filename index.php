<?php
session_start();
require_once 'config/db.php';
require_once 'config/depart.php';
require_once 'navbar.php';

if (!isset($_SESSION["admin_log"])) {
    $_SESSION["warning"] = "กรุณาเข้าสู่ระบบ";
    header("location: login");
    unset($_SESSION['admin_log']);
    exit();
}

if (isset($_GET['id'])) {
    require 'config/db.php'; // Ensure database connection is included

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

    if ($id) {
        $sql = "UPDATE computer_assets SET is_deleted = 1 WHERE computer_center_number = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
            header("Location: index");
            exit(); // Stop script execution after redirection
        } else {
            echo "Error updating record.";
        }
    } else {
        echo "Invalid ID.";
    }
}
if (isset($_GET['idDevice'])) {
    require 'config/db.php';  // Ensure database connection is included

    // Sanitize input to prevent SQL Injection
    $id = filter_input(INPUT_GET, 'idDevice', FILTER_SANITIZE_STRING);

    if ($id) {
        $sql = "UPDATE device_asset SET is_deleted = 1 WHERE computer_center_number = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
            header("Location: index");
            exit(); // Stop further execution
        } else {
            echo "Error updating record.";
        }
    } else {
        echo "Invalid ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css"> -->
    <link rel="shortcut icon" href="image/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="bootstrap/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">

    <title>ระบบจัดการครุฑภัณฑ์</title>
</head>

<body>
    <?php navbar(); ?>
    <!--<nav class="navbar navbar-expand-lg" style="background-color: #365486;">
        <div class="container p-2" style="background-color: #365486; box-shadow: none;">
            <a class="navbar-brand" href="../orderit/dashboard.php" style="color: #ffffff; font-weight: 900;">ระบบบริหารงานซ่อม</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" style="color: #ffffff;" href="create">เพิ่มข้อมูลคอมพิวเตอร์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="color: #ffffff;" href="createDevice">เพิ่มอุปกรณ์ทั่วไป</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ms-5" style="color: #ffffff;" href="system/logout">ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav> -->
    <div class="container mt-5 mb-4">
        <hr>
        <!-- Alerts -->
        <?php foreach (['error', 'warning', 'success'] as $type) {
            if (isset($_SESSION[$type])) { ?>
                <div class="alert alert-<?php echo $type; ?> mt-3" role="alert">
                    <?php echo $_SESSION[$type]; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php
                unset($_SESSION[$type]);
            }
        }
        ?>

        <!-- Computer Table -->
        <h2 class="mb-3">คอมพิวเตอร์</h2>
        <div class="table-responsive">
            <form method="post" action="export.php">
                <button name="act" class="btn btn-primary mb-3" type="submit">Export->Excel</button>
            </form>
            <table id="dataAll" class="table table-striped">
                <thead>
                    <tr>
                        <th>หมายเลขศูนย์คอม</th>
                        <th>เลขครุภัณฑ์</th>
                        <th>ชื่อเครื่อง</th>
                        <th>หน่วยงาน</th>
                        <th>ที่ตั้งอุปกรณ์</th>
                        <th>ดูข้อมูล</th>
                        <th>ประวัติการอัพเกรด</th>
                        <th>ประวัติการซ่อม</th>
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                        <th>พิมพ์</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
            </table>
        </div>
        <hr>
        <h2 class="mb-3">อุปกรณ์ทั่วไป</h2>
        <div class="table-responsive">
            <form method="post" action="export.php">
                <button name="actAll" class="btn btn-primary mb-3" type="submit">Export->Excel</button>
            </form>
            <table id="dataAllTAKE" class="table table-striped">
                <thead>
                    <tr>
                        <th>หมายเลขศูนย์คอม</th>
                        <th>เลขครุภัณฑ์</th>
                        <th>รายการ</th>
                        <th>หน่วยงาน</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>วันเดือนปีที่ซื้อ</th>
                        <th>ราคา</th>
                        <th>ประวัติการซ่อม</th>
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                        <th>พิมพ์</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Link to Bootstrap 5 JS, if needed -->
    <!-- <script src="bootstrap/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script> -->
    <script src="bootstrap/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataAll').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'fetchDataCom.php',
                    type: 'POST'
                },
                columns: [{
                        data: 'computer_center_number'
                    },
                    {
                        data: 'asset_number'
                    },
                    {
                        data: 'computer_name'
                    },
                    {
                        data: 'depart_name'
                    },
                    {
                        data: 'equipment_location'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#com${data}">ดูข้อมูล</button>
                        <div class="modal fade" id="com${data}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">ข้อมูลของเครื่อง ${row.computer_name}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <b>CPU :</b>
                                                        <span>${row.CPU}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Socket :</b>
                                                        <span>${row.socket}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Memory Type :</b>
                                                        <span>${row.memory_type}</span>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <b>Memory_capacity :</b>
                                                        <span>${row.memory_capacity}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Motherboard_brand :</b>
                                                        <span>${row.motherboard_brand}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Motherboard :</b>
                                                        <span>${row.motherboard}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage :</b>
                                                        <span>${row.storage}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage_capacity :</b>
                                                        <span>${row.storage_capacity}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage2 :</b>
                                                        <span>${row.storage2}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage_capacity2 :</b>
                                                        <span>${row.storage_capacity2}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>VGA :</b>
                                                        <span>${row.VGA}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>OS :</b>
                                                        <span>${row.OS}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    `;
                        }
                    },
                    {
                        data: 'computer_center_number',
                        render: function(data) {
                            return '<a class="btn btn-primary" href="historyUpgrade?id=' + data + '">ดูประวัติ</a>';
                        }
                    },
                    {
                        data: 'asset_number',
                        render: function(data, type, row) {
                            if (row.historyExists && data && data !== '-') {
                                return `<a class="btn btn-primary" href="historyRepair?id=${encodeURIComponent(data)}">ดูประวัติ</a>`;
                            } else {
                                return 'ไม่มี';
                            }
                        }
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return '<a href="edit?id=' + data + '" class="btn btn-warning btn-action">แก้ไข</a>';
                        }
                    },
                    {
                        data: 'computer_center_number',
                        render: function(data) {
                            return '<a class="btn btn-danger btn-action" href="?id=' + data + '" onclick="return confirm(\'ต้องการลบข้อมูลใช่หรือไม่?\')">ลบข้อมูล</a>';
                        }
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return '<a class="btn btn-secondary btn-action" target="_blank" href="printData?id=' + data + '">พิมพ์</a>';
                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            let statusText = data == 2 ? 'จำหน่ายแล้ว' : 'ใช้งาน';
                            let btnClass = data == 2 ? 'btn-danger' : 'btn-success';
                            let modalId = 'statusModal' + row.id;

                            return `
                        <button type="button" class="btn ${btnClass}" data-bs-toggle="modal" data-bs-target="#${modalId}">
                            ${statusText}
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="modalLabel${row.id}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel${row.id}">แก้ไขสถานะ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="updateStatusForm">
                                            <input type="hidden" name="id" value="${row.id}">
                                            <select class="form-select" name="status">
                                                <option value="1" ${data == 1 ? 'selected' : ''}>ใช้งาน</option>
                                                <option value="2" ${data == 2 ? 'selected' : ''}>จำหน่ายแล้ว</option>
                                            </select>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="button" class="btn btn-primary saveStatusBtn" data-id="${row.id}">บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                searching: true, // Enable search
                paging: true,
            });


            $('#dataAllTAKE').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'fetchDataDevice.php',
                    type: 'POST'
                },
                columns: [{
                        data: 'computer_center_number'
                    },
                    {
                        data: 'asset_number'
                    },
                    {
                        data: 'list_device'
                    },
                    {
                        data: 'depart_name'
                    },
                    {
                        data: 'brand'
                    },
                    {
                        data: 'model'
                    },
                    {
                        data: 'purchase_date'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'asset_number',
                        render: function(data, type, row) {
                            if (row.historyExists && data && data !== '-') {
                                return `<a class="btn btn-primary" href="historyRepair?id=${encodeURIComponent(data)}">ดูประวัติ</a>`;
                            } else {
                                return 'ไม่มี';
                            }
                        }
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return '<a href="editDevice?id=' + data + '" class="btn btn-warning btn-action">แก้ไข</a>';
                        }
                    },
                    {
                        data: 'computer_center_number',
                        render: function(data) {
                            return '<a class="btn btn-danger btn-action" href="?idDevice=' + data + '" onclick="return confirm(\'ต้องการลบข้อมูลใช่หรือไม่?\')">ลบข้อมูล</a>';
                        }
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return '<a class="btn btn-secondary btn-action" target="_blank" href="printDataP?id=' + data + '">พิมพ์</a>';
                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            let statusText = data == 2 ? 'จำหน่ายแล้ว' : 'ใช้งาน';
                            let btnClass = data == 2 ? 'btn-danger' : 'btn-success';
                            let modalId = 'statusModal' + row.id;

                            return `
                        <button type="button" class="btn ${btnClass}" data-bs-toggle="modal" data-bs-target="#${modalId}">
                            ${statusText}
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="modalLabel${row.id}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel${row.id}">แก้ไขสถานะ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="updateStatusForm">
                                            <input type="hidden" name="id" value="${row.id}">
                                            <select class="form-select" name="status">
                                                <option value="1" ${data == 1 ? 'selected' : ''}>ใช้งาน</option>
                                                <option value="2" ${data == 2 ? 'selected' : ''}>จำหน่ายแล้ว</option>
                                            </select>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="button" class="btn btn-primary saveStatusBtnDevice" data-id="${row.id}">บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                searching: true, // Enable search
                paging: true,
            });

            $(document).on("click", ".saveStatusBtn", function() {
                let id = $(this).data("id");
                let modal = $(this).closest(".modal");
                let newStatus = modal.find("select[name='status']").val();

                $.ajax({
                    url: "system/update.php",
                    type: "POST",
                    data: {
                        updateStatus: true,
                        id: id,
                        status: newStatus
                    },
                    success: function(response) {
                        modal.modal("hide");
                        $('#dataAll').DataTable().ajax.reload(); // Refresh DataTable
                    },
                    error: function() {
                        alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
                    }
                });
            });

            $(document).on("click", ".saveStatusBtnDevice", function() {
                let id = $(this).data("id");
                let modal = $(this).closest(".modal");
                let newStatus = modal.find("select[name='status']").val();

                $.ajax({
                    url: "system/update.php",
                    type: "POST",
                    data: {
                        updateStatusDevice: true,
                        id: id,
                        status: newStatus
                    },
                    success: function(response) {
                        modal.modal("hide");
                        $('#dataAllTAKE').DataTable().ajax.reload(); // Refresh DataTable
                    },
                    error: function() {
                        alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
                    }
                });
            });
        });
    </script>
</body>

</html>