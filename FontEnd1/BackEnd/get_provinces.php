<?php
require 'connect.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id_province, name FROM province ORDER BY name ASC");
    $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($provinces, JSON_UNESCAPED_UNICODE); // ✅ รองรับภาษาไทย
} catch (PDOException $e) {
    echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
