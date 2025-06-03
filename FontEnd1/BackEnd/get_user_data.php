<?php
require_once "connect.php";
session_start();

if (!isset($_SESSION['id_member'])) {
    echo json_encode(["error" => "ไม่ได้เข้าสู่ระบบ"]);
    exit;
}

$id_member = $_SESSION['id_member'];
$stmt = $pdo->prepare("SELECT id_member, name, email, tel FROM user WHERE id_member = ?");
$stmt->execute([$id_member]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode($user); // ✅ ส่งข้อมูลเป็นอ็อบเจกต์
} else {
    echo json_encode(["error" => "ไม่พบข้อมูลผู้ใช้"]);
}
?>
