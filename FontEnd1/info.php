<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือยัง
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? htmlspecialchars($_SESSION['username']) : null;
$role = $loggedIn && isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!-- ดึง php มาเก็บข้อมูลการเข้า web (Count Viewer) -->
<?php include 'Backend/track_visitor.php'; ?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดโรงแรม</title>
    <link rel="stylesheet" href="css/info.css">
</head>
<body>

<!-- Top Web side (หัวเว็บ) -->
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
                        if ($role) {
                            echo ($role === 'a') ? 'Admin/Home_page_admin.php' : 'Home_page_user.php';
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
            <?php if ($loggedIn): ?>
                <div class="my-ticket">
                    <a href="my_booking.php">🎫</a>
                </div>
                <div class="dropdown">
                    <button class="user-info">
                        <i class="fas fa-user"></i> ยินดีต้อนรับ <?php echo $username; ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown">
                        <a href="Edit_user.php">แสดงข้อมูลส่วนตัว</a>
                        <?php if ($role === 'a'): ?>
                            <a href="Admin/Dashboard">หน้าหลังบ้าน</a>
                        <?php endif; ?>
                        <a href="BackEnd/logout.php">ออกจากระบบ</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="register.php" class="button r">ลงทะเบียน</a>
                <a href="login.php" class="button l active">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="hero">
    <div class="search-container">
        <div class="requse-box">
            <form id="searchForm" action="show_search.php" method="GET">
                <input type="text" name="search" placeholder="จุดหมายที่พัก" required>
                <input type="date" name="checkin_date" id="checkin-date">
                <input type="date" name="checkout_date" id="checkout-date">
                <button class="button" id="searchButton">ค้นหา</button>
            </form>  
        </div>
    </div>
</section>

<!-- Content -->
<section class="hotel-info">
    <div id="hotel-details">
        <!-- ข้อมูลโรงแรมจะถูกโหลดที่นี่จาก hotel_info.js -->
    </div>
</section>

 <!-- Footer Section -->
 <footer>
        <p>Copyright 2024</p>
</footer>

    <!-- เรียกใช้ไฟล์ JavaScript -->
    <script src="js/hotel_info.js"></script>
    <script src="js/date_move.js"></script>
    <script src="js/date_handler.js"></script>
    <script src="js/dropdown_button.js"></script>

</body>
</html>
