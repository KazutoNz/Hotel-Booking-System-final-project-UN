<?php
require_once "connect.php";
require_once "session_manager.php"; // ✅ เพิ่ม session_manager เพื่อเช็คการเข้าสู่ระบบ

// ✅ ตรวจสอบว่ามี ID โรงแรมส่งมา
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "ไม่พบข้อมูลโรงแรม"]);
    exit;
}

$id_hotel = $_GET['id'];

// ✅ ดึงข้อมูลโรงแรม
$sql = "SELECT h.*, p.name AS province_name 
        FROM hotel h
        LEFT JOIN province p ON h.province_id = p.id_province
        WHERE h.id_hotel = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_hotel]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    echo json_encode(["error" => "ไม่พบข้อมูลโรงแรม"]);
    exit;
}

// ✅ กำหนดภาพเริ่มต้นถ้าไม่มีภาพ
$hotel['image'] = !empty($hotel['image']) ? $hotel['image'] : "default-hotel.jpg";

// ✅ ดึงสิ่งอำนวยความสะดวก
$sql_facilities = "SELECT f.name FROM facility f
                   JOIN hotel_facility hf ON f.id_facility = hf.id_facility
                   WHERE hf.id_hotel = :id";
$stmt_facilities = $pdo->prepare($sql_facilities);
$stmt_facilities->execute(['id' => $id_hotel]);
$facilities = $stmt_facilities->fetchAll(PDO::FETCH_COLUMN);

// ✅ ดึงข้อมูลห้องพัก
$sql_rooms = "SELECT rt.name AS room_type, MIN(r.price) AS min_price, MAX(r.price) AS max_price
              FROM room r
              JOIN roomt rt ON r.id_roomt = rt.id_roomt
              WHERE r.id_hotel = :id
              GROUP BY rt.name";
$stmt_rooms = $pdo->prepare($sql_rooms);
$stmt_rooms->execute(['id' => $id_hotel]);
$rooms = $stmt_rooms->fetchAll(PDO::FETCH_ASSOC);

// ✅ คืนค่า JSON พร้อมข้อมูลการเข้าสู่ระบบ
$response = [
    "hotel" => $hotel,
    "facilities" => $facilities,
    "rooms" => $rooms,
    "loggedIn" => isLoggedIn() // ✅ ตรวจสอบสถานะการเข้าสู่ระบบ
];

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>