<?php
require 'connect.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT h.id_hotel, h.name AS hotel_name, COUNT(t.id_ticket) AS ticket_count
            FROM hotel h
            LEFT JOIN ticket t ON h.id_hotel = t.id_hotel
            GROUP BY h.id_hotel, h.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งกลับข้อมูลเป็น JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // หากเกิดข้อผิดพลาด ส่งกลับข้อความผิดพลาดเป็น JSON
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>