<!-- ดึง php มาเก็บข้อมูลการเข้า web (Count Viewer) -->
<?php include 'Backend/track_visitor.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บจองที่พักอย่างไม่เป็นทางการ</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- เชื่อมโยงไฟล์ CSS -->
</head>
<body>
    <!-- ครอบส่วน Header และ Search -->
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

            <!-- ปุ่มไปหน้าต่างๆ -->
            <div class="top-web-right">
                <a href="register.php" class="button r">ลงทะเบียน</a>
                <a href="login.php" class="button l active">เข้าสู่ระบบ</a>
            </div>
        </div>
    </header>

    <!-- ครอบเนื้อหาทั้งหมด -->
    <div class="main-content">
        <!-- Hero Section (ส่วนกลางของเว็บ)-->
        <section class="hero">
            <div class="search-container">
                <div class="requse-box">
                    <form id="searchForm" action="show_search.php" method="GET">
                        <input type="text" name="search" placeholder="จุดหมายที่พัก" required>
                        <input type="date" name="checkin_date" id="checkin-date">
                        <input type="date" name="checkout_date" id="checkout-date">
                    </form>
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

        <!-- Footer -->
        <footer>
            <p>Copyright &copy; Website 2024</p>
        </footer>
    </div>

    <!-- ดึงไฟล์ Js มาใช้ -->
    <script src="js/auto_date.js"></script>
    <script src="js/date_move.js"></script>
    <script src="js/search_button.js"></script>
</body>
</html>
