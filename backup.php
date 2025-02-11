<tbody>
                    <?php foreach ($result as $row) { ?>
                        <tr>
                            <td><?= $row['computer_center_number'] ?></td>
                            <td><?= $row['asset_number'] ?></td>
                            <td><?= $row['computer_name'] ?></td>
                            <td><?= $row['depart_name'] ?? '-' ?></td>
                            <td><?= $row['equipment_location'] ?></td>
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
                            <td>
                                <?php
                                $sql = "SELECT * FROM data_report WHERE number_device = :number_device";
                                $stmt = $db->prepare($sql);
                                $stmt->bindParam(":number_device", $row['asset_number']);
                                $stmt->execute();
                                $historyExists = $stmt->rowCount() > 0; // Check if there are records

                                if (!empty($row['asset_number']) && $row['asset_number'] !== '-' && $historyExists) {
                                ?>
                                    <a class="btn btn-primary" href="historyRepair?id=<?= htmlspecialchars($row['asset_number']) ?>">ดูประวัติ</a>
                                <?php
                                } else {
                                    echo 'ไม่มี';
                                }
                                ?>
                            </td>
                            <td><a href="edit?id=<?= $row['id'] ?>" class="btn btn-warning btn-action">แก้ไข</a></td>

                            <td>
                                <a class="btn btn-danger btn-action" href="?id=<?= htmlspecialchars($row['computer_center_number']) ?>" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่?')">ลบข้อมูล</a>
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

                  <!-- General Device Table -->
        <hr>
        <h2 class="mb-3">อุปกรณ์ทั่วไป</h2>
        <div class="table-responsive">
            <?php
            $sql = "
            SELECT da.*, d.depart_name 
FROM run_number.device_asset da
LEFT JOIN OrderIT.depart d ON da.depart_id = d.depart_id
WHERE da.is_deleted != 1
            ";
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
                        <th>ประวัติการซ่อม</th>
                        <th>แก้ไข</th>
                        <th>ลบ</th>
                        <th>พิมพ์</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row) {?>
                        <tr>
                            <td><?= $row['computer_center_number'] ?></td>
                            <td><?= $row['asset_number'] ?></td>
                            <td><?= $row['list_device'] ?></td>
                            <td><?= $row['depart_name'] ?? '-' ?></td>
                            <td><?= $row['brand'] ?></td>
                            <td><?= $row['model'] ?></td>
                            <td><?= $row['purchase_date'] ?></td>
                            <td><?= $row['price'] ?></td>
                            <td>
                                <?php
                                $sql = "SELECT * FROM data_report WHERE number_device = :number_device";
                                $stmt = $db->prepare($sql);
                                $stmt->bindParam(":number_device", $row['asset_number']);
                                $stmt->execute();
                                $historyExists = $stmt->rowCount() > 0; // Check if there are records

                                if (!empty($row['asset_number']) && $row['asset_number'] !== '-' && $historyExists) {
                                ?>
                                    <a class="btn btn-primary" href="historyRepair?id=<?= htmlspecialchars($row['asset_number']) ?>">ดูประวัติ</a>
                                <?php
                                } else {
                                    echo 'ไม่มี';
                                }
                                ?>
                            </td>
                            <td><a href="editDevice?id=<?= $row['id'] ?>" class="btn btn-warning btn-action">แก้ไข</a></td>
                            <td><a class="btn btn-danger btn-action" href="?idDevice=<?= htmlspecialchars($row['computer_center_number']) ?>" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบข้อมูล</a></td>
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