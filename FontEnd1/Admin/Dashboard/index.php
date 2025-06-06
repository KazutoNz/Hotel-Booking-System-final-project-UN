<?php 
require '../../BackEnd/session_manager.php'; // ใช้ session_manager.php

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

//แสดงการนัคจำนวนผู้ใช้
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM user");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userCount = $row['total_users'];
} catch (PDOException $e) {
    $userCount = 0; // fallback
}

//จำนวนการจองทั้งหมด
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_bookings FROM ticket");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $bookingCount = $row['total_bookings'];
} catch (PDOException $e) {
    $bookingCount = 0; // fallback
}

// แสดงการจองในแต่ละเดือน
try {
    // Query เพื่อดึงจำนวนการจองต่อเดือน
    $stmt = $pdo->query("SELECT YEAR(check_in) AS year, MONTH(check_in) AS month, COUNT(*) AS bookings
                         FROM ticket
                         WHERE check_in IS NOT NULL
                         GROUP BY YEAR(check_in), MONTH(check_in)
                         ORDER BY YEAR(check_in) DESC, MONTH(check_in) DESC");

    $bookingsData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bookingsData[] = [
            'year' => $row['year'],
            'month' => $row['month'],
            'bookings' => $row['bookings']
        ];
    }

    // เรียงข้อมูลตามเดือนจากมกราคมถึงธันวาคม
    usort($bookingsData, function($a, $b) {
        // เปรียบเทียบเดือนจากตัวเลข
        if ($a['year'] === $b['year']) {
            return $a['month'] - $b['month'];  // เรียงเดือนจากน้อยไปมาก
        }
        return $a['year'] - $b['year']; // ถ้าเป็นปีต่างกัน ให้เรียงจากปีน้อยไปมาก
    });
} catch (PDOException $e) {
    $bookingsData = []; // Fallback
}

// ส่งข้อมูลไปยัง JavaScript
echo "<script>
    var bookingsData = " . json_encode($bookingsData) . ";
</script>";

// แสดงจำนวนการเข้าชมทั้งหมด
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS visit_count FROM visitor_log");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $visitCount = $row['visit_count']; // เก็บจำนวนการเข้าชม
} catch (PDOException $e) {
    $visitCount = 0; // ถ้าเกิดข้อผิดพลาด
}


// รายได้ทั้งหมดจากสถานะที่จ่ายเงินหรือใช้บริการแล้ว
try {
    $stmt = $pdo->query("SELECT SUM(total_price) AS total_revenue FROM ticket WHERE status IN ('Used', 'Paid')");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRevenue = $row['total_revenue'] ?? 0;
} catch (PDOException $e) {
    $totalRevenue = 0; // fallback กรณี error
}

