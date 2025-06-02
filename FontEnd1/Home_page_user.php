<?php
require 'BackEnd/session_manager.php'; // ใช้ session_manager.php

// ตรวจสอบสถานะการล็อกอิน
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // เริ่มเซสชันหากยังไม่ได้เริ่ม
}
checkLogin(); // ตรวจสอบสถานะการล็อกอิน

// ดึงข้อมูลผู้ใช้จากเซสชัน
$username = htmlspecialchars($_SESSION['username'] ?? 'Guest');
?>
<!-- ดึง php มาเก็บข้อมูลการเข้า web (Count Viewer) -->
<?php include 'Backend/track_visitor.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บจองที่พักอย่างไม่เป็นทางการ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Top Web Section -->
    <header>
        <div class="top-web">
            <div class="logo">
                <div class="title-serch">
                    <input type="text" placeholder="ค้นหาที่ที่คุณอยากไป" />
                    <input type="button" value="ค้นหา" class="btn_serch">   
                </div>

                <ul>
                    <li><a href="Home_page_user.php">ค้นหาโรงแรม</a></li>
                    <li><a href="#">เที่ยวบิน</a></li>
                    <li><a href="#">เกี่ยวกับเรา</a></li>
                </ul>
            </div>

            <!-- ไปหน้าต่างๆ -->
            <div class="top-web-right">
                <div class="my-ticket">
                    <a href="my_booking.php">🎫</a>
                </div>
                
                <div class="dropdown">
                    <button class="user-info">
                        <i class="fas fa-user"></i> ยินดีต้อนรับ <?php echo $username; ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown">
                        <a href="Edit_user.php">จัดการบัญชี</a>
                        <a href="BackEnd/logout.php">ออกจากระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section (ส่วนกลางของเว็บ)-->
    <section class="hero">
        <div class="search-container">
            <div class="requse-box">
                <form id="searchForm" action="show_search.php" method="GET">
                    <input type="text" name="search" placeholder="จุดหมายที่พัก" required>
                    <input type="date" name="checkin_date" id="checkin-date">
                    <input type="date" name="checkout_date" id="checkout-date">
                </form>
                <!-- ปุ่มอยู่นอก <form> -->
                <button class="button" id="searchButton">ค้นหา</button>
            </div>
        </div>
    </section>

    <!-- เลือกจังหวัด -->
    <div class="home-wrapper home-body-container-new">
        <h2 class="section-title">จังหวัดที่คุณสนใจ</h2>
        <div class="popular-destinations">
            <a href="show_search.php?search=กรุงเทพ" class="destination-card">
                <img src="img/Bangkok.jpg" alt="Bangkok">
                <div class="destination-overlay">กรุงเทพ</div>
            </a>
            <a href="show_search.php?search=พัทยา" class="destination-card">
                <img src="img/Pattaya.jpg" alt="Pattaya">
                <div class="destination-overlay">พัทยา</div>
            </a>
            <a href="show_search.php?search=เชียงใหม่" class="destination-card">
                <img src="img/Chiang_Mai.jpg" alt="Chiang Mai">
                <div class="destination-overlay">เชียงใหม่</div>
            </a>
            <a href="show_search.php?search=ภูเก็ต" class="destination-card">
                <img src="img/Phuket.jpg" alt="ภูเก็ต">
                <div class="destination-overlay">ภูเก็ต</div>
            </a>
        </div>
    </div>

    <!-- ดึงไฟล์ Js มาใช้-->
    <script src="js/auto_date.js"></script>
    <script src="js/date_move.js"></script>
    <script src="js/dropdown_button.js"></script>
    <script src="js/search_button.js"></script>

    <footer>
        <p>Copyright &copy; Your Website 2024</p>
    </footer>

</body>
</html>