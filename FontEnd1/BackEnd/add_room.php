<?php
require 'connect.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_hotel = $_POST['id_hotel'] ?? null;
    $room_number = $_POST['room_number'] ?? null;
    $room_type = $_POST['room_type'] ?? null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : null; // ✅ แก้ให้รองรับทศนิยม

    if (!$id_hotel || !$room_number || !$room_type || !$price) {
        echo json_encode(["error" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    try {
        // ดึง id_roomt ที่ตรงกับประเภทห้องที่เลือก
        $stmt = $pdo->prepare("SELECT id_roomt FROM roomt WHERE name = ?");
        $stmt->execute([$room_type]);
        $roomTypeData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$roomTypeData) {
            echo json_encode(["error" => "ไม่พบประเภทห้อง"]);
            exit;
        }

        $id_roomt = $roomTypeData['id_roomt'];

        // 
        $stmt = $pdo->prepare("INSERT INTO room (id_hotel, room_number, id_roomt, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_hotel, $room_number, $id_roomt, $price]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Method ไม่ถูกต้อง"]);
}
?>
