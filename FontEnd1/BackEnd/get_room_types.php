<?php
require 'connect.php';

header('Content-Type: application/json');

if (isset($_GET['id_hotel'])) {
    $id_hotel = $_GET['id_hotel'];

    try {
        $stmt = $pdo->prepare("SELECT id_roomt, name FROM roomt WHERE id_hotel = ?");
        $stmt->execute([$id_hotel]);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ไม่มี ID โรงแรม"]);
}
?>
