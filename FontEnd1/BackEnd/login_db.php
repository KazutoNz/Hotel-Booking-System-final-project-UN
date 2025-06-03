<?php
require 'connect.php';
session_start();

$email = trim($_POST['email']);
$password = $_POST['password'];

// ตรวจสอบข้อมูลที่กรอก
if (empty($email) || empty($password)) {
    echo "<script>
        alert('กรุณากรอกข้อมูลให้ครบ');
        history.back();
    </script>";
    exit;
}

try {
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $sql = "SELECT * FROM user WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // ตรวจสอบรหัสผ่านและบทบาทผู้ใช้
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // ป้องกัน Session Fixation
        $_SESSION['id_member'] = $user['id_member']; // เซ็ต id_member จากฐานข้อมูล
        $_SESSION['username'] = $user['name']; // เซ็ต username
        $_SESSION['role'] = $user['role']; // เซ็ต role


        // เปลี่ยนเส้นทางตามบทบาท
        if ($user['role'] === 'a') {
            echo "<script>
                alert('เข้าสู่ระบบสำเร็จสำหรับ Admin');
                window.location.href = '../Admin/Home_page_admin.php'; // เปลี่ยนเส้นทางใหม่
            </script>";
        } elseif ($user['role'] === 'u') {
            echo "<script>
                alert('เข้าสู่ระบบสำเร็จสำหรับ User');
                window.location.href = '../Home_page_user.php'; // User ไม่มีการย้ายที่
            </script>";
        } else {
            echo "<script>
                alert('บทบาทของคุณไม่ถูกต้อง');
                history.back();
            </script>";
        }
    } else {
        echo "<script>
            alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง');
            history.back();
        </script>";
    }
} catch (PDOException $e) {
    echo "<script>
        alert('เกิดข้อผิดพลาด: " . addslashes($e->getMessage()) . "');
        history.back();
    </script>";
}
?>
