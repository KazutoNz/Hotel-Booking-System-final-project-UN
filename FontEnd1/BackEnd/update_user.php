<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['id_member'])) {
    echo json_encode(["error" => "ไม่ได้เข้าสู่ระบบ"]);
    exit;
}

// รับค่าจากฟอร์ม
$id_member = $_SESSION['id_member'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$tel = trim($_POST['tel']);
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ✅ ตรวจสอบค่าที่รับมา
if (empty($name) || empty($email) || empty($tel) || empty($old_password)) {
    echo json_encode(["error" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
    exit;
}

// ✅ ตรวจสอบรูปแบบอีเมล
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "รูปแบบอีเมลไม่ถูกต้อง"]);
    exit;
}

// ✅ ตรวจสอบว่าเบอร์โทรเป็นตัวเลข 10 หลัก
if (!preg_match('/^\d{10}$/', $tel)) {
    echo json_encode(["error" => "เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 หลัก"]);
    exit;
}

// ✅ ตรวจสอบรหัสผ่านเดิม
$stmt = $pdo->prepare("SELECT password FROM user WHERE id_member = ?");
$stmt->execute([$id_member]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($old_password, $user['password'])) {
    echo json_encode(["error" => "รหัสผ่านเดิมไม่ถูกต้อง"]);
    exit;
}

// ✅ อัปเดตข้อมูลทั่วไป
try {
    $sql = "UPDATE user SET name = ?, email = ?, tel = ? WHERE id_member = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$name, $email, $tel, $id_member]);

    // ✅ อัปเดตรหัสผ่านใหม่ถ้ามีการป้อน
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            echo json_encode(["error" => "รหัสผ่านใหม่ไม่ตรงกัน"]);
            exit;
        }
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE user SET password = ? WHERE id_member = ?");
        $stmt->execute([$hashed_password, $id_member]);
    }

    // ✅ อัปเดตข้อมูลใน SESSION
    $_SESSION['username'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['tel'] = $tel;

    echo json_encode(["success" => true, "message" => "อัปเดตข้อมูลสำเร็จ"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