// แสดงชื่อโรงแรมที่มียอดการจอง
try {
    // ตั้งค่าการแสดงผลข้อผิดพลาด
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ดึงข้อมูลโรงแรมและจำนวนการจองที่มีสถานะ 'Used' หรือ 'Paid'
    $stmt = $pdo->query("
        SELECT h.name AS hotel_name, COUNT(t.id_ticket) AS booking_count
        FROM hotel h
        LEFT JOIN ticket t ON h.id_hotel = t.id_hotel
        LEFT JOIN payment p ON t.id_ticket = p.id_ticket
        WHERE (p.payment_status = 'Confirmed' OR p.payment_status IS NULL)
        AND (t.status = 'Paid' OR t.status = 'Used')
        GROUP BY h.name;
    ");
    
    // เก็บผลลัพธ์ในตัวแปร $hotelBookings
    $hotelBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // ถ้ามีข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
    // กำหนดค่า fallback หากเกิดข้อผิดพลาด
    $hotelBookings = []; 
}

// เก็บข้อมูลจาก Session
$username = htmlspecialchars($_SESSION['username']); // ป้องกัน XSS
$role = $_SESSION['role']; // ตรวจสอบบทบาท (role)

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Dashboard Admin</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                เมนูหลัก
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>จัดการผู้ใช้</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">เมนูจัดการผู้ใช้:</h6>
                        <a class="collapse-item" href="Show-user.php">ดูสมาชิก</a>
                        <a class="collapse-item" href="manage_users.php">จัดการสมาชิก</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>โรงแรม</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">จัดการภายในโรงแรม:</h6>
                        <a class="collapse-item" href="Show_hotels.php">ดูโรงแรม</a>
                        <a class="collapse-item" href="manage_hotels.php">จัดการโรงแรม</a>
                        <a class="collapse-item" href="manage_rooms.php">จัดการห้องพัก</a>
                        <a class="collapse-item" href="manage_order.php">จัดการคำสั่งซื้อ</a>
                        <!-- <a class="collapse-item" href="others.html">อื่นๆ</a> -->
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- ปิดการใช้งาน -->
            <!-- Nav Item - Pages Collapse Menu ไปหน้าต่างๆของโปรเจค
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>หน้าเว็บ</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">หน้าเข้าสู่ระบบ:</h6>
                        <a class="collapse-item" href="login.html">เข้าสู่ระบบ</a>
                        <a class="collapse-item" href="register.html">ลงทะเบียน</a>
                        <a class="collapse-item" href="forgot-password.html">ลืมรหัสผ่าน</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li>

            Nav Item - Charts
            <li class="nav-item">
                <a class="nav-link" href="charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            Nav Item - Tables ปุ่มไปหน้าตั้งค่า
            <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>ตั้งค่า</span></a>
            </li> -->

            <!-- Divider
            <hr class="sidebar-divider d-none d-md-block"> -->

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                         <!-- Nav Item - User Information -->
                         <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                src="img/undraw_profile.svg">
                            </a>
                            
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../../Edit_user.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    โปรไฟล์ของฉัน
                                </a>
                                <a class="dropdown-item" href="../Home_page_admin.php">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    หน้าเว็บหลัก
                                </a>
                                <a class="dropdown-item" href="activity_log.php">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    ประวัติการใช้งาน
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../../BackEnd/logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    ออกจากระบบ
                                </a>
                            </div>
                        </li>
                    </ul>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-4 text-gray-800">สวัสดีคุณ <?= $username ?></h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- จำนวนผู้ใช้ (Count User) -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                จำนวนผู้ใช้ทั้งหมด</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $userCount ?> คน</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- จำนวนการจอง (Count Ticket)-->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                จำนวนการจองทั้งหมด</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $bookingCount ?> รายการ</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hotel fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- จำนวนการเข้าชม (Visitor Count) -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">จำนวนการเข้าชมหน้า</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $visitCount ?> ครั้ง</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-eye fa-2x text-gray-300"></i> <!-- ใช้ไอคอนรูปตา -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Revenue Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                รายได้ทั้งหมด
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" title="฿<?= number_format($totalRevenue, 2) ?>">
                                                <?php
                                                // ถ้ารายได้ทั้งหมดมากกว่า 10,000 จะแสดงเป็น 'K' แทน
                                                if ($totalRevenue >= 10000) {
                                                    echo number_format(floor($totalRevenue / 1000)) . 'K';
                                                } else {
                                                    echo '฿' . number_format($totalRevenue, 2);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- การจองแต่ละเดือน (Ticket per month) -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">ตารางการจองในแต่ละเดือน</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <!-- ใช้ class 'w-100' และ 'h-100' เพื่อให้กราฟขยายเต็มพื้นที่ของ container -->
                                        <canvas id="myAreaChart" class="w-100 h-100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Ticket per month -->
                        <script src="js/D_Ticket.js"></script> <!-- Reference to the JS file -->
                        
                        <!-- ส่วนของรายชื่อโรงแรมและจำนวนการจอง -->
                        <div class="col">
                             <div class="card shadow">
                                <div class="card-header bg-success text-white fw-bold">
                                    รายชื่อโรงแรมและจำนวนการจอง
                                </div>
                                <ul class="list-group list-group-flush">
                                    <?php if (!empty($hotelBookings)): ?>
                                        <?php foreach ($hotelBookings as $hotel): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <!-- แสดงชื่อโรงแรม -->
                                                <?= htmlspecialchars($hotel['hotel_name']) ?>
                                                <!-- แสดงจำนวนการจอง -->
                                                <span class="badge bg-warning text-dark rounded-pill w-20 text-center">
                                                    <?= $hotel['booking_count'] ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- หากไม่มีข้อมูลการจอง -->
                                        <li class="list-group-item text-center text-muted">
                                            ไม่พบข้อมูลการจอง
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>


                    </div>

                    <!-- Content Row -->

                    <div class="row">
                        <div class="col-md-6.col-lg-6">
                            
                        </div>
                    </div>


                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>


</body>

</html>