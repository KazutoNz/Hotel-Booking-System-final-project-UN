<?php
require __DIR__ . '/BackEnd/scan_qr_backend.php'; // ✅ โหลด Backend

if (!isset($_GET['id_ticket'])) {
    die("❌ ไม่พบรหัสตั๋ว");
}

$id_ticket = $_GET['id_ticket'];
$ticket = getTicketData($id_ticket); // ✅ ดึงข้อมูลตั๋ว

if (!$ticket) {
    die("❌ ไม่พบข้อมูลการจอง");
}

// ✅ ตั้งชื่อไฟล์ QR Code ให้เปลี่ยนไปตาม id_ticket
$qr_folder = "/ALL/FontEnd1/img-payment/";
$qr_file = $qr_folder . "qr_" . $id_ticket . ".png";

// ✅ ตรวจสอบว่าไฟล์ QR มีอยู่จริงหรือไม่
$serverQrPath = $_SERVER['DOCUMENT_ROOT'] . $qr_file;
if (!file_exists($serverQrPath)) {
    $qr_file = "/All/FontEnd1/img-payment/qr_payment.png"; // ถ้าไม่มี ให้ใช้รูปเริ่มต้น
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>QR Code ชำระเงิน</title>
    <link rel="stylesheet" href="css/seenQr.css">
</head>
<body>
    <div class="container">
        <h2>สแกน QR Code เพื่อชำระเงิน</h2>
        <img src="/All/FontEnd1/img-payment/img/qr_payment.png" alt="QR Code">
        <p>หลังจากชำระเงินเสร็จ กรุณาอัปโหลดหลักฐานการชำระเงินด้านล่าง</p>

        <!-- ✅ ฟอร์มอัปโหลดหลักฐานการชำระเงิน -->
        <form action="BackEnd/scan_qr_backend.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_ticket" value="<?php echo $id_ticket; ?>">
            <input type="file" name="payment_image" accept="image/*" required>
            <button type="submit">อัปโหลดหลักฐาน</button>
        </form>

        <?php if (!empty($ticket['payment_image'])): ?>
            <h3>หลักฐานการชำระเงิน</h3>
            <img src="/ALL/FontEnd1/img-payment/<?php echo $ticket['payment_image']; ?>" alt="หลักฐานการชำระเงิน" width="300">
        <?php endif; ?>
    </div>
</body>
</html>
