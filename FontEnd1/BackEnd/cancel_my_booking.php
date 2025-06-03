<?php
require 'connect.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['id_member'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "❌ กรุณาเข้าสู่ระบบ"]);
    exit;
}

// รับค่า ticket_id และตรวจสอบว่าเป็นค่าที่ถูกต้องหรือไม่
$ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_SANITIZE_NUMBER_INT);

if (!$ticket_id) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "❌ ข้อมูลไม่ถูกต้อง"]);
    exit;
}

try {
    // ตรวจสอบว่ามีการจองของผู้ใช้นี้จริงหรือไม่
    $stmt = $pdo->prepare("SELECT id_ticket FROM ticket WHERE id_ticket = :ticket_id AND id_member = :id_member");
    $stmt->bindParam(":ticket_id", $ticket_id);
    $stmt->bindParam(":id_member", $_SESSION['id_member']);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "❌ ไม่พบการจองนี้"]);
        exit;
    }

    // อัปเดตสถานะเป็น "Cancelled"
    $stmt = $pdo->prepare("UPDATE ticket SET status = 'Cancelled' WHERE id_ticket = :ticket_id");
    $stmt->bindParam(":ticket_id", $ticket_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        http_response_code(200); // OK
        echo json_encode(["success" => "✅ การจองถูกยกเลิกเรียบร้อย"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "❌ ไม่สามารถยกเลิกการจองได้"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "❌ Database error: " . $e->getMessage()]);
}
?>
