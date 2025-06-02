<!-- ดึง php มาเก็บข้อมูลการเข้า web (Count Viewer) -->
<?php include 'Backend/track_visitor.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regiter/ลงทะเบียน</title>
    <link rel="stylesheet" href="css/log-reg.css">
</head>
<body>
    <!-- Top Web side (หัวเว็บ)-->
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

            <!-- ไปหน้าต่างๆ -->
            <div class="top-web-right">
                <a href="register.php" class="button r active">ลงทะเบียน</a>
                <a href="login.php" class="button l">เข้าสู่ระบบ</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <form action="BackEnd/register_db.php" method="post">

           <div action="" class="centrt-email-password">
                <h2 class="name">Name</h2>
                <input type="text" name="name"placeholder="Name">

                <h2 class="email">Email</h2>
                <input type="email" name="email" required placeholder="...@gmail.com">

                <h2 class="password">Password</h2>
                <input type="password" name="password" required placeholder="******">

                <h2 class="password">Confirm Password</h2>
                <input type="password" name="cpassword" required placeholder="******">

                <input type="submit" value="Regiter">
            </div>
        </form>
    </section>
    
    <footer>
        <p>
            Copyright 2024 
        </p>
    </footer>
</html>