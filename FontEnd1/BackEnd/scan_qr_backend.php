<?php
require __DIR__ . '/connect.php';
require __DIR__ . '/session_manager.php';

// ✅ ตรวจสอบว่ามี session หรือยัง
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    die("❌ กรุณาเข้าสู่ระบบก่อนทำรายการ");
}

// ✅ ฟังก์ชันดึงข้อมูลตั๋วจากฐานข้อมูล
function getTicketData($id_ticket) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM ticket WHERE id_ticket = ?");
    $stmt->execute([$id_ticket]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ✅ อัปโหลดหลักฐานการชำระเงิน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_ticket']) || empty($_FILES['payment_image']['name'])) {
        echo json_encode(["error" => "❌ ไม่มีรหัสตั๋วหรือไฟล์รูปภาพ"]);
        exit;
    }

    $id_ticket = $_POST['id_ticket'];
    $imageFolder = "/ALL/FontEnd1/img-payment/";
    $serverImageFolder = $_SERVER['DOCUMENT_ROOT'] . $imageFolder;

    // ✅ ตั้งชื่อไฟล์หลักฐานเป็น `slip_{id_ticket}_{timestamp}.jpg`
    $imagePath = "slip_{$id_ticket}_" . time() . ".jpg";
    $targetFilePath = $serverImageFolder . $imagePath;

    // ✅ ตรวจสอบและสร้างโฟลเดอร์ถ้ายังไม่มี
    if (!is_dir($serverImageFolder)) {
        mkdir($serverImageFolder, 0777, true);
    }

    if (move_uploaded_file($_FILES['payment_image']['tmp_name'], $targetFilePath)) {
        // ✅ อัปเดตฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE ticket SET payment_image = ?, status = 'Pending' WHERE id_ticket = ?");
        $stmt->execute([$imagePath, $id_ticket]);

        // ✅ Redirect ไปหน้า my_booking.php หลังจากอัปโหลดสำเร็จ
        header("Location: /All/FontEnd1/my_booking.php");
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "❌ ไม่สามารถอัปโหลดรูปภาพได้"]);
    }
    exit;
}
