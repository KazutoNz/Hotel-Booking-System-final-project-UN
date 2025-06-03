<?php 
require_once "connect.php"; // เชื่อมต่อฐานข้อมูล

// รับค่าการค้นหาจาก GET request
$search = $_GET['search'] ?? ''; 
$checkin_date = $_GET['checkin_date'] ?? ''; 
$checkout_date = $_GET['checkout_date'] ?? ''; 
$amenities = $_GET['amenities'] ?? [];

try {
    // ✅ คำสั่ง SQL ค้นหาโรงแรม
    $sql = "SELECT DISTINCT h.* , p.name AS province_name
            FROM hotel h
            LEFT JOIN province p ON h.province_id = p.id_province
            LEFT JOIN hotel_facility hf ON h.id_hotel = hf.id_hotel
            LEFT JOIN facility f ON hf.id_facility = f.id_facility
            WHERE (h.name LIKE :name OR p.name LIKE :province)";
    
    $params = [
        ":name" => "%$search%", 
        ":province" => "%$search%"
    ];

    if (!empty($amenities)) {
        $placeholders = implode(',', array_map(fn($i) => ":facility$i", array_keys($amenities)));
        $sql .= " AND f.name IN ($placeholders)";

        foreach ($amenities as $key => $amenity) {
            $params[":facility$key"] = $amenity;
        }
    }

    // ✅ ประมวลผลคำสั่ง SQL
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo '<div class="search-results-container">';

    // ✅ ฟิลเตอร์ตัวกรองสิ่งอำนวยความสะดวก
    echo '<div class="filter-container">
        <h3>กรองสิ่งอำนวยความสะดวก:</h3>
        <form id="filterForm">
            <input type="hidden" name="search" value="' . htmlspecialchars($search) . '">
            <input type="hidden" name="checkin_date" value="' . htmlspecialchars($checkin_date) . '">
            <input type="hidden" name="checkout_date" value="' . htmlspecialchars($checkout_date) . '">
            <div class="Filter-checkbox">';

    $facilityQuery = $pdo->query("SELECT * FROM facility");
    $facilities = $facilityQuery->fetchAll();

    foreach ($facilities as $facility) {
        $checked = in_array($facility['name'], $amenities) ? "checked" : "";
        echo '<label><input type="checkbox" name="amenities[]" value="' . htmlspecialchars($facility['name']) . '" ' . $checked . '> ' . htmlspecialchars($facility['name']) . '</label>';
    }

    echo '</div>
            <button type="submit" id="filterButton">ค้นหา</button>
        </form>
    </div>';

    // ✅ แสดงผลลัพธ์ของโรงแรม
    echo '<div class="hotel-container">';
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch()) {
            echo '<div class="hotel-card">';

            // ✅ **แก้ไข Path ของรูปภาพ**
            $basePath = "/All/FontEnd1/img-hotel/img/";  // ✅ โฟลเดอร์รูปภาพ
            $imagePath = $basePath . $row["image"];  // ✅ Path รูปของโรงแรม
            $defaultImage = $basePath . "default-hotel.jpg";  // ✅ รูป Default ถ้าไม่มีรูป

            // ✅ ตรวจสอบว่ารูปมีอยู่จริงหรือไม่ ถ้าไม่มีให้ใช้ Default
            $imageFullPath = __DIR__ . "/../img-hotel/img/" . $row["image"];
            $finalImagePath = (!empty($row["image"]) && file_exists($imageFullPath)) ? $imagePath : $defaultImage;

            // ✅ Debug Path (ช่วยตรวจสอบ Path)
            error_log("Final Image Path: " . $finalImagePath);
            error_log("Image Exists: " . (file_exists($imageFullPath) ? "Yes" : "No"));

            // ✅ **แสดงรูปภาพโรงแรม**
            echo '<div class="hotel-image-container">
                    <img src="' . htmlspecialchars($finalImagePath) . '" alt="' . htmlspecialchars($row["name"]) . '" 
                         class="hotel-image"
                         onerror="this.onerror=null; this.src=\'/All/FontEnd1/img-hotel/img/default-hotel.jpg\';">
                  </div>';

            // ✅ **แสดงรายละเอียดของโรงแรม**
            echo '<div class="hotel-details">';
            echo '<h3 class="hotel-name">' . htmlspecialchars($row["name"]) . '</h3>';
            $province = $row["province_name"] ?? "ไม่ระบุ";
            echo '<p><strong>จังหวัด:</strong> ' . htmlspecialchars($province) . '</p>';

            // ✅ **ย่อคำอธิบายโรงแรม**
            $description = htmlspecialchars($row["description"]);
            $shortDescription = mb_substr($description, 0, 100, 'UTF-8') . (mb_strlen($description, 'UTF-8') > 100 ? '...' : '');
            echo '<p class="description">' . $shortDescription . '</p>';

            // ✅ **ปุ่มดูรายละเอียดโรงแรม**
            echo '<a href="info.php?id=' . urlencode($row["id_hotel"]) . '&checkin_date=' . urlencode($checkin_date) . '&checkout_date=' . urlencode($checkout_date) . '" class="view-more-btn">ดูรายละเอียด</a>';
            echo '</div>'; // ปิด hotel-details

            echo '</div>'; // ปิด hotel-card
        }
    } else {
        echo "<p class='no-results'>❌ ไม่พบข้อมูลโรงแรมที่ตรงกับคำค้นหา</p>";
    }
    echo '</div>'; // ปิด hotel-container
} catch (PDOException $e) {
    echo json_encode(['error' => "❌ เกิดข้อผิดพลาด: " . $e->getMessage()]); // ✅ แสดง Error หากมีปัญหา
}
?>
