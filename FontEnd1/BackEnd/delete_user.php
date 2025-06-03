<?php
require 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_member'] ?? null;

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบ ID']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM user WHERE id_member = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'ลบสำเร็จ']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ลบไม่สำเร็จ']);
    }
}
?>
