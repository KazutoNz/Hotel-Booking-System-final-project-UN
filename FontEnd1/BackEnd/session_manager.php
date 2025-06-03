<?php
// ✅ ตรวจสอบว่ามี session หรือยัง ถ้าไม่มีให้เริ่ม session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
function checkLogin() {
    if (!isset($_SESSION['id_member'])) {
        header('Location: login.html');
        exit;
    }
}

// ตรวจสอบบทบาทผู้ใช้
function checkRole($requiredRole) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        echo json_encode(["error" => "คุณไม่มีสิทธิ์เข้าถึงหน้านี้"]);
        exit;
    }
}

// ดึงชื่อผู้ใช้
function getUsername() {
    return isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
}

// ดึง ID ผู้ใช้
function getUserId() {
    return isset($_SESSION['id_member']) ? $_SESSION['id_member'] : null;
}

// ดึงเบอร์โทรศัพท์ของผู้ใช้
function getUserTel() {
    return isset($_SESSION['tel']) ? htmlspecialchars($_SESSION['tel']) : null;
}

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// ✅ ดึงข้อมูลผู้ใช้ใหม่หลังจากอัปเดต
function refreshUserData() {
    global $pdo;

    if (!isset($_SESSION['id_member'])) {
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT id_member, name, email, tel, role FROM user WHERE id_member = :id_member");
        $stmt->execute([':id_member' => $_SESSION['id_member']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['id_member'] = $user['id_member'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['tel'] = $user['tel'];
            $_SESSION['role'] = $user['role'];
        } else {
            error_log("❌ ไม่พบข้อมูลผู้ใช้ในฐานข้อมูล");
        }
    } catch (PDOException $e) {
        error_log("Error refreshing user data: " . $e->getMessage());
    }
}
?>
