<?php
require 'connect.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id_ticket'])) {
    echo json_encode(["error" => "❌ ไม่มีรหัสตั๋ว"]);
    exit;
}

$id_ticket = $_GET['id_ticket'];
$stmt = $pdo->prepare("SELECT t.id_ticket, u.name AS user_name, u.tel AS phone_number, rt.name AS room_type, 
                            t.date_time, t.status, t.payment_image, t.special_request
                       FROM ticket t
                       JOIN user u ON t.id_member = u.id_member
                       JOIN room r ON t.id_room = r.id_room
                       JOIN roomt rt ON r.id_roomt = rt.id_roomt
                       WHERE t.id_ticket = ?");
$stmt->execute([$id_ticket]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ticket) {
    // ✅ กำหนด path ของรูปให้ถูกต้อง
    $imagePath = !empty($ticket['payment_image']) ? "/All/FontEnd1/img-payment/" . $ticket['payment_image'] : null;

    echo json_encode([
        "id_ticket" => $ticket['id_ticket'],
        "user_name" => $ticket['user_name'],
        "room_type" => $ticket['room_type'],
        "phone_number" => $ticket['phone_number'], // ใช้เบอร์โทรจาก user table
        "date_time" => $ticket['date_time'],
        "status" => $ticket['status'],
        "payment_image" => $imagePath,
        "special_request" => $ticket['special_request'] ?? "ไม่มีคำขอเพิ่มเติม"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["error" => "❌ ไม่พบข้อมูลการจอง"]);
}
?>
