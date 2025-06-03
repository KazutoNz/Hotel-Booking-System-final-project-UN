<?php
require 'connect.php';

header('Content-Type: application/json; charset=utf-8');

// ปิด error และล้าง output ก่อน
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// ตรวจสอบข้อมูลที่จำเป็น
if (!isset($_POST['id_ticket']) || !isset($_POST['status'])) {
    echo json_encode(['error' => '❌ ข้อมูลไม่ครบ']);
    exit;
}

$id_ticket = $_POST['id_ticket'];
$status = $_POST['status'];

// ตรวจสอบว่ารายการจองมีอยู่จริง
$stmt_check = $pdo->prepare("SELECT id_ticket FROM ticket WHERE id_ticket = ?");
$stmt_check->execute([$id_ticket]);

if ($stmt_check->rowCount() === 0) {
    echo json_encode(['error' => '❌ ไม่พบรายการจองนี้']);
    exit;
}

// อัปเดตสถานะ
$stmt = $pdo->prepare("UPDATE ticket SET status = :status WHERE id_ticket = :id_ticket");
$success = $stmt->execute([
    ':status' => $status,
    ':id_ticket' => $id_ticket
]);

echo json_encode(['success' => $success]);
