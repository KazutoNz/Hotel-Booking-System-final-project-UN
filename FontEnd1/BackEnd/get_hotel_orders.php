<?php
require 'connect.php';

header('Content-Type: application/json; charset=utf-8');

// ✅ ปิด error และล้าง output buffer
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
if (ob_get_length()) ob_end_clean();

// ✅ ตรวจสอบการส่ง id_hotel มาหรือไม่
if (!isset($_GET['id_hotel'])) {
    echo json_encode(["error" => "❌ ไม่มีรหัสโรงแรม"]);
    exit;
}

$id_hotel = $_GET['id_hotel'];

// ✅ ดึงข้อมูลการจองของโรงแรมนั้น
$sql = "SELECT t.id_ticket, u.name AS user_name, u.tel AS phone_number, rt.name AS room_type, t.status
        FROM ticket t
        JOIN user u ON t.id_member = u.id_member
        JOIN room r ON t.id_room = r.id_room
        JOIN roomt rt ON r.id_roomt = rt.id_roomt
        WHERE t.id_hotel = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_hotel]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ ถ้าไม่มีข้อมูลให้ส่ง array ว่าง เพื่อให้ JSON ถูกต้อง
if (!$data) {
    echo json_encode([]); // สำคัญ: ส่ง [] ไม่ใช่ error message
    exit;
}

// ✅ ตรวจสอบ JSON ก่อนส่ง
$json = json_encode($data, JSON_UNESCAPED_UNICODE);
if ($json === false) {
    echo json_encode(["error" => "❌ JSON encode ล้มเหลว: " . json_last_error_msg()]);
    exit;
}

echo $json;
?>
