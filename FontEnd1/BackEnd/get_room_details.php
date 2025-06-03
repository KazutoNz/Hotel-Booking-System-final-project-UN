<?php
require 'connect.php';

header('Content-Type: application/json');

if (isset($_GET['id_room'])) {
    $id_room = $_GET['id_room'];

    try {
        $stmt = $pdo->prepare("SELECT r.id_room, r.room_number, r.price, r.availability, rt.id_roomt, rt.name AS room_type 
                               FROM room r 
                               LEFT JOIN roomt rt ON r.id_roomt = rt.id_roomt 
                               WHERE r.id_room = ?");
        $stmt->execute([$id_room]);

        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            echo json_encode($room);
        } else {
            echo json_encode(["error" => "ไม่พบข้อมูลห้อง"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ไม่มี ID ห้อง"]);
}
?>
