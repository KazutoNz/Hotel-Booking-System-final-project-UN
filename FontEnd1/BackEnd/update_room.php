<?php
require 'connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_room = $_POST['id_room'] ?? null;
    $room_number = $_POST['room_number'] ?? null;
    $id_roomt = $_POST['room_type'] ?? null;
    $price = $_POST['price'] ?? null;
    $availability = $_POST['availability'] ?? null;

    if (!$id_room || !$room_number || !$id_roomt || !$price || !$availability) {
        echo json_encode(["error" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    // ✅ ตรวจสอบว่า id_roomt มีอยู่ในตาราง roomt หรือไม่
    $stmt = $pdo->prepare("SELECT id_roomt FROM roomt WHERE id_roomt = ?");
    $stmt->execute([$id_roomt]);
    if (!$stmt->fetch()) {
        echo json_encode(["error" => "ประเภทห้องนี้ไม่มีอยู่ในระบบ"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE room SET room_number = ?, id_roomt = ?, price = ?, availability = ? WHERE id_room = ?");
        $stmt->execute([$room_number, $id_roomt, $price, $availability, $id_room]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Method ไม่ถูกต้อง"]);
}

?>
