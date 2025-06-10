<?php
require_once "BackEnd/connect.php";
require_once "BackEnd/session_manager.php";

if (!isset($pdo)) {
    die("❌ การเชื่อมต่อฐานข้อมูลล้มเหลว");
}

if (!isLoggedIn()) {
    header("Location: login.html");
    exit;
}

// ✅ รับค่า `id_ticket` จาก URL
$ticket_id = $_GET['id'] ?? null;

// ✅ ถ้าไม่มี `id_ticket` ให้แสดงข้อความผิดพลาด
if (!$ticket_id) {
    die("❌ ไม่พบข้อมูลการจอง");
}

// ✅ ดึงข้อมูลจากตาราง `ticket`, `hotel`, `province`, `user`
$sql = "SELECT 
            t.id_ticket, t.id_hotel, t.id_room, t.first_name, t.last_name, t.email,
            t.check_in, t.check_out, t.phone_number, t.date_time, t.status, t.special_request,
            h.name AS hotel_name, h.address AS hotel_address, h.province_id, h.image AS hotel_image,
            p.name AS province_name
        FROM ticket t
        JOIN hotel h ON t.id_hotel = h.id_hotel
        JOIN province p ON h.province_id = p.id_province
        WHERE t.id_ticket = :ticket_id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ ตรวจสอบว่าพบข้อมูลหรือไม่
if (!$data) {
    die("❌ ไม่พบข้อมูลการจอง");
}

// ✅ กำหนดค่าตัวแปรเพื่อใช้ใน HTML
$hotel_id = $data['id_hotel'];
$room_id = $data['id_room'];
$checkin_date = $data['check_in'];
$checkout_date = $data['check_out'];
$status = $data['status'];
$special_request = $data['special_request'];
$hotel_name = $data['hotel_name'];
$hotel_address = $data['hotel_address'];
$hotel_image = $data['hotel_image'];
$province_name = $data['province_name'];
$phone_number = $data['phone_number'];
$first_name = $data['first_name'];
$last_name = $data['last_name'];
$email = $data['email'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพัก</title>
    <link rel="stylesheet" href="css/booking.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header>
    <div class="top-web">
        <div class="logo">
            <div class="title-serch">
                <input type="text" placeholder="ค้นหาที่ที่คุณอยากไป" />
                <input type="button" value="ค้นหา" class="btn_serch">
            </div>
            <ul>
                <li><a href="Home_page.php">ค้นหาโรงแรม</a></li>
                <li><a href="#">เที่ยวบิน</a></li>
                <li><a href="#">เกี่ยวกับเรา</a></li>
            </ul>
        </div>

        <div class="top-web-right">
            <div class="my-ticket">
                <a href="my_booking.php">🎫</a>
            </div>

            <?php if (isset($_SESSION['username'])): ?>
                <div class="dropdown">
                    <button class="user-info">
                        <i class="fas fa-user"></i> ยินดีต้อนรับ <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown">
                        <a href="Edit_user.php">แสดงข้อมูลส่วนตัว</a>
                        <?php if ($_SESSION['role'] === 'a'): ?>
                            <a href="Admin/Dashboard">หน้าหลังบ้าน</a>
                        <?php endif; ?>
                        <a href="BackEnd/logout.php">ออกจากระบบ</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="register.html" class="button r">ลงทะเบียน</a>
                <a href="login.html" class="button l active">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="main-title">
    <h1>จองห้องพัก</h1>
</div>

<div class="booking-container">
    <div class="left-section">
        <h2>ข้อมูลผู้เข้าพัก</h2>
        <form id="booking-form">
            <div class="form-group">
                <label>ชื่อ *</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" readonly>
            </div>
            <div class="form-group">
                <label>นามสกุล *</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" readonly>
            </div>
            <div class="form-group">
                <label>อีเมล *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
            </div>
            <div class="form-group">
                <label>หมายเลขโทรศัพท์ *</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($phone_number) ?>" readonly>
            </div>
            <div class="special-request">
                <h3>คำขอพิเศษ (ถ้ามี)</h3>
                <textarea name="special_request" rows="3"readonly ><?= htmlspecialchars($special_request) ?></textarea>
            </div>
        </form>
    </div>

    <div class="right-section">
    <h2>รายละเอียดการจอง</h2>
        <div class="hotel-info">
            <img id="hotel-image" src="/All/FontEnd1/img-hotel/img/<?= htmlspecialchars($hotel_image) ?>" alt="รูปโรงแรม">
            <p><strong>โรงแรม:</strong> <span><?= htmlspecialchars($hotel_name) ?></span></p>
            <p><strong>ที่อยู่:</strong> <span><?= htmlspecialchars($hotel_address) ?></span></p>
            <p><strong>จังหวัด:</strong> <span><?= htmlspecialchars($province_name) ?></span></p>
            <p><strong>หมายเลขการจอง:</strong> <span><?= htmlspecialchars($id_ticket = $data['id_ticket']) ?></span></p>
            <p><strong>สถานะการจอง:</strong> <span><?= htmlspecialchars($status) ?></span></p>
        </div>

        <div class="date-selection">
            <label>วันที่เช็คอิน:</label>
            <input type="date" id="checkin-date" value="<?= htmlspecialchars($checkin_date) ?>" readonly>
            <label>วันที่เช็คเอาท์:</label>
            <input type="date" id="checkout-date" value="<?= htmlspecialchars($checkout_date) ?>" readonly>
        </div>
</div>

</div>

<script src="js/my_Order_booking.js"></script>
<script src="js/dropdown_button.js"></script>
</body>
</html>