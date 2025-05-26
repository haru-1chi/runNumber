<?php
require_once 'config/db.php';
require_once 'config/depart.php';
require_once 'navbar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM device_asset WHERE id = :id";
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
    <!-- <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="bootstrap/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <title>เพิ่มข้อมูลครุภัณฑ์</title>
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
    <div class="container mt-5">
        <h1 class="mb-3">เพิ่มข้อมูลครุภัณฑ์</h1>
        <hr>
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
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3" id="asset_number">
                        <label for="asset_number_input" class="form-label">เลขครุภัณฑ์:</label>
                        <input type="text" name="asset_number" value="<?= $row['asset_number'] ?>" class="form-control">
                    </div>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="computer_name">รายการ:</label>
                    <select required class="form-select" name="list_device" id="">
                        <?php
                        $sql = "SELECT * FROM device";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $rowa = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($rowa as $d) {
                            if ($row['list_device'] == $d['device_name']) { ?>
                                <option selected value="<?= $row['list_device'] ?>"><?= $row['list_device'] ?></option>

                            <?php } else { ?>

                                <option value="<?= $d['device_name'] ?>"><?= $d['device_name'] ?></option>
                            <?php  }
                            ?>
                        <?php  }
                        ?>

                    </select>
                </div>

                <?php
                $selectDepart = "SELECT * FROM depart WHERE depart_id = :depart_id";
                $stmt = $db->prepare($selectDepart);
                $stmt->bindParam(":depart_id", $row['depart_id']);
                $stmt->execute();
                $depart = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="departInput">หน่วยงาน</label>
                    <input type="text" required class="form-control" value="<?= $depart['depart_name'] ?? '' ?>" id="departInput" name="ref_depart">
                    <input type="hidden" id="departName" name="depart_name">
                    <input type="hidden" id="departId" value="<?= $row['depart_id'] ?>" name="depart_id">

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
                    <label class="form-label" for="equipment_location">ยี่ห้อ:</label>
                    <input type="text" name="brand" value="<?= $row['brand'] ?>" class="form-control" required><br>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="purchase_date">รุ่น:</label>
                    <input type="text" name="model" value="<?= $row['model'] ?>" class="form-control" required><br>
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label">วันเดือนปีซื้อ :</label>
                    <input type="date" name="purchase_date" class="form-control" value="<?= $row['purchase_date'] ?>">
                </div>

                <div class="col-sm-4 mb-3">
                    <label class="form-label" for="upgrading_person">ราคา:</label>
                    <input type="text" name="price" value="<?= $row['price'] ?>" class="form-control" required><br>
                </div>
                <div class="col-sm-12 mb-3">
                    <div class="d-grid gap-2">
                        <button type="submit" name="updateDevice" class="btn p-3 btn-primary mt-3">อัพเดทข้อมูล</button>
                        <a href="index" class="btn p-3 btn-secondary">กลับ</a>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- Link to Bootstrap 5 JS, if needed -->
    <!-- <script src="bootstrap/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script> -->
    <script src="bootstrap/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>