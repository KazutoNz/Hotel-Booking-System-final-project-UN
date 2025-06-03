<?php
require 'connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT h.id_hotel As id_hotel, h.name AS name, h.address As address, p.name AS province_name
    FROM hotel h
    LEFT JOIN province p ON h.province_id = p.id_province");
    $stmt->execute();
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($hotels);
} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
