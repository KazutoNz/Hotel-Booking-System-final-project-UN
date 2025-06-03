<?php
require_once "connect.php";
require_once "session_manager.php";

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(["error" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"], JSON_UNESCAPED_UNICODE);
    exit;
}

$room_type = $_GET['room_type'] ?? null;
$hotel_id = $_GET['hotel_id'] ?? null;
$checkin_date = $_GET['checkin_date'] ?? date("Y-m-d");
$checkout_date = $_GET['checkout_date'] ?? date("Y-m-d", strtotime("+1 day"));

if (!$room_type || !$hotel_id) {
    echo json_encode(["error" => "ไม่พบข้อมูลห้องพัก"], JSON_UNESCAPED_UNICODE);
    exit;
}

// ✅ ดึงข้อมูลโรงแรม พร้อมชื่อจังหวัด
$sql = "SELECT r.price, h.name AS hotel_name, h.address, p.name AS province_name, h.image AS hotel_image
        FROM room r 
        JOIN hotel h ON r.id_hotel = h.id_hotel 
        LEFT JOIN province p ON h.province_id = p.id_province
        JOIN roomt rt ON r.id_roomt = rt.id_roomt
        WHERE rt.name = :room_type AND h.id_hotel = :hotel_id
        ORDER BY r.price ASC 
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['room_type' => $room_type, 'hotel_id' => $hotel_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo json_encode(["error" => "ไม่พบข้อมูลห้องพัก"], JSON_UNESCAPED_UNICODE);
    exit;
}

// ✅ แก้ไข Path รูปภาพโรงแรม
$hotel_image = !empty($room['hotel_image']) ? $room['hotel_image'] : "default-hotel.jpg";

$response = [
    "hotel_name" => $room['hotel_name'],
    "hotel_address" => $room['address'],
    "province_name" => $room['province_name'] ?? "ไม่ระบุจังหวัด", // ✅ เพิ่มจังหวัด
    "hotel_image" => "/All/FontEnd1/img-hotel/img/" . ($room['hotel_image'] ?: "default-hotel.jpg"),
    "room_type" => $room_type,
    "checkin_date" => $checkin_date,
    "checkout_date" => $checkout_date,
    "days" => (strtotime($checkout_date) - strtotime($checkin_date)) / 86400,
    "price_per_night" => number_format($room['price'], 2),
    "grand_total" => number_format($room['price'] * ((strtotime($checkout_date) - strtotime($checkin_date)) / 86400), 2)
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
