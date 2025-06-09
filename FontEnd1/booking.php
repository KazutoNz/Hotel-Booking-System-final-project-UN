<?php
require_once "BackEnd/connect.php";
require_once "BackEnd/session_manager.php";

if (!isLoggedIn()) {
    header("Location: login.html");
    exit;
}

// รับค่าจาก URL
$room_type = $_GET['room_type'] ?? null;
$checkin_date = $_GET['checkin_date'] ?? date("Y-m-d");
$checkout_date = $_GET['checkout_date'] ?? date("Y-m-d", strtotime("+1 day"));
$hotel_name = $_GET['hotel_name'] ?? null;
$hotel_address = $_GET['hotel_address'] ?? null;
$hotel_image = $_GET['hotel_image'] ?? null;

if (!$room_type || !$hotel_name || !$hotel_address) {
    die("ไม่พบข้อมูลห้องพัก");
}
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
            <li> 
               <a href="
                    <?php 
                        if (isset($_SESSION['role'])) {
                            echo ($_SESSION['role'] === 'a') ? 'Admin/Home_page_admin.php' : 'Home_page_user.php';
                        } else {
                            echo 'Home_page.php';
                        }
                        ?>
                    ">ค้นหาโรงแรม</a>            
            </li>
            <li><a href="#">เที่ยวบิน</a></li>
            <li><a href="#">เกี่ยวกับเรา</a></li>
        </ul>
    </div>
    
        <!-- ไปหน้าต่างๆ -->
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
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>นามสกุล *</label>
                <input type="text" name="last_name" required>
            </div>
            <div class="form-group">
                <label>อีเมล *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>หมายเลขโทรศัพท์ *</label>
                <input type="text" name="phone" required>
            </div>
            <div class="special-request">
                <h3>คำขอพิเศษ (ถ้ามี)</h3>
                <textarea name="special_request" rows="3" placeholder="ป้อนคำขอของคุณที่นี่..."></textarea>
            </div>
        </form>
    </div>

    <div class="right-section">
        <h2>รายละเอียดการจอง</h2>
        <div class="hotel-info">
        <img id="hotel-image" src="/All/FontEnd1/img-hotel/img/<?= $hotel_image ?>" alt="รูปโรงแรม">


            <p><strong>โรงแรม:</strong> <span id="hotel-name"><?= $hotel_name ?></span></p>
            <p><strong>ที่อยู่:</strong> <span id="hotel-address"><?= $hotel_address ?></span></p>
            <p><strong>จังหวัด:</strong> <span id="hotel-province"><?= $province_name ?></span></p>
        </div>

        <div class="date-selection">
            <label>วันที่เช็คอิน:</label>
            <input type="date" id="checkin-date" value="<?= $checkin_date ?>">
            <label>วันที่เช็คเอาท์:</label>
            <input type="date" id="checkout-date" value="<?= $checkout_date ?>">
        </div>

        <h3>รายละเอียดราคา</h3>
        
        <div class="price-summary">
            <p><span>ราคาห้องต่อคืน:</span> <span id="room-price"></span></p>
            <p><span>จำนวนคืน:</span> <span id="days"></span> คืน</p>
            <hr>
            <p class="total-price"><span>ยอดรวม:</span> <span id="total-price"></span></p>
        </div>

        <div class="payment-method">
            <label for="payment_method">ช่องทางการชำระเงิน:</label>
            <select name="payment_method" id="payment_method">
                <option value="promptpay">พร้อมเพย์ (PromptPay)</option>
                <option value="credit_card">บัตรเครดิต/เดบิต</option>
                <option value="bank_transfer">โอนเงินผ่านธนาคาร</option>
            </select>
        </div>
        <button class="confirm-btn" id="confirm-btn">ยืนยันการจอง</button>
    </div>
</div>

<script src="js/booking.js"></script>
<script src="js/dropdown_button.js"></script>
</body>
</html>