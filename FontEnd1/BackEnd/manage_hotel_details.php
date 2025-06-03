<?php
require 'connect.php';

header('Content-Type: application/json');

if (isset($_GET['id_hotel'])) {
    $id_hotel = $_GET['id_hotel'];

    try {
        // ดึงข้อมูลโรงแรมพร้อมชื่อจังหวัดและรูปภาพ
        $stmt = $pdo->prepare("SELECT h.*, p.id_province, p.name AS province_name, h.image
                               FROM hotel h
                               LEFT JOIN province p ON h.province_id = p.id_province
                               WHERE h.id_hotel = ?");
        $stmt->execute([$id_hotel]);
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hotel) {
            // ถ้าไม่มีรูปภาพ ให้กำหนดค่าเป็น null
            if (empty($hotel['image'])) {
                $hotel['image'] = null;
            }

            // ดึงข้อมูลสิ่งอำนวยความสะดวกที่โรงแรมนี้มี (เฉพาะ ID)
            $stmt = $pdo->prepare("SELECT hf.id_facility
                                   FROM hotel_facility hf
                                   WHERE hf.id_hotel = ?");
            $stmt->execute([$id_hotel]);
            $selected_facilities = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  // ดึงเฉพาะค่า id_facility

            // ดึงสิ่งอำนวยความสะดวกทั้งหมด
            $stmt = $pdo->query("SELECT id_facility, name FROM facility ORDER BY id_facility ASC");
            $all_facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ใส่ข้อมูลลงใน JSON response
            $hotel['selected_facilities'] = $selected_facilities; // เก็บรายการที่เลือกไว้
            $hotel['all_facilities'] = $all_facilities; // รายการทั้งหมด

            echo json_encode($hotel);
        } else {
            echo json_encode(["error" => "ไม่พบข้อมูลโรงแรม"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ข้อผิดพลาด: ไม่มีข้อมูลโรงแรมที่เลือก"]);
}
?>