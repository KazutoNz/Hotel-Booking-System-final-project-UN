<?php
require 'connect.php';

header('Content-Type: application/json');

if (isset($_POST['id_room'])) {
    $id_room = $_POST['id_room'];

    try {
        $stmt = $pdo->prepare("DELETE FROM room WHERE id_room = ?");
        $stmt->execute([$id_room]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ไม่มี ID ห้องพัก"]);
}
?>
