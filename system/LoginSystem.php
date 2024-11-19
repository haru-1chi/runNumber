<?php
session_start();
require_once '../config/depart.php';


if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM admin WHERE username = :username";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {
            if ($check['username'] == $username && password_verify($password, $check['password'])) {
                $_SESSION['admin_log'] = $check['username'];
                header('location: ../index');
            } else {
                $_SESSION['error'] = 'ชื่อผู้ใช้หรือรหัสผ่านผิด';
                header('location: ../login');
            }
        } else {
            $_SESSION['error'] = 'ไม่พบข้อมูลในระบบ';
            header('location: ../login');
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
