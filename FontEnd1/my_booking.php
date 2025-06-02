<?php 
require 'BackEnd/session_manager.php'; // ใช้ session_manager.php

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

// เก็บข้อมูลจาก Session
$username = htmlspecialchars($_SESSION['username']); // ป้องกัน XSS
$role = $_SESSION['role']; // ตรวจสอบบทบาท (role)
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองของฉัน</title>
    <link rel="stylesheet" href="css/my_booking.css">
</head>

<body>
    <!-- Header -->
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
            <div class="top-web-right">
                <div class="dropdown">
                    <button class="user-info">
                        <i class="fas fa-user"></i> ยินดีต้อนรับ, <?php echo $username; ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown">
                        <a href="../Edit_user.php">จัดการบัญชี</a>
                        <?php if ($role === 'a'): // แสดงเมนูเฉพาะสำหรับ Admin ?>
                            <a href="Admin/Dashboard">หน้าหลังบ้าน</a>
                        <?php endif; ?>
                        <a href="BackEnd/logout.php">ออกจากระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Section: การจองของฉัน -->
    <div class="container">
        <h2 class="section-title">การจองของฉัน</h2>

        <div class="booking-tabs">
        <div class="booking-tabs">
            <button class="tab active" data-status="Pending,Paid,Used,Cancelled">การจองทั้งหมด</button>
            <button class="tab" data-status="Pending">รอชำระเงิน</button>
            <button class="tab" data-status="Paid">สำเร็จการจอง</button>
            <button class="tab" data-status="Used">ใช้แล้ว</button>
            <button class="tab" data-status="Cancelled">ยกเลิกการจอง</button>
        </div>

        </div>

        <div id="my-booking-list">
            <p>กำลังโหลดข้อมูลการจอง...</p>
        </div>
    </div>

    <!-- ดึง JavaScript -->
     <script src="js/auto_date.js"></script>
    <script src="js/dropdown_button.js"></script>
    <script src="js/my_booking.js"></script>

    <!-- Footer -->
    <footer>
        <p>Copyright &copy; Your Website 2024</p>
    </footer>

</body>
</html>
