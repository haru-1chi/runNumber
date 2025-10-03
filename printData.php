<?php
session_start();
require_once 'config/db.php';

$id = $_GET['id'];

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title>พิมพ์สติ๊กเกอร์</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="bootstrap/bootstrap-5.3.2-dist/css/bootstrap.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            /* padding-top: 100px; */
            margin-top: -25px;
        }

        tr {
            height: 1.5pt;
            line-height: 18pt;
            page-break-inside: avoid;
        }

        p {
            font-size: 12pt;
        }

        .breakhere {
            page-break-after: always;
        }



        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 18pt
        }

        td {
            height: 18.8pt;

        }

        .empty-row td {
            height: 18.8pt;
            /* กำหนดความสูงตามที่คุณต้องการ */
        }

        @page {
            size: 50mm 25mm;
            margin: 0;
            page-break-after: always;
        }

        footer {
            display: block;
        }

        @media print {

            html,
            body {
                width: 50mm;
                height: 25mm;
                padding-left: 1.3mm;
                padding-right: 1.3mm;
                page-break-after: always;
            }

            /* ... the rest of the rules ... */
        }
    </style>

    <!-- กำหนดฟังก์ชันแปลงเลขไทย -->

<body onload="window.print()">
    <div class="breakhere">
        <br />
        <?php
        $sql = "SELECT * FROM computer_assets WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $idP = $data['computer_center_number'];


        ?>


        <div class="row gx-2">
            <div style="margin-top: 20px;" class="col-6">
                <p style="margin-left: 10px">
                    <span style="font-size: 7pt">หมายเลขศูนย์คอม</span> <br> <?= $data['computer_center_number'] ?>
                </p>
            </div>
            <div style="margin-top: 10px;" class="col-6">
                <p class="mb-0" style="text-align: right; font-size: 7pt">สแกนเพื่อแจ้งซ่อม</p>
                <div id="qrcode" style="margin-left: 20px"></div>
            </div>
        </div>



    </div>



    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

    <!-- Your QR code generation script -->
    <script type="text/javascript">
    var id = <?= json_encode($idP) ?>;
    if (id) {
        try {
            new QRCode(document.getElementById("qrcode"), {
                text: "http://172.16.190.6/data_report/index.php?id=" + id,
                width: 60,
                height: 60
            });
        } catch (e) {
            console.error("Error generating QR code: ", e);
        }
    } else {
        console.error("Invalid ID for QR code generation.");
    }
</script>



</body>

</html>