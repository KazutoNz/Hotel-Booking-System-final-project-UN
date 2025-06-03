<?php
require 'connect.php';
header('Content-Type: application/json');

$response = [];

try {
    $id = $_POST['id_hotel'] ?? null;
    $name = $_POST['name'] ?? "";
    $address = $_POST['address'] ?? "";
    $province_id = $_POST['province_id'] ?? "";
    $description = $_POST['description'] ?? "";
    $facilities = json_decode($_POST['facilities'] ?? "[]", true);
    $imagePath = null;

    // ✅ Debug ข้อมูลที่ได้รับจาก JS
    error_log("🔍 ข้อมูลที่ได้รับจาก JS: " . print_r($_POST, true));
    error_log("📁 ข้อมูลไฟล์ที่อัปโหลด: " . print_r($_FILES, true));

    if (!$id) {
        $response['error'] = "❌ ไม่มีรหัสโรงแรม";
        echo json_encode($response);
        exit;
    }

    // ✅ ตรวจสอบการอัปโหลดไฟล์รูปภาพ
    if (!empty($_FILES['image']['name'])) {
        $imagePath = time() . "_" . basename($_FILES['image']['name']);

        // ✅ กำหนดพาธให้ตรงกับ Frontend
        $imageFolder = "/ALL/FontEnd1/img-hotel/img/";
        $serverImageFolder = $_SERVER['DOCUMENT_ROOT'] . $imageFolder;
        $targetFilePath = $serverImageFolder . $imagePath;

        if (!is_dir($serverImageFolder)) {
            mkdir($serverImageFolder, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $updateImageSQL = ", image = ?";
        } else {
            echo json_encode(["success" => false, "error" => "❌ ไม่สามารถอัปโหลดรูปภาพได้"]);
            exit();
        }
    } else {
        $updateImageSQL = ""; // ❌ ถ้าไม่มีการอัปโหลดภาพ อย่าเปลี่ยนค่า image ในฐานข้อมูล
    }

    // ✅ อัปเดตข้อมูลโรงแรม
    $query = "UPDATE hotel SET name = ?, address = ?, province_id = ?, description = ? $updateImageSQL WHERE id_hotel = ?";
    $params = [$name, $address, $province_id, $description];

    if ($imagePath) {
        $params[] = $imagePath;
    }

    $params[] = $id;
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    // ✅ ลบข้อมูลสิ่งอำนวยความสะดวกเก่าก่อน
    $stmt = $pdo->prepare("DELETE FROM hotel_facility WHERE id_hotel = ?");
    $stmt->execute([$id]);

    // ✅ เพิ่มรายการสิ่งอำนวยความสะดวกใหม่
    if (!empty($facilities) && is_array($facilities)) {
        $stmt = $pdo->prepare("INSERT INTO hotel_facility (id_hotel, id_facility) VALUES (?, ?)");
        foreach ($facilities as $facility) {
            $stmt->execute([$id, $facility]);
        }
    }

    $response['message'] = "✅ อัปเดตข้อมูลสำเร็จ!";
    $response['image'] = $imagePath ? $imageFolder . $imagePath : null;

} catch (PDOException $e) {
    $response['error'] = "❌ เกิดข้อผิดพลาด: " . $e->getMessage();
}

// ✅ Debug ข้อมูลที่ส่งกลับไป
error_log("📡 ข้อมูลที่ส่งกลับไปยัง JS: " . json_encode($response));

echo json_encode($response);
?>
