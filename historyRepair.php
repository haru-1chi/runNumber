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
    <div class="container mt-5 mb-5">

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
        <h2 class="mb-3">ประวัติการซ่อม</h2>
        <div class="table-responsive">
            <?php
            $sql = "SELECT * FROM data_report WHERE number_device = :number_device";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":number_device", $idCom);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table id="dataAll" class="table table-striped">
                <thead>
                    <tr>
                        <th>หมายเลขงาน</th>
                        <th>วันที่</th>
                        <th>เลขครุภัณฑ์</th>
                        <th>รูปแบบการทำงาน</th>
                        <th>อาการรับแจ้ง</th>
                        <th>ผู้แจ้ง</th>
                        <th>หน่วยงาน</th>
                        <th>เบอร์โทร</th>
                        <th>ดูรายละเอียด</th>
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
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['date_report'] ?></td>
                            <td><?= $row['number_device'] ?></td>
                            <td><?= $row['device'] ?></td>
                            <td><?= $row['report'] ?></td>
                            <td><?= $row['reporter'] ?></td>
                            <td><?= $depart['depart_name'] ?? '-' ?></td>
                            <td><?= $row['tel'] ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#com<?= $row['id'] ?>">ดูข้อมูล</button>
                                <!-- Modal -->
                                <div class="modal fade" id="com<?= $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">รายละเอียดงาน</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>หมายเลขงาน</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= $row['id'] ?>" disabled>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>วันที่</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= $row['date_report'] ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label>เวลาแจ้ง</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= date('H:i', strtotime($row['time_report'])) ?>" disabled>
                                                    </div>
                                                    <div class="col-4">
                                                        <label>เวลารับงาน</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= date('H:i', strtotime($row['take']))  ?>" disabled>
                                                    </div>
                                                    <div class="col-4">
                                                        <label>เวลาปิดงาน (ถ้ามี)</label>
                                                        <input disabled type="time" class="form-control" id="time_report" name="close_date"
                                                            value="<?= ($row['status'] == 3 && ($row['close_date'] === '00:00:00.000000' || $row['close_date'] === null || trim($row['close_date']) === ''))
                                                                        ? '' : (($row['close_date'] && $row['close_date'] !== '00:00:00.000000') ? date('H:i', strtotime($row['close_date'])) : '') ?>">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>ผู้แจ้ง</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= $row['reporter'] ?>" disabled>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>หน่วยงาน</label>
                                                        <?php
                                                        $sql = "SELECT depart_name FROM depart WHERE depart_id = ?";
                                                        $stmt = $db->prepare($sql);
                                                        $stmt->execute([$row['department']]);
                                                        $departRow = $stmt->fetch(PDO::FETCH_ASSOC);
                                                        ?>

                                                        <input type="text" class="form-control"
                                                            value="<?= $departRow['depart_name'] ?>" disabled>

                                                        <input type="hidden" name="department"
                                                            value="<?= $row['department'] ?>">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>เบอร์ติดต่อกลับ</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= $row['tel'] ?>" disabled>
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="deviceInput">อุปกรณ์</label>
                                                        <input disabled type="text" class="form-control" id="deviceInput<?= $row['id'] ?>" name="deviceName"
                                                            value="<?= $row['deviceName'] ?>">
                                                        <input type="hidden" id="deviceId<?= $row['id'] ?>">
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label> หมายเลขครุภัณฑ์ (ถ้ามี)</label>
                                                        <input disabled value="<?= $row['number_device'] ?>" type="text"
                                                            class="form-control" name="number_devices">
                                                    </div>
                                                    <div class="col-6">
                                                        <label>หมายเลข IP addrees</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= $row['ip_address'] ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>อาการที่ได้รับแจ้ง</label>
                                                        <input type="text" class="form-control"
                                                            value="<?= $row['report'] ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>รูปแบบการทำงาน<span style="color: red;">*</span></label>
                                                        <select disabled class="form-select" name="device"
                                                            aria-label="Default select example">
                                                            <option value="<?= $row['device'] ?: '' ?>"
                                                                selected>
                                                                <?= !empty($row['device']) ? $row['device'] : '-' ?>
                                                            </option>
                                                            <?php
                                                            $sql = "SELECT * FROM workinglist";
                                                            $stmt = $db->prepare($sql);
                                                            $stmt->execute();
                                                            $checkD = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                            foreach ($checkD as $d) {
                                                                if ($d['workingName'] != $row['device']) {
                                                            ?>
                                                                    <option value="<?= $d['workingName'] ?>">
                                                                        <?= $d['workingName'] ?>
                                                                    </option>
                                                            <?php }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>หมายเลขใบเบิก</label>
                                                        <?php if (empty($row['withdraw'])) { ?>
                                                            <input disabled type="text"
                                                                class="form-control withdrawInput" name="withdraw"
                                                                id="withdrawInput<?= $row['id'] ?>">
                                                        <?php } else { ?>
                                                            <input disabled value="<?= $row['withdraw'] ?>"
                                                                type="text" class="form-control withdrawInput"
                                                                name="withdraw" id="withdrawInput<?= $row['id'] ?>">
                                                            <input type="hidden" value="<?= $row['withdraw'] ?>"
                                                                class="form-control withdrawInput"
                                                                id="withdrawInputHidden<?= $row['id'] ?>" name="withdraw2">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>รายละเอียด<span style="color: red;">*</span></label>
                                                        <textarea disabled class="form-control " name="description" rows="2"><?= $row['description'] ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>หมายเหตุ</label>
                                                        <input disabled value="<?= $row['note'] ?>" type="text"
                                                            class="form-control" name="noteTask">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>ผู้คีย์งาน</label>
                                                        <input value="<?= $row['create_by'] ?>" type="text"
                                                            class="form-control" name="create_by" disabled>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>ซ่อมครั้งที่</label>
                                                        <?php
                                                        if (!empty($row['number_device']) && $row['number_device'] !== '-') {
                                                            $sqlCount = "SELECT COUNT(*) AS repair_count 
                     FROM data_report 
                     WHERE number_device = :number_device 
                     AND (number_device IS NOT NULL AND number_device <> '' AND number_device <> '-')";
                                                            $stmtCount = $db->prepare($sqlCount);
                                                            $stmtCount->bindParam(":number_device", $row['number_device']);
                                                            $stmtCount->execute();
                                                            $count = $stmtCount->fetch(PDO::FETCH_ASSOC);
                                                            $repairCount = $count['repair_count'];
                                                        } else {
                                                            $repairCount = '-';
                                                        }
                                                        ?>
                                                        <input disabled value="<?= $repairCount ?>" type="text" class="form-control" name="repair_count">
                                                    </div>
                                                </div>

                                                <hr class="mb-2">
                                                <!-- !!!!! -->
                                                <h4 class="mt-0 mb-3" id="staticBackdropLabel">งานคุณภาพ</h4>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>ปัญหาอยู่ใน SLA หรือไม่<span style="color: red;">*</span></label>
                                                        <select disabled class="form-select" name="sla"
                                                            aria-label="Default select example">
                                                            <option value="<?= $row['sla'] ?: '' ?>" selected>
                                                                <?= !empty($row['sla']) ? $row['sla'] : '-' ?>
                                                            </option>
                                                            <?php
                                                            $sql = "SELECT * FROM sla";
                                                            $stmt = $db->prepare($sql);
                                                            $stmt->execute();
                                                            $checkD = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                            foreach ($checkD as $d) {
                                                                if ($d['sla_name'] != $row['sla']) {
                                                            ?>
                                                                    <option value="<?= $d['sla_name'] ?>">
                                                                        <?= $d['sla_name'] ?>
                                                                    </option>
                                                            <?php }
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>เป็นตัวชี้วัดหรือไม่<span style="color: red;">*</span></label>
                                                        <select disabled class="form-select" name="kpi"
                                                            aria-label="Default select example">
                                                            <option value="<?= $row['kpi'] ?: '' ?>" selected>
                                                                <?= !empty($row['kpi']) ? $row['kpi'] : '-' ?>
                                                            </option>
                                                            <?php
                                                            $sql = "SELECT * FROM kpi";
                                                            $stmt = $db->prepare($sql);
                                                            $stmt->execute();
                                                            $checkD = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                            foreach ($checkD as $d) {
                                                                if ($d['kpi_name'] != $row['kpi']) {
                                                            ?>
                                                                    <option value="<?= $d['kpi_name'] ?>">
                                                                        <?= $d['kpi_name'] ?>
                                                                    </option>
                                                            <?php }
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>Activity Report<span style="color: red;">*</span></label>
                                                        <select disabled class="form-select" name="problem"
                                                            aria-label="Default select example">
                                                            <?php
                                                            $sql = "SELECT * FROM problemlist";
                                                            $stmt = $db->prepare($sql);
                                                            $stmt->execute();
                                                            $data = $stmt->fetchAll(PDO::FETCH_ASSOC); ?>
                                                            <option value="<?= $row['problem'] ?: '' ?>"
                                                                selected>
                                                                <?= !empty($row['problem']) ? $row['problem'] : '-' ?>
                                                            </option>
                                                            <?php foreach ($data as $d) {
                                                                if ($row['problem'] != $d['problemName']) { ?>
                                                                    <option value="<?= $d['problemName'] ?>">
                                                                        <?= $d['problemName'] ?>
                                                                    </option>
                                                            <?php }
                                                            }
                                                            ?>
                                                        </select>
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
    <footer class="mt-5 footer fixed-bottom mt-auto py-3" style="background: #fff;">

        <marquee class="font-thai" style="font-weight: bold; font-size: 1rem"><span class="text-muted text-center">Design website by นายอภิชน ประสาทศรี , พุฒิพงศ์ ใหญ่แก้ว &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Coding โดย นายอานุภาพ ศรเทียน &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ควบคุมโดย นนท์ บรรณวัฒน์ นักวิชาการคอมพิวเตอร์ ปฏิบัติการ</span>
        </marquee>

    </footer>
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