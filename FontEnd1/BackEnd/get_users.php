<?php
require_once "connect.php"; // ✅ เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

try {
    // ตรวจสอบว่า `$pdo` มีค่าหรือไม่
    if (!$pdo) {
        echo json_encode(["error" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"]);
        exit;
    }

    // **ตรวจสอบว่า Table `user` มีข้อมูลหรือไม่**
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM user");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($count['count'] == 0) {
        echo json_encode(["error" => "ไม่มีข้อมูลในฐานข้อมูล"]);
        exit;
    }

    // **ดึงข้อมูลผู้ใช้**
    $stmt = $pdo->query("SELECT id_member, name, email, tel, role FROM user ORDER BY id_member ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
