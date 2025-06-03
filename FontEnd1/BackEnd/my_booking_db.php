<?php
require 'connect.php';
require_once "session_manager.php";

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(["error" => "❌ กรุณาเข้าสู่ระบบก่อนทำการจอง"], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_member = getUserId();
$status = $_GET['status'] ?? "all";

try {
    $sql = "SELECT t.id_ticket, h.id_hotel, h.name AS hotel_name, h.image AS hotel_image, rt.name AS room_type, t.date_time, t.status
    FROM ticket t
    JOIN hotel h ON t.id_hotel = h.id_hotel
    JOIN room r ON t.id_room = r.id_room
    JOIN roomt rt ON r.id_roomt = rt.id_roomt
    WHERE t.id_member = :id_member";

    if ($status !== "all") {
        $sql .= " AND t.status = :status";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_member', $id_member);

    if ($status !== "all") {
        $stmt->bindParam(':status', $status);
    }

    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ กำหนด path รูปภาพโรงแรม
    $imageBasePath = "/All/FontEnd1/img-hotel/img/";
    foreach ($bookings as &$booking) {
        $booking['hotel_image'] = !empty($booking['hotel_image']) ? $imageBasePath . $booking['hotel_image'] : $imageBasePath . "default.jpg";
    }

    if (empty($bookings)) {
        echo json_encode(["message" => "❌ ยังไม่มีการจอง"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode($bookings, JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "❌ เกิดข้อผิดพลาดกับฐานข้อมูล: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
