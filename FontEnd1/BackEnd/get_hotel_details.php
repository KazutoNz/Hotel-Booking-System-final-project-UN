<?php
require 'connect.php';

if (isset($_GET['id_hotel'])) {
    $id_hotel = $_GET['id_hotel'];

    // ✅ ดึงข้อมูลโรงแรม
    $stmt = $pdo->prepare("SELECT h.id_hotel As id_hotel, h.name AS name, h.address As address, p.name AS province_name , h.description , h.image
    FROM hotel h
    LEFT JOIN province p ON h.province_id = p.id_province = ?");
    $stmt->execute([$id_hotel]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($hotel) {
        echo "<h4>{$hotel['name']}</h4>";
        echo "<p><strong>ที่อยู่:</strong> {$hotel['address']}, {$hotel['province_name']}</p>";
        echo "<p><strong>รายละเอียด:</strong> {$hotel['description']}</p>";

        // ✅ ตั้งค่า path รูปภาพ
        $imagePath = "/ALL/FontEnd1/img-hotel/img/" . $hotel['image'];
        $defaultImage = "/ALL/FontEnd1/img-hotel/img/default-hotel.jpg";
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

        // ✅ ตรวจสอบและแสดงภาพโรงแรม
        if (!empty($hotel['image']) && file_exists($fullPath)) {
            echo "<img src='$imagePath' alt='รูปโรงแรม' class='img-fluid mb-3' style='max-width: 300px;' 
                  onerror=\"this.onerror=null; this.src='$defaultImage';\">";
        } else {
            echo "<img src='$defaultImage' alt='ไม่มีภาพโรงแรม' class='img-fluid mb-3' style='max-width: 300px;'>";
        }

        // ✅ ดึงข้อมูลสิ่งอำนวยความสะดวก
        $stmt = $pdo->prepare("
            SELECT f.name FROM facility f
            JOIN hotel_facility hf ON f.id_facility = hf.id_facility
            WHERE hf.id_hotel = ?
        ");
        $stmt->execute([$id_hotel]);
        $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($facilities) {
            echo "<h5>สิ่งอำนวยความสะดวก:</h5><ul>";
            foreach ($facilities as $facility) {
                echo "<li>{$facility['name']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p><strong>สิ่งอำนวยความสะดวก:</strong> ไม่มีข้อมูล</p>";
        }
    } else {
        echo "<p>❌ ไม่พบข้อมูลโรงแรม</p>";
    }
} else {
    echo "<p>❌ ข้อผิดพลาด: ไม่มีข้อมูลโรงแรมที่เลือก</p>";
}
?>
