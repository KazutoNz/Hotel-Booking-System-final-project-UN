<?php
require 'BackEnd/session_manager.php'; // ใช้ session_manager.php
checkLogin(); // ตรวจสอบสถานะการล็อกอิน

// ดึงข้อมูลจากเซสชันแทนการดึงจากฐานข้อมูลโดยตรง
$name = htmlspecialchars($_SESSION['username'] ?? '');
$email = htmlspecialchars($_SESSION['email'] ?? '');
$tel = htmlspecialchars($_SESSION['tel'] ?? '');
$role = htmlspecialchars($_SESSION['role'] ?? 'u');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้ใช้</title>
    <link rel="stylesheet" href="css/edit-user.css">
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
                    <li><a href="<?php echo ($role === 'a') ? 'Admin/Home_page_admin.php' : 'Home_page_user.php'; ?>">ค้นหาโรงแรม</a></li>
                    <li><a href="#">จังหวัดต่างๆ</a></li>
                    <li><a href="#">เกี่ยวกับเรา</a></li>
                </ul>
            </div>
            <!-- ไปหน้าต่างๆ -->
            <div class="top-web-right">
                <div class="dropdown">
                    <button class="user-info">
                        <i class="fas fa-user"></i> ยินดีต้อนรับ, <?php echo htmlspecialchars($name); ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown">
                        <a href="Edit_user.php">จัดการบัญชี</a>
                        <?php if ($role === 'a'): ?>
                            <a href="../FontEnd1/Admin/Dashboard/">หน้าหลังบ้าน</a>
                        <?php endif; ?>
                        <a href="BackEnd/logout.php">ออกจากระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Display Alerts -->
    <div class="alert <?php echo isset($_SESSION['error']) ? 'alert-error' : (isset($_SESSION['success']) ? 'alert-success' : ''); ?>">
        <?php
        if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        }
        ?>
    </div>

    <!-- Edit User Form -->
    <form id="editUserForm">
        <label for="name">ชื่อ</label>
        <input type="text" id="name" name="name" required>

        <label for="email">อีเมล</label>
        <input type="email" id="email" name="email" required>

        <label for="tel">เบอร์โทร</label>
        <input type="tel" id="tel" name="tel" pattern="[0-9]{10}" maxlength="10">

        <label for="old_password">รหัสผ่านเดิม</label>
        <input type="password" id="old_password" name="old_password" required>

        <label for="new_password">รหัสผ่านใหม่ (ถ้าต้องการเปลี่ยน)</label>
        <input type="password" id="new_password" name="new_password">

        <label for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <button type="submit">บันทึกการเปลี่ยนแปลง</button>
    </form>

    <!-- Footer -->
    <footer>
        <p>Copyright &copy; Your Website 2024</p>
    </footer>

    <!-- Include JavaScript -->
    <script src="js/Edit_user.js"></script>
    <script src="js/dropdown_button.js"></script>
</body>
</html>
