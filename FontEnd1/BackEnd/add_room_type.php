<?php
require 'connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_hotel = $_POST['id_hotel'] ?? null;
    $room_type_name = $_POST['room_type_name'] ?? null;

    if (!$id_hotel || !$room_type_name) {
        echo json_encode(["error" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO roomt (id_hotel, name) VALUES (?, ?)");
        $stmt->execute([$id_hotel, $room_type_name]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Method ไม่ถูกต้อง"]);
}
?>
