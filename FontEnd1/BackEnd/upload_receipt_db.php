<?php
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ticket = $_POST['id_ticket'];

    // ตรวจสอบและอัปโหลดไฟล์
    if(isset($_FILES['payment_image'])){
        $target_dir = "../uploads/payments/";
        $file_name = basename($_FILES["payment_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["payment_image"]["tmp_name"], $target_file)) {
            // อัปเดตฐานข้อมูล
            $sql = "UPDATE ticket SET payment_image = :payment_image, status = 'Paid' WHERE id_ticket = :id_ticket";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':payment_image' => $file_name,
                ':id_ticket' => $id_ticket
            ]);

            header("Location: ../my_booking.php");
            exit;

        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
        }
    }
}
?>
