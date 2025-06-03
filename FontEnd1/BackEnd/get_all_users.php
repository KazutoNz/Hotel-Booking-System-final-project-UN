<?php
require_once "connect.php";
session_start();

// ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่
if (!isset($_SESSION['id_member']) || $_SESSION['role'] !== 'a') {
    echo json_encode(["error" => "ไม่มีสิทธิ์เข้าถึงข้อมูล"]);
    exit;
}

try {
    // ✅ ดึงข้อมูลผู้ใช้ทั้งหมด
    $stmt = $pdo->query("SELECT id_member, name, email, tel, role FROM user ORDER BY id_member ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        echo json_encode($users);
    } else {
        echo json_encode(["error" => "ไม่พบข้อมูลในฐานข้อมูล"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
