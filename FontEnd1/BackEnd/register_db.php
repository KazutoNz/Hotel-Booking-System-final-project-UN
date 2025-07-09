<?php
require 'connect.php';
session_start();

$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
$cpassword = $_POST["cpassword"];

// กำหนด Role เป็น User โดยอัตโนมัติ
$role = 'u';

// ✅ ตรวจสอบว่าฟิลด์ว่างหรือไม่
if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
    echo "<script>
        alert('กรุณากรอกข้อมูลให้ครบ');
        history.back();
    </script>";
    exit;
}

// ตรวจสอบอีเมลด้วย FILTER_VALIDATE_EMAIL เบื้องต้น
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        alert('รูปแบบอีเมลไม่ถูกต้อง');
        history.back();
    </script>";
    exit;
}

// ตรวจสอบว่าอีเมลมีโดเมนที่ถูกต้องหรือไม่
$domain = substr(strrchr($email, "@"), 1); // ดึงโดเมนหลัง @
if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
    echo "<script>
        alert('โดเมนอีเมลไม่มีอยู่จริง กรุณากรอกใหม่');
        history.back();
    </script>";
    exit;
}

// ใช้ RegEx ตรวจสอบอีเมลให้ละเอียด
$pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
if (!preg_match($pattern, $email)) {
    echo "<script>
        alert('รูปแบบอีเมลไม่ถูกต้อง');
        history.back();
    </script>";
    exit;
}


// ✅ ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกันหรือไม่
if ($password !== $cpassword) {
    echo "<script>
        alert('รหัสผ่านและรหัสผ่านยืนยันไม่ตรงกัน');
        history.back();
    </script>";
    exit;
}

try {
    // ✅ ตรวจสอบว่าอีเมลซ้ำหรือไม่
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists > 0) {
        echo "<script>
            alert('อีเมลนี้ถูกใช้ไปแล้ว');
            history.back();
        </script>";
        exit;
    }

    // ✅ Hash รหัสผ่านก่อนบันทึก
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // ✅ บันทึกข้อมูลผู้ใช้ใหม่
    $sql = "INSERT INTO user (name, email, password, role) VALUES (:name, :email, :password, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':role' => $role,
    ]);

    echo "<script>
        alert('ลงทะเบียนสำเร็จ');
        window.location.href = '../login.php';
    </script>";
    exit;
} catch (PDOException $e) {
    echo "<script>
        alert('เกิดข้อผิดพลาด: " . addslashes($e->getMessage()) . "');
        history.back();
    </script>";
}
?>
