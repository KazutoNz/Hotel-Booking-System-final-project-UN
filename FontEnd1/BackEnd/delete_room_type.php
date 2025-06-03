<?php
require 'connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_roomt = $_POST['id_roomt'] ?? null;

    if (!$id_roomt) {
        echo json_encode(["error" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    // ✅ เช็คว่ามีห้องใช้ประเภทนี้อยู่หรือไม่
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM room WHERE id_roomt = ?");
    $stmt->execute([$id_roomt]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($count > 0) {
        echo json_encode(["error" => "ไม่สามารถลบได้ เพราะมีห้องใช้ประเภทนี้อยู่"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM roomt WHERE id_roomt = ?");
        $stmt->execute([$id_roomt]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Method ไม่ถูกต้อง"]);
}
?>
