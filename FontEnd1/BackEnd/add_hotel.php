<?php
require 'connect.php';
header('Content-Type: application/json');

// ✅ Debug: ตรวจสอบค่าที่ PHP ได้รับจาก JavaScript
error_log(print_r($_POST, true)); // แสดงค่าที่รับจาก FormData
error_log(print_r($_FILES, true)); // แสดงค่าของไฟล์ที่อัปโหลด

// ✅ ตรวจสอบว่าเป็น Method POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Method ไม่ถูกต้อง"]);
    exit();
}

// ✅ รับค่าจาก FormData ที่ส่งมาจาก JavaScript
$name = trim($_POST["hotelName"] ?? ""); // รับค่าชื่อโรงแรม
$address = trim($_POST["hotelAddress"] ?? ""); // รับค่าที่อยู่
$province_id = trim($_POST["province_id"] ?? ""); // รับค่าจังหวัด
$description = trim($_POST["hotelDescription"] ?? ""); // รับค่ารายละเอียด
$facilities = json_decode($_POST["facilities"] ?? "[]", true); // ✅ แปลง JSON เป็นอาร์เรย์ของสิ่งอำนวยความสะดวก
$image = null; // ค่าเริ่มต้นของรูปภาพ

// ✅ ตรวจสอบว่ากรอกข้อมูลครบหรือไม่
if (empty($name) || empty($address) || empty($province_id)) {
    echo json_encode(["success" => false, "error" => "❌ กรุณากรอกข้อมูลให้ครบถ้วน!"]);
    exit();
}

// ✅ ตรวจสอบและอัปโหลดรูปภาพ
$imageFolder = "/ALL/FontEnd1/img-hotel/img/"; // ตำแหน่งเก็บไฟล์รูปภาพ
$serverImageFolder = $_SERVER['DOCUMENT_ROOT'] . $imageFolder; // ตำแหน่งไฟล์ในเซิร์ฟเวอร์
$defaultImage = "default-hotel.jpg"; // รูปภาพเริ่มต้นหากไม่มีการอัปโหลด

if (!empty($_FILES["hotelImageUpload"]["name"])) { // ✅ ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพหรือไม่
    $fileName = time() . "_" . basename($_FILES["hotelImageUpload"]["name"]); // ตั้งชื่อไฟล์ไม่ให้ซ้ำกัน
    $targetFilePath = $serverImageFolder . "/" . $fileName;

    if (move_uploaded_file($_FILES["hotelImageUpload"]["tmp_name"], $targetFilePath)) {
        $image = $fileName; // ถ้าอัปโหลดสำเร็จให้ใช้ไฟล์ที่อัปโหลด
    } else {
        echo json_encode(["success" => false, "error" => "❌ ไม่สามารถอัปโหลดรูปภาพได้"]);
        exit();
    }
} else {
    $image = $defaultImage; // ถ้าไม่มีไฟล์ที่อัปโหลดให้ใช้ภาพเริ่มต้น
}

try {
    // ✅ เพิ่มโรงแรมลงในฐานข้อมูล
    $stmt = $pdo->prepare("INSERT INTO hotel (name, address, province_id, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $address, $province_id, $description, $image]);
    $hotel_id = $pdo->lastInsertId(); // ดึง ID โรงแรมที่ถูกเพิ่มล่าสุด

    // ✅ Debug ตรวจสอบ ID โรงแรมใหม่
    error_log("✅ โรงแรมถูกเพิ่มด้วย ID: " . $hotel_id);

    // ✅ เพิ่มข้อมูลสิ่งอำนวยความสะดวกที่เลือก
    if (!empty($facilities)) {
        $stmt = $pdo->prepare("INSERT INTO hotel_facility (id_hotel, id_facility) VALUES (?, ?)");
        foreach ($facilities as $facility) {
            $stmt->execute([$hotel_id, $facility]); // เพิ่มข้อมูลสิ่งอำนวยความสะดวกที่เลือก
        }
    }

    echo json_encode(["success" => true, "message" => "✅ เพิ่มโรงแรมสำเร็จ!", "image" => $image]); // ส่งค่ากลับไปที่ JavaScript
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "❌ เกิดข้อผิดพลาด: " . $e->getMessage()]); // ส่งข้อผิดพลาดกลับไปที่ JavaScript
}
?>
