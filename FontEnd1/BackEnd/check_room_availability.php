<?php
require 'connect.php';

header('Content-Type: application/json; charset=UTF-8');

$id_hotel = $_GET['id_hotel'] ?? null;
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';

if (!$id_hotel || !$check_in || !$check_out) {
    echo json_encode(["error" => "ระบุข้อมูลไม่ครบ"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT 
            rt.id_roomt, 
            rt.name AS room_type, 
            COUNT(DISTINCT t.id_ticket) AS booked_count, 
            COUNT(DISTINCT r.id_room) AS total_rooms
        FROM room r
        INNER JOIN roomt rt ON r.id_roomt = rt.id_roomt
        LEFT JOIN ticket t ON r.id_room = t.id_room 
            AND t.status IN ('Pending', 'Paid', 'Used') 
            AND DATE(t.date_time) BETWEEN :check_in AND :check_out
        WHERE r.id_hotel = :id_hotel
        GROUP BY rt.id_roomt, rt.name
    ");

    $stmt->execute([
        ':id_hotel' => $id_hotel, 
        ':check_in' => $check_in, 
        ':check_out' => $check_out
    ]);

    $availability = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($availability, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(["error" => "ฐานข้อมูลผิดพลาด: " . $e->getMessage()]);
}
