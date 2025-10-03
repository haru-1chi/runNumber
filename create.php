<?php
session_start();
require_once 'config/db.php';
require_once 'navbar.php';

$sql = "SELECT * FROM computer_assets";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lastComputerNumber = ""; // ตั้งค่าเริ่มต้นเป็นสตริงว่าง

foreach ($result as $row) {
    $lastComputerNumber = $row['computer_center_number'];
}


// เรียกใช้ฟังก์ชันเพื่อตรวจสอบและเพิ่มเลข
$newComputerNumber = generateNewComputerNumber($lastComputerNumber);

// echo "New Computer Number: $newComputerNumber";

// ฟังก์ชันสำหรับตรวจสอบและเพิ่มเลข
function generateNewComputerNumber($lastNumber)
{
    // ตรวจสอบว่าหมายเลขศูนย์คอมมีรูปแบบ C หรือไม่
    if (preg_match('/^C(\d+)$/', $lastNumber, $matches)) {
        // เลขปัจจุบัน
        $currentNumber = (int)$matches[1];

        // เพิ่มเลขที่ต่อท้ายไป 1 ค่า
        $newNumber = $currentNumber + 1;

        // สร้างหมายเลขใหม่ (เติม 0 ในหน้าตัวเลข)
        $newComputerNumber = "C" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        return $newComputerNumber;
    } else {
        return "C00001";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลครุภัณฑ์</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="bootstrap/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css"> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
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
        <h1 class="mb-3">เพิ่มข้อมูลครุภัณฑ์</h1>
        <hr>

        <form method="post" action="system/insertComputer">
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
                <div class="col-sm-12">
                    <div class="mb-3" id="asset_number">
                        <label for="asset_number_input" class="form-label">เลขครุภัณฑ์:</label>
                        <input value="-" type="text" name="asset_number" class="form-control">
                    </div>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="computer_name">ชื่อเครื่อง:</label>
                    <input type="text" name="computer_name" class="form-control" required><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="departInput">หน่วยงาน</label>
                    <input type="text" required class="form-control" id="departInput" name="ref_depart" required>
                    <input type="hidden" id="departName" name="depart_name">
                    <input type="hidden" id="departId" name="depart_id">

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
                    <input type="text" name="equipment_location" class="form-control" required><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="purchase_date">วันเดือนปีซื้อ:</label>
                    <input type="date" name="purchase_date" class="form-control" required><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="upgrade_date">วันที่อัพเกรด:</label>
                    <input type="date" name="upgrade_date" class="form-control"><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="upgrading_person">ผู้อัพเกรด:</label>
                    <input type="text" name="upgrading_person" class="form-control"><br>
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

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="cpu">CPU:</label>
                    <input type="text" id="cpu" name="cpu" class="form-control"><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="socket">Socket:</label>
                    <input type="text" id="socket" name="socket" class="form-control"><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="memory_type">Memory Type:</label>
                    <input type="text" id="memoryType" name="memory_type" class="form-control"><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="memory_capacity">Memory Capacity:</label>
                    <input type="text" id="memoryCapacity" name="memory_capacity" class="form-control"><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="motherboard_brand">Motherboard Brand:</label>
                    <input type="text" id="motherboardBrand" name="motherboard_brand" class="form-control"><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="motherboard">Motherboard:</label>
                    <input type="text" id="motherboard" name="motherboard" class="form-control"><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="storage">Storage:</label>
                    <input type="text" id="storage" name="storage" class="form-control"><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="storage_capacity">Storage Capacity:</label>
                    <input type="text" id="storageCapacity" name="storage_capacity" class="form-control"><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="storage_capacity">Storage2:</label>
                    <input type="text" name="storage2" class="form-control"><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="storage_capacity">Storage Capacity2:</label>
                    <input type="text" name="storage_capacity2" class="form-control"><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="storage_capacity">VGA:</label>
                    <input type="text" id="vga" name="vga" class="form-control"><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="storage_capacity">OS:</label>
                    <input type="text" id="os" name="os" class="form-control"><br>
                </div>

                <h1>รายชื่อโปรแกรมที่ติดตั้ง</h1>
                <hr>
                <div class="col-sm-12 mb-3">
                    <input type="file" class="form-control mb-3" id="programFileInput" accept=".txt" />
                    <button type="button" class="btn btn-secondary" onclick="loadPrograms()">โหลดรายชื่อโปรแกรม</button>
                </div>
                <div class="col-sm-12 mb-3">
                    <div id="programCheckboxList" class="list-group"></div>
                </div>

                <div class="col-sm-12 mb-3">
                    <div class="d-grid gap-2">
                        <input type="submit" value="เพิ่มข้อมูล" class="btn p-3 btn-primary">
                        <a href="index" class="btn p-3 btn-secondary">กลับ</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        function loadPrograms() {
            const programFileInput = document.getElementById("programFileInput");

            if (programFileInput.files.length > 0) {
                const file = programFileInput.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const content = e.target.result;
                    const lines = content.split('\n').map(line => line.trim());

                    // Find the dashed line separator
                    const startIndex = lines.findIndex(line => line.includes('---'));
                    const programLines = lines.slice(startIndex + 1).filter(line => line.length > 0);

                    const checkboxContainer = document.getElementById("programCheckboxList");
                    checkboxContainer.innerHTML = ''; // Clear old items

                    // 🔁 Add toggle all checkbox
                    const toggleLabel = document.createElement("label");
                    toggleLabel.className = "list-group-item";

                    const toggleCheckbox = document.createElement("input");
                    toggleCheckbox.type = "checkbox";
                    toggleCheckbox.className = "form-check-input me-3";
                    toggleCheckbox.id = "toggleAllCheckbox";
                    toggleCheckbox.checked = true;

                    toggleLabel.appendChild(toggleCheckbox);
                    toggleLabel.appendChild(document.createTextNode("เลือกทั้งหมด"));
                    checkboxContainer.appendChild(toggleLabel);

                    // ⛏ Add logic for toggling all
                    toggleCheckbox.addEventListener("change", function() {
                        const allCheckboxes = checkboxContainer.querySelectorAll('input[name="programs[]"]');
                        allCheckboxes.forEach(cb => cb.checked = this.checked);
                    });

                    // ✅ Add program checkboxes
                    programLines.forEach((name, index) => {
                        const checkboxId = `program_${index}`;

                        const label = document.createElement("label");
                        label.className = "list-group-item";
                        label.htmlFor = checkboxId;

                        const checkbox = document.createElement("input");
                        checkbox.type = "checkbox";
                        checkbox.className = "form-check-input me-3";
                        checkbox.id = checkboxId;
                        checkbox.name = "programs[]";
                        checkbox.value = name;
                        checkbox.checked = true;

                        label.appendChild(checkbox);
                        label.appendChild(document.createTextNode(name));

                        checkboxContainer.appendChild(label);
                    });
                };

                reader.readAsText(file);
            }
        }



        function loadAndExtract() {
            const fileInput = document.getElementById("fileInput");
            const userCpuInput = document.getElementById("userCpu");
            const userSocketInput = document.getElementById("userSocket");
            const userMemoryTypeInput = document.getElementById("userMemoryType");
            const userMemoryCapacityInput = document.getElementById("userMemoryCapacity");
            const userMotherboardBrandInput = document.getElementById("userMotherboardBrand");
            const userMotherboardInput = document.getElementById("userMotherboard");
            const userStorageInput = document.getElementById("userStorage");
            const userStorageCapacityInput = document.getElementById("userStorageCapacity");
            const userVGAInput = document.getElementById("userVGA");
            const userOSInput = document.getElementById("userOS");


            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const content = e.target.result;
                    const userCpuValue = userCpuInput.value.trim();
                    const userSocketValue = userSocket.value.trim();
                    const userMemoryTypeValue = userMemoryType.value.trim();
                    const userMemoryCapacityValue = userMemoryCapacity.value.trim();
                    const userMotherboardBrandValue = userMotherboardBrand.value.trim();
                    const userMotherboardValue = userMotherboard.value.trim();
                    const userStorageValue = userStorage.value.trim();
                    const userStorageCapacityValue = userStorageCapacity.value.trim();
                    const userVGAValue = userVGA.value.trim();
                    const userOSValue = userOS.value.trim();

                    const cpu = userCpuValue !== '' ? extractValue(content, userCpuValue) : extractValue(content, "Name");
                    const socket = userSocketValue !== '' ? extractValue(content, userSocketValue) : extractValue(content, "Package"); // Fix: Use userSocketValue
                    const memoryType = userMemoryTypeValue !== '' ? extractValue(content, userMemoryTypeValue) : extractValue(content, "Memory Type");
                    const memoryCapacity = userMemoryCapacityValue !== '' ? extractValue(content, userMemoryCapacityValue) : extractValue(content, "Memory Size");
                    const motherboardBrand = userMotherboardBrandValue !== '' ? extractValue(content, userMotherboardBrandValue) : extractValue(content, "Board Manufacturer");
                    const motherboard = userMotherboardValue !== '' ? extractValue(content, userMotherboardValue) : extractValue(content, "Mainboard Model");
                    const storage = userStorageValue !== '' ? extractValue(content, userStorageValue) : extractValue(content, "Storage");
                    const storageCapacity = userStorageCapacityValue !== '' ? extractValue(content, userStorageCapacityValue) : extractValue(content, "Capacity");
                    const vga = userVGAValue !== '' ? extractValue(content, userVGAValue) : extractValue(content, "NAME");
                    const os = userOSValue !== '' ? extractValue(content, userOSValue) : extractValue(content, "Windows Version");

                    // กำหนดค่าให้กับ input fields
                    document.getElementById("cpu").value = cpu;
                    document.getElementById("socket").value = socket;
                    document.getElementById("memoryType").value = memoryType;
                    document.getElementById("memoryCapacity").value = memoryCapacity;
                    document.getElementById("motherboardBrand").value = motherboardBrand;
                    document.getElementById("motherboard").value = motherboard;

                    document.getElementById("storage").value = extractStorageInfo(content, "Name");


                    document.getElementById("storageCapacity").value = storageCapacity;

                    const displayAdapter0Info = extractDisplayAdapterInfo(content, 0);
                    const displayAdapter1Info = extractDisplayAdapterInfo(content, 1);

                    const vgaElement = document.getElementById("vga");

                    if (displayAdapter1Info) {
                        vgaElement.value = displayAdapter1Info.name; // แก้ไขนี้
                    } else {
                        vgaElement.value = displayAdapter0Info.name; // แก้ไขนี้
                    }
                    document.getElementById("os").value = os;

                    console.log(extractStorageInfo(content));

                    // console.log("Display Adapter 0 Info:", displayAdapter0Info);
                    // console.log("Display Adapter 1 Info:", displayAdapter1Info);
                };

                reader.readAsText(file);
            }
        }

        function extractValue(content, key) {
            const pattern = new RegExp(`${key}\\s+(.+)`);
            const match = content.match(pattern);
            return match ? match[1] : "N/A";
        }

        function extractDisplayAdapterInfo(content, adapterIndex) {
            const regex = new RegExp(
                `Display adapter ${adapterIndex}\\s+ID\\s+(.+)\\s+Name\\s+(.+)`
            );

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
    <script src="bootstrap/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="bootstrap/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script> -->
</body>

</html>