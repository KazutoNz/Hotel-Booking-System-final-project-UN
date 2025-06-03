<?php
require 'connect.php';

header('Content-Type: application/json');

if (isset($_GET['id_hotel'])) {
    $id_hotel = $_GET['id_hotel'];

    try {
        $stmt = $pdo->prepare("SELECT r.id_room, r.room_number, r.price, r.availability, rt.name AS room_type
                               FROM room r
                               LEFT JOIN roomt rt ON r.id_roomt = rt.id_roomt
                               WHERE r.id_hotel = ?");
        $stmt->execute([$id_hotel]);

        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($rooms ?: []); // ถ้าไม่มีข้อมูล ให้คืนค่า []
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ข้อผิดพลาด: ไม่มี ID โรงแรมที่ระบุ"]);
}
?>
