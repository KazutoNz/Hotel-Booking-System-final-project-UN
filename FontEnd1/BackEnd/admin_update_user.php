<?php
require_once "connect.php";
session_start();

header('Content-Type: application/json');

// ✅ Debug: แสดงค่าที่ได้รับจาก JavaScript
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log(print_r($_POST, true)); // บันทึกค่าที่ได้รับลง log

    $id_member = $_POST['id_member'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = isset($_POST['tel']) && trim($_POST['tel']) !== '' ? trim($_POST['tel']) : null; // ✅ กำหนดค่า NULL ถ้าเว้นว่าง
    $role = trim($_POST['role'] ?? '');

    // ✅ ตรวจสอบว่าค่าที่จำเป็นต้องไม่เป็นค่าว่าง
    if (!$id_member || empty($name) || empty($email) || empty($role)) {
        echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบ"]);
        exit;
    }

    // ✅ ตรวจสอบรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "รูปแบบอีเมลไม่ถูกต้อง"]);
        exit;
    }

    try {
        // ✅ ตรวจสอบว่าข้อมูลมีการเปลี่ยนแปลงจริงหรือไม่
        $stmt_check = $pdo->prepare("SELECT name, email, tel, role FROM user WHERE id_member = ?");
        $stmt_check->execute([$id_member]);
        $existingData = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$existingData) {
            echo json_encode(["status" => "error", "message" => "ไม่พบข้อมูลผู้ใช้งาน"]);
            exit;
        }

        if ($existingData['name'] == $name && $existingData['email'] == $email && 
            $existingData['tel'] == $tel && $existingData['role'] == $role) {
            echo json_encode(["status" => "success", "message" => "ไม่มีการเปลี่ยนแปลงข้อมูล"]);
            exit;
        }

        // ✅ อัปเดตข้อมูลในฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE user SET name = ?, email = ?, tel = ?, role = ? WHERE id_member = ?");
        $success = $stmt->execute([$name, $email, $tel, $role, $id_member]);

        if ($success) {
            echo json_encode(["status" => "success", "message" => "อัปเดตข้อมูลสำเร็จ"]);
        } else {
            echo json_encode(["status" => "error", "message" => "ไม่สามารถอัปเดตข้อมูลได้"]);
        }
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage()); // บันทึก error ลง log
        echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ไม่รองรับการร้องขอ"]);
}
?>
