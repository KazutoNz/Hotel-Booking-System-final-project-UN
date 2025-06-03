<?php
require 'connect.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

// ปิดการแสดง PHP Error (ป้องกันส่ง HTML กลับไปแทน JSON)
ini_set('display_errors', 0);
error_reporting(0);

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Method ไม่ถูกต้อง"]);
    exit();
}

$id_hotel = $_POST['id_hotel'] ?? null;

if (!$id_hotel) {
    echo json_encode(["success" => false, "error" => "❌ ไม่พบ ID โรงแรม"]);
    exit();
}

try {
    $pdo->beginTransaction(); // ✅ ใช้ Transaction ป้องกันข้อมูลเสียหาย

    // ✅ ลบข้อมูลที่เกี่ยวข้องกับโรงแรมใน hotel_facility ก่อน
    $stmt = $pdo->prepare("DELETE FROM hotel_facility WHERE id_hotel = ?");
    $stmt->execute([$id_hotel]);

    // ✅ ลบโรงแรมออกจากตาราง hotel
    $stmt = $pdo->prepare("DELETE FROM hotel WHERE id_hotel = ?");
    $stmt->execute([$id_hotel]);

    // ✅ จัดเรียงไอดีใหม่ (ทำให้ ID ต่อเนื่องกัน)
    $pdo->exec("SET @new_id = 0;");
    $pdo->exec("UPDATE hotel SET id_hotel = (@new_id := @new_id + 1) ORDER BY id_hotel ASC;");

    // ✅ รีเซ็ต AUTO_INCREMENT ให้ตรงกับค่า MAX(id_hotel) +1
    $stmt = $pdo->query("SELECT MAX(id_hotel) AS max_id FROM hotel");
    $maxId = $stmt->fetch(PDO::FETCH_ASSOC)['max_id'] ?? 0;
    $pdo->exec("ALTER TABLE hotel AUTO_INCREMENT = " . ($maxId + 1));

    $pdo->commit(); // ✅ ยืนยัน Transaction

    $response["success"] = true;
    $response["message"] = "✅ ลบโรงแรมนี้แล้ว!";
} catch (PDOException $e) {
    $pdo->rollBack(); // ❌ ยกเลิก Transaction ถ้าเกิดข้อผิดพลาด
    $response["error"] = "❌ เกิดข้อผิดพลาด: " . $e->getMessage();
}

// ✅ บังคับให้ JSON ถูกต้องเสมอ
echo json_encode($response);
exit;
?>
