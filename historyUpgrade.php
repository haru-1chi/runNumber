<?php
session_start();
require_once 'config/db.php';
require_once 'config/depart.php';
require_once 'navbar.php';

if (!isset($_SESSION["admin_log"])) {
    $_SESSION["warning"] = "กรุณาเข้าสู่ระบบ";
    header("location: login");
    unset($_SESSION['admin_log']);
}

$idCom = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="bootstrap/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">

    <title>ระบบจัดการครุฑภัณฑ์</title>
</head>

<body>
    <?php navbar(); ?>
    <!-- <nav class="navbar navbar-expand-lg" style="background-color: #365486;">
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
            <?php
            $sql = "SELECT * FROM computer_assets WHERE computer_center_number = :computer_center_number";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":computer_center_number", $idCom);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table id="dataAll" class="table table-striped">
                <thead>
                    <tr>
                        <th>หมายเลขศูนย์คอม</th>
                        <th>เลขครุภัณฑ์</th>
                        <th>ชื่อเครื่อง</th>
                        <th>หน่วยงาน</th>
                        <th>ที่ตั้งอุปกรณ์</th>
                        <th>วันเดือนปีซื้อ</th>
                        <th>วันที่อัพเกรด</th>
                        <th>ผู้อัพเกรด</th>
                        <th>ประทับเวลา</th>
                        <th>ดูข้อมูล</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row) {
                        $selectDepart = "SELECT * FROM depart WHERE depart_id = :depart_id";
                        $stmt = $db->prepare($selectDepart);
                        $stmt->bindParam(":depart_id", $row['department']);
                        $stmt->execute();
                        $depart = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                        <tr>
                            <td><?= $row['computer_center_number'] ?></td>
                            <td><?= $row['asset_number'] ?></td>
                            <td><?= $row['computer_name'] ?></td>
                            <td><?= $depart['depart_name'] ?? '-' ?></td>
                            <td><?= $row['equipment_location'] ?></td>
                            <td><?= $row['purchase_date'] ?></td>
                            <td><?= $row['upgrade_date'] ?></td>
                            <td><?= $row['upgrading_person'] ?></td>
                            <td><?= $row['timestamp'] ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#com<?= $row['id'] ?>">ดูข้อมูล</button>
                                <!-- Modal -->
                                <div class="modal fade" id="com<?= $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">ข้อมูลของเครื่อง <?= $row['computer_name'] ?></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <b>CPU :</b>
                                                        <span><?= $row['CPU'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Socket :</b>
                                                        <span><?= $row['socket'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Memory Type :</b>
                                                        <span><?= $row['memory_type'] ?></span>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <b>Memory_capacity :</b>
                                                        <span><?= $row['memory_capacity'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Motherboard_brand :</b>
                                                        <span><?= $row['motherboard_brand'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Motherboard :</b>
                                                        <span><?= $row['motherboard'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage :</b>
                                                        <span><?= $row['storage'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage_capacity :</b>
                                                        <span><?= $row['storage_capacity'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage2 :</b>
                                                        <span><?= $row['storage2'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>Storage_capacity2 :</b>
                                                        <span><?= $row['storage_capacity2'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>VGA :</b>
                                                        <span><?= $row['VGA'] ?></span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <b>OS :</b>
                                                        <span><?= $row['OS'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        <?php  } ?>
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
                order: [
                    [0, 'desc']
                ] // assuming you want to sort the first column in ascending order
            });

            $('#dataAllTAKE').DataTable({
                order: [
                    [0, 'desc']
                ] // adjust the column index as needed
            });


        });
    </script>
</body>

</html>