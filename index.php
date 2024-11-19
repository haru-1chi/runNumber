<?php
session_start();
require_once 'config/db.php';
require_once 'config/depart.php';

if (!isset($_SESSION["admin_log"])) {
    $_SESSION["warning"] = "กรุณาเข้าสู่ระบบ";
    header("location: login");
    unset($_SESSION['admin_log']);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM computer_assets WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    header("Location: index");
}
if (isset($_GET['idDevice'])) {
    $id = $_GET['idDevice'];
    $sql = "DELETE FROM device_asset WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    header("Location: index");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">

    <title>ระบบจัดการครุฑภัณฑ์</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg" style="background-color: #365486;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index" style="color: #ffffff;">ระบบครุภัณฑ์คอมพิวเตอร์</a>
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
                        <a class="nav-link" style="color: #ffffff;" href="system/logout">ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 mb-5">
        <!-- <h1 class="text-center mb-4">ระบบจัดการครุภัณฑ์</h1>
        <div class="text-center mb-3">
            <a class="btn btn-primary btn-action" href="create">เพิ่มข้อมูลคอมพิวเตอร์</a>
            <a class="btn btn-primary btn-action" href="createDevice">เพิ่มอุปกรณ์ทั่วไป</a>
            <a class="btn btn-danger btn-action" href="system/logout">ออกจากระบบ</a>
        </div> -->
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
            <?php
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
            ?>
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
                        <th>วันเดือนปีซื้อ</th>
                        <th>วันที่อัพเกรด</th>
                        <th>ผู้อัพเกรด</th>
                        <th>ดูข้อมูล</th>
                        <th>ประวัติการอัพเกรด</th>
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                        <th>พิมพ์</th>
                        <th>สถานะ</th>
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
                            <td><?= $depart['depart_name'] ?></td>
                            <td><?= $row['equipment_location'] ?></td>
                            <td><?= $row['purchase_date'] ?></td>
                            <td><?= $row['upgrade_date'] ?></td>
                            <td><?= $row['upgrading_person'] ?></td>
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
                            <td><a class="btn btn-primary" href="historyUpgrade?id=<?= $row['computer_center_number'] ?>">ดูประวัติ</a></td>

                            <td><a href="edit?id=<?= $row['id'] ?>" class="btn btn-warning btn-action">แก้ไข</a></td>

                            <td>
                                <a class="btn btn-danger btn-action" href="?id=<?= $row['id'] ?>" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบข้อมูล</a>
                            </td>

                            <td><a class="btn btn-secondary btn-action" target="_blank" href="printData?id=<?= $row['id'] ?>">พิมพ์</a></td>

                            <td>
                                <?php if ($row['status'] == 2) { ?>
                                    <button data-bs-toggle="modal" data-bs-target="#exampleModal<?= $row['id'] ?>" type="button" class="btn btn-danger">
                                        จำหน่ายแล้ว
                                    </button>
                                <?php } else { ?>
                                    <button data-bs-toggle="modal" data-bs-target="#exampleModal<?= $row['id'] ?>" type="button" class="btn btn-success">
                                        ใช้งาน
                                    </button>
                                <?php } ?>


                                <div class="modal fade" id="exampleModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">แก้ไขสถานะ</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="system/update" method="post">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                                    <select class="form-select" name="status">
                                                        <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>ใช้งาน</option>
                                                        <option value="2" <?php echo ($row['status'] == 2) ? 'selected' : ''; ?>>จำหน่ายแล้ว</option>
                                                    </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                <button type="submit" name="updateStatus" class="btn btn-primary">บันทึก</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    <?php  } ?>
                </tbody>
            </table>
        </div>

        <!-- General Device Table -->
        <hr>
        <h2 class="mb-3">อุปกรณ์ทั่วไป</h2>
        <div class="table-responsive">
            <?php
            $sql = "SELECT * FROM device_asset";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
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
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                        <th>พิมพ์</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row) {
                        $selectDepart = "SELECT * FROM depart WHERE depart_id = :depart_id";
                        $stmt = $db->prepare($selectDepart);
                        $stmt->bindParam(":depart_id", $row['depart_id']);
                        $stmt->execute();
                        $depart = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                        <tr>
                            <td><?= $row['computer_center_number'] ?></td>
                            <td><?= $row['asset_number'] ?></td>
                            <td><?= $row['list_device'] ?></td>
                            <td><?= $depart['depart_name'] ?></td>
                            <td><?= $row['brand'] ?></td>
                            <td><?= $row['model'] ?></td>
                            <td><?= $row['purchase_date'] ?></td>
                            <td><?= $row['price'] ?></td>
                            <td><a href="editDevice?id=<?= $row['id'] ?>" class="btn btn-warning btn-action">แก้ไข</a></td>
                            <td><a class="btn btn-danger btn-action" href="?idDevice=<?= $row['id'] ?>" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบข้อมูล</a></td>
                            <td><a class="btn btn-secondary btn-action" target="_blank" href="printDataP?id=<?= $row['id'] ?>">พิมพ์</a></td>
                            <td>
                                <?php if ($row['status'] == 2) { ?>
                                    <button data-bs-toggle="modal" data-bs-target="#exampleModal<?= $row['id'] ?>" type="button" class="btn btn-danger">
                                        จำหน่ายแล้ว
                                    </button>
                                <?php } else { ?>
                                    <button data-bs-toggle="modal" data-bs-target="#exampleModalE<?= $row['id'] ?>" type="button" class="btn btn-success">
                                        ใช้งาน
                                    </button>
                                <?php } ?>


                                <div class="modal fade" id="exampleModalE<?= $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">แก้ไขสถานะ</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="system/update" method="post">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                                    <select class="form-select" name="status">
                                                        <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>ใช้งาน</option>
                                                        <option value="2" <?php echo ($row['status'] == 2) ? 'selected' : ''; ?>>จำหน่ายแล้ว</option>
                                                    </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                <button type="submit" name="updateStatus" class="btn btn-primary">บันทึก</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php  } ?>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <br>
    <footer class="mt-5 footer fixed-bottom mt-auto py-3" style="background: #fff;">

        <marquee class="font-thai" style="font-weight: bold; font-size: 1rem"><span class="text-muted text-center">Design website by นายอภิชน ประสาทศรี , พุฒิพงศ์ ใหญ่แก้ว &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Coding โดย นายอานุภาพ ศรเทียน &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ควบคุมโดย นนท์ บรรณวัฒน์ นักวิชาการคอมพิวเตอร์ ปฏิบัติการ</span>
        </marquee>

    </footer>
    <!-- Link to Bootstrap 5 JS, if needed -->
    <script src="bootstrap/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
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