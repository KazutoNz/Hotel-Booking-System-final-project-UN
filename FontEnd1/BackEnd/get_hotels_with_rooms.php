<?php
require 'connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT h.id_hotel, h.name AS hotel_name, COUNT(r.id_room) AS room_count
                         FROM hotel h
                         LEFT JOIN room r ON h.id_hotel = r.id_hotel
                         GROUP BY h.id_hotel
                         ORDER BY h.name ASC");

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
