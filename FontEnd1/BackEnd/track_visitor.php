<?php
require 'connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ip = $_SERVER['REMOTE_ADDR'];
$page = $_SERVER['PHP_SELF'];
$id_member = isset($_SESSION['id_member']) ? $_SESSION['id_member'] : null;

try {
    $stmt = $pdo->prepare("INSERT INTO visitor_log (ip_address, page, id_member)
                           VALUES (:ip, :page, :id_member)");
    $stmt->execute([
        ':ip' => $ip,
        ':page' => $page,
        ':id_member' => $id_member
    ]);
} catch (PDOException $e) {
    // error_log($e->getMessage());
}
?>
