<?php
session_start();
require_once 'config/db.php';
require_once 'config/depart.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM computer_assets WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link to Bootstrap 5 CSS -->
    <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css">
    <title>เพิ่มข้อมูลครุภัณฑ์</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg" style="background-color: #365486;">
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
    </nav>
    <div class="container mt-5">
        <h1 class="mb-3">ข้อมูลครุภัณฑ์</h1>
        <form action="system/update" method="POST">
            <div class="row">
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="d-flex mt-5 justify-content-center">

                        <div class="alert w-50  alert-danger alert-dismissible" role="alert">
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php } ?>

                <?php if (isset($_SESSION['warning'])) { ?>
                    <div class="d-flex mt-5 justify-content-center">
                        <div class="alert w-50  alert-warning alert-dismissible" role="alert">
                            <?php
                            echo $_SESSION['warning'];
                            unset($_SESSION['warning']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>

                <?php } ?>

                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="d-flex mt-5 justify-content-center">
                        <div class="alert w-50  alert-success alert-dismissible" role="alert">
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>

                <?php } ?>
                <div class="col-sm-6">
                    <div class="mb-3" id="computer_center_number">
                        <label for="computer_center_number_input" class="form-label">หมายเลขศูนย์คอม:</label>
                        <input type="text" readonly name="computer_center_number" value="<?= $row['computer_center_number'] ?>" required class="form-control mb-3">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3" id="asset_number">
                        <label for="asset_number_input" class="form-label">เลขครุภัณฑ์:</label>
                        <input type="text" name="asset_number" value="<?= $row['asset_number'] ?>" class="form-control">
                    </div>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="computer_name">ชื่อเครื่อง:</label>
                    <input type="text" name="computer_name" value="<?= $row['computer_name'] ?>" class="form-control" required><br>
                </div>

                <?php
                $selectDepart = "SELECT * FROM depart WHERE depart_id = :depart_id";
                $stmt = $db->prepare($selectDepart);
                $stmt->bindParam(":depart_id", $row['department']);
                $stmt->execute();
                $depart = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="departInput">หน่วยงาน</label>
                    <input type="text" required class="form-control" value="<?= $depart['depart_name'] ?? '' ?>" id="departInput" name="ref_depart">
                    <input type="hidden" id="departName" name="depart_name">
                    <input type="hidden" id="departId" value="<?= $row['department'] ?>" name="depart_id">

                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.29/dist/sweetalert2.min.css">
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.29/dist/sweetalert2.min.js"></script>

                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

                    <script>
                        $(function() {
                            let inputChanged = false;
                            let alertShown = false;

                            $("#departInput").autocomplete({
                                    source: function(request, response) {
                                        $.ajax({
                                            url: "autocomplete.php",
                                            dataType: "json",
                                            data: {
                                                term: request.term
                                            },
                                            success: function(data) {
                                                response(data);
                                            }
                                        });
                                    },
                                    minLength: 1,
                                    select: function(event, ui) {
                                        $("#departInput").val(ui.item.label);
                                        $("#departId").val(ui.item.value);
                                        return false;
                                    },
                                    autoFocus: true
                                })
                                .data("ui-autocomplete")._renderItem = function(ul, item) {
                                    return $("<li>")
                                        .append("<div>" + item.label + "</div>")
                                        .appendTo(ul);
                                };

                            // Trigger select event when an item is highlighted
                            $("#departInput").on("autocompletefocus", function(event, ui) {
                                // $("#departInput").val(ui.item.label);
                                // $("#departId").val(ui.item.value);
                                return false;
                            });

                            $("#departInput").on("keyup", function() {
                                inputChanged = true;
                            });

                            $("#departInput").on("blur", function() {
                                if (inputChanged && !alertShown) {
                                    const userInput = $(this).val().trim();
                                    if (userInput === "") return;

                                    let found = false;
                                    $(this).autocomplete("instance").menu.element.find("div").each(function() {
                                        if ($(this).text() === userInput) {
                                            found = true;
                                            return false;
                                        }
                                    });

                                    if (!found) {
                                        alertShown = true; // Prevent the alert from firing again
                                        // Show SweetAlert to confirm insert data
                                        Swal.fire({
                                            title: "คุณต้องการเพิ่มข้อมูลนี้หรือไม่?",
                                            icon: "info",
                                            showCancelButton: true,
                                            confirmButtonText: "ใช่",
                                            cancelButtonText: "ไม่"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $.ajax({
                                                    url: "insertDepart.php",
                                                    method: "POST",
                                                    data: {
                                                        dataToInsert: userInput
                                                    },
                                                    success: function(response) {
                                                        console.log("Data inserted successfully!");
                                                        $("#departId").val(response); // Set inserted ID
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error("Error inserting data:", error);
                                                    }
                                                });
                                            } else {
                                                $("#departInput").val(""); // Clear input if canceled
                                                $("#departId").val("");
                                            }
                                            alertShown = false; // Reset the flag after the action
                                        });
                                    }
                                }
                                inputChanged = false; // Reset the flag
                            });
                        });
                    </script>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="equipment_location">ที่ตั้งอุปกรณ์:</label>
                    <input type="text" name="equipment_location" value="<?= $row['equipment_location'] ?>" class="form-control" required><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="purchase_date">วันเดือนปีซื้อ:</label>
                    <input type="date" name="purchase_date" value="<?= $row['purchase_date'] ?>" class="form-control" required><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label">วันที่อัพเกรด :</label>
                    <input type="date" name="upgrade_date" class="form-control" value="<?= $row['upgrade_date'] ?>">
                    <input type="hidden" name="computer_id" class="form-control" value="<?= $row['id'] ?>">
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="upgrading_person">ผู้อัพเกรด:</label>
                    <input type="text" name="upgrading_person" value="<?= $row['upgrading_person'] ?>" class="form-control" required><br>
                </div>
                <div class="col-sm-12 mb-3">
                    <input type="file" class="form-control mb-3" id="fileInput" accept=".txt" />
                    <button type="button" class="btn btn-primary" onclick="loadAndExtract()">Load File</button>
                </div>
                <h1>ข้อมูลจาก CPU-Z</h1>
                <hr>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">CPU:</label>
                    <input type="text" id="userCpu" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Socket:</label>
                    <input type="text" id="userSocket" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Memory Type:</label>
                    <input type="text" id="userMemoryType" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Memory Capacity:</label>
                    <input type="text" id="userMemoryCapacity" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Motherboard Brand:</label>
                    <input type="text" id="userMotherboardBrand" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Motherboard:</label>
                    <input type="text" id="userMotherboard" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Storage:</label>
                    <input type="text" id="userStorage" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">Storage Capacity:</label>
                    <input type="text" id="userStorageCapacity" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label" for="cpu">VGA:</label>
                    <input type="text" id="userVGA" class="form-control">
                </div>
                <div class="col-sm-2 mb-3">
                    <label class="form-label" for="cpu">OS:</label>
                    <input type="text" id="userOS" class="form-control">
                </div>
                <hr>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">CPU :</label>
                    <input type="text" id="cpu" name="CPU" class="form-control" value="<?= $row['CPU'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Socket :</label>
                    <input type="text" id="socket" name="socket" class="form-control" value="<?= $row['socket'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Memory Type :</label>
                    <input type="text" id="memoryType" name="memory_type" class="form-control" value="<?= $row['memory_type'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Memory Capacity :</label>
                    <input type="text" id="memoryCapacity" name="memory_capacity" class="form-control" value="<?= $row['memory_capacity'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Motherboard Brand :</label>
                    <input type="text" id="motherboardBrand" name="motherboard_brand" class="form-control" value="<?= $row['motherboard_brand'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Motherboard :</label>
                    <input type="text" id="motherboard" name="motherboard" class="form-control" value="<?= $row['motherboard'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Storage :</label>
                    <input type="text" id="storage" name="storage" class="form-control" value="<?= $row['storage'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Storage Capacity :</label>
                    <input type="text" id="storageCapacity" name="storage_capacity" class="form-control" value="<?= $row['storage_capacity'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Storage2 :</label>
                    <input type="text" name="storage2" class="form-control" value="<?= $row['storage2'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">Storage Capacity2 :</label>
                    <input type="text" name="storage_capacity2" class="form-control" value="<?= $row['storage_capacity2'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">VGA :</label>
                    <input type="text" id="vga" name="VGA" class="form-control" value="<?= $row['VGA'] ?>">
                </div>
                <div class="col-sm-3 mb-3">
                    <label class="form-label">OS :</label>
                    <input type="text" id="os" name="OS" class="form-control" value="<?= $row['OS'] ?>">
                </div>
                <div class="col-sm-12 mb-3">
                    <div class="d-grid gap-2">
                        <button type="submit" name="update" class="btn p-3 btn-primary mt-3">อัพเดทข้อมูล</button>
                        <a href="index" class="btn p-3 btn-secondary">กลับ</a>
                    </div>
                </div>
            </div>


        </form>
    </div>
    <script>
        function loadAndExtract() {
            const fileInput = document.getElementById("fileInput");

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const content = e.target.result;

                    const cpu = extractValue(content, "Name");
                    const socket = extractValue(content, "Package");
                    const memoryType = extractValue(content, "Memory Type");
                    const memoryCapacity = extractValue(content, "Memory Size");
                    const motherboardBrand = extractValue(content, "Board Manufacturer");
                    const motherboard = extractValue(content, "Mainboard Model");
                    const storage = extractStorageInfo(content, "Name");
                    const storageCapacity = extractValue(content, "Capacity");
                    const vga = extractDisplayAdapterInfo(content, 1)?.name || extractDisplayAdapterInfo(content, 0)?.name || "N/A";
                    const os = extractValue(content, "Windows Version");

                    // Set values to input fields
                    document.getElementById("cpu").value = cpu;
                    document.getElementById("socket").value = socket;
                    document.getElementById("memoryType").value = memoryType;
                    document.getElementById("memoryCapacity").value = memoryCapacity;
                    document.getElementById("motherboardBrand").value = motherboardBrand;
                    document.getElementById("motherboard").value = motherboard;
                    document.getElementById("storage").value = storage;
                    document.getElementById("storageCapacity").value = storageCapacity;
                    document.getElementById("vga").value = vga;
                    document.getElementById("os").value = os;

                    console.log("CPU:", cpu);
                    console.log("Socket:", socket);
                    console.log("Memory Type:", memoryType);
                    console.log("Memory Capacity:", memoryCapacity);
                    console.log("Motherboard Brand:", motherboardBrand);
                    console.log("Motherboard:", motherboard);
                    console.log("Storage:", storage);
                    console.log("Storage Capacity:", storageCapacity);
                    console.log("VGA:", vga);
                    console.log("OS:", os);
                };

                reader.readAsText(file);
            }
        }

        function extractValue(content, key) {
            const pattern = new RegExp(`${key}\\s+(.+)`);
            const match = content.match(pattern);
            return match ? match[1].trim() : "N/A";
        }

        function extractDisplayAdapterInfo(content, adapterIndex) {
            const regex = new RegExp(`Display adapter ${adapterIndex}\\s+ID\\s+(.+)\\s+Name\\s+(.+)`);
            const match = content.match(regex);

            if (match) {
                const [, id, name] = match;
                return {
                    id,
                    name,
                };
            } else {
                return null;
            }
        }

        function extractStorageInfo(content) {
            const regex = new RegExp(`Drive[\\s\\S]+?Name\\s+(.+)`);
            const match = content.match(regex);

            return match ? match[1].trim() : null;
        }
    </script>

    <!-- Link to Bootstrap 5 JS, if needed -->
    <script src="bootstrap/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
</body>

</html>