<?php
require_once "connect.php"; // ✅ เชื่อมต่อฐานข้อมูล

header("Content-Type: application/json");

try {
    // ✅ ตรวจสอบค่าที่ส่งมา
    if (!isset($_GET['room_type']) || !isset($_GET['hotel_id']) || !isset($_GET['checkin_date']) || !isset($_GET['checkout_date'])) {
        echo json_encode(["error" => "ข้อมูลไม่ครบถ้วน"]);
        exit;
    }

    $room_type = $_GET['room_type'];
    $hotel_id = $_GET['hotel_id'];
    $checkin_date = $_GET['checkin_date'];
    $checkout_date = $_GET['checkout_date'] ?? date('Y-m-d', strtotime('+1 day'));

    // ✅ ดึงข้อมูลโรงแรม + ราคาต่อคืน
    $sql = "SELECT h.hotel_name, h.hotel_address, h.province_name, h.hotel_image, r.price_per_night 
            FROM hotel h
            JOIN room r ON h.hotel_id = r.hotel_id
            WHERE h.hotel_id = :hotel_id AND r.room_type = :room_type";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":hotel_id", $hotel_id, PDO::PARAM_INT);
    $stmt->bindParam(":room_type", $room_type, PDO::PARAM_STR);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(["error" => "ไม่พบข้อมูลโรงแรมหรือห้องพัก"]);
        exit;
    }

    // ✅ คำนวณจำนวนคืนและราคาทั้งหมด
    $date1 = new DateTime($checkin_date);
    $date2 = new DateTime($checkout_date);
    $interval = $date1->diff($date2);
    $days = $interval->days;

    $data['days'] = $days;
    $data['grand_total'] = $days * $data['price_per_night'];

    // ✅ เพิ่ม: ดึง id_ticket และ status ของการจอง (ล่าสุด)
    $ticket_sql = "SELECT t.id_ticket, t.status 
                   FROM ticket t
                   JOIN room r ON t.id_room = r.id_room
                   WHERE t.hotel_id = :hotel_id AND r.room_type = :room_type AND t.check_in = :checkin_date
                   ORDER BY t.date_time DESC
                   LIMIT 1";

    $stmt_ticket = $conn->prepare($ticket_sql);
    $stmt_ticket->bindParam(":hotel_id", $hotel_id, PDO::PARAM_INT);
    $stmt_ticket->bindParam(":room_type", $room_type, PDO::PARAM_STR);
    $stmt_ticket->bindParam(":checkin_date", $checkin_date, PDO::PARAM_STR);
    $stmt_ticket->execute();

    $ticket = $stmt_ticket->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        $data['id_ticket'] = $ticket['id_ticket'];
        $data['status'] = $ticket['status'];
    } else {
        $data['id_ticket'] = null;
        $data['status'] = "ยังไม่มีสถานะ";
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => "❌ เกิดข้อผิดพลาดในเซิร์ฟเวอร์: " . $e->getMessage()]);
}
?>
