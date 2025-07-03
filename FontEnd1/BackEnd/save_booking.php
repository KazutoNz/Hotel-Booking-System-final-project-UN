<?php
require 'connect.php';
require 'session_manager.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(["error" => "กรุณาเข้าสู่ระบบก่อนทำการจอง"]);
    exit;
}

$required_fields = ['hotel_id', 'room_type', 'checkin_date', 'checkout_date', 'phone_number', 'total_price', 'first_name', 'last_name', 'email'];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(["error" => "ข้อมูลไม่ครบ: " . $field]);
        exit;
    }
}

$id_member      = getUserId();
$id_hotel       = $_POST['hotel_id'];
$room_type      = $_POST['room_type'];
$phone_number   = $_POST['phone_number'];
$checkin_date   = $_POST['checkin_date'];
$checkout_date  = $_POST['checkout_date'];
$total_price    = $_POST['total_price'];
$first_name     = $_POST['first_name'];
$last_name      = $_POST['last_name'];
$email          = $_POST['email'];
$special_request = $_POST['special_request'] ?? null;

// ✅ **ตรวจสอบรูปแบบหมายเลขโทรศัพท์ (ให้เป็นตัวเลข 10 หลัก)**
$phone_number = preg_replace('/[^0-9]/', '', $phone_number); // ลบอักขระที่ไม่ใช่ตัวเลขออก
if (!preg_match('/^\d{10}$/', $phone_number)) {
    echo json_encode(["error" => "❌ หมายเลขโทรศัพท์ต้องเป็นตัวเลข 10 หลัก"]);
    exit;
}

// ✅ **ตรวจสอบรูปแบบวันที่เช็คอินและเช็คเอาท์**
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout_date)) {
    echo json_encode(["error" => "❌ รูปแบบวันที่ไม่ถูกต้อง"]);
    exit;
}

// ✅ **ตรวจสอบว่า `checkin_date` < `checkout_date`**
if (strtotime($checkout_date) <= strtotime($checkin_date)) {
    echo json_encode(["error" => "❌ วันที่เช็คเอาท์ต้องมากกว่าวันที่เช็คอิน"]);
    exit;
}

// Debug ค่าที่ส่งมา
//file_put_contents('debug_post.txt', print_r($_POST, true));

try {
    // ✅ ตรวจสอบว่าห้องพักมีอยู่จริง
    $stmt_room = $pdo->prepare("SELECT r.id_room 
        FROM room r 
        INNER JOIN roomt rt ON r.id_roomt = rt.id_roomt
        WHERE rt.name = :room_type AND r.id_hotel = :hotel_id AND r.availability = 'Available' 
        LIMIT 1
    ");

    $stmt_room->execute([
        ':hotel_id' => $id_hotel,
        ':room_type' => $room_type
    ]);
    
    $room = $stmt_room->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        echo json_encode(["error" => "❌ ไม่พบห้องพักที่เลือก หรือห้องพักเต็ม"]);
        exit;
    }

    $id_room = $room['id_room'];

    // ✅ ตรวจสอบว่า id_member, id_hotel, id_room มีอยู่จริงหรือไม่
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE id_member = ?");
    $stmt_check->execute([$id_member]);
    if ($stmt_check->fetchColumn() == 0) {
        echo json_encode(["error" => "❌ ไม่พบ id_member: " . $id_member]);
        exit;
    }

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM hotel WHERE id_hotel = ?");
    $stmt_check->execute([$id_hotel]);
    if ($stmt_check->fetchColumn() == 0) {
        echo json_encode(["error" => "❌ ไม่พบ id_hotel: " . $id_hotel]);
        exit;
    }

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM room WHERE id_room = ?");
    $stmt_check->execute([$id_room]);
    if ($stmt_check->fetchColumn() == 0) {
        echo json_encode(["error" => "❌ ไม่พบ id_room: " . $id_room]);
        exit;
    }

    // ✅ **เพิ่มข้อมูลการจองพร้อม `checkin_date` และ `checkout_date`**
    $sql = "INSERT INTO ticket (id_member, id_hotel, id_room, first_name, last_name, email, phone_number,check_in, check_out, date_time, status, special_request, total_price)
    VALUES (:id_member, :id_hotel, :id_room, :first_name, :last_name, :email, :phone_number,:checkin_date, :checkout_date, NOW(), 'Pending', :special_request, :total_price)";


    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        ':id_member' => $id_member,
        ':id_hotel' => $id_hotel,
        ':id_room' => $id_room,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':phone_number' => $phone_number,
        ':checkin_date' => $checkin_date,
        ':checkout_date' => $checkout_date,
        ':special_request' => $special_request,
        ':total_price' => $total_price
    ]);

    if (!$success) {
        echo json_encode(["error" => "❌ INSERT ล้มเหลว"]);
        exit;
    }

    $id_ticket = $pdo->lastInsertId();

    if (!$id_ticket) {
        echo json_encode(["error" => "❌ ไม่สามารถสร้างรายการจองได้"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "id_ticket" => $id_ticket
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาดกับฐานข้อมูล: " . $e->getMessage()]);
}
?>
