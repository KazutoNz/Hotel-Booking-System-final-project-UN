<?php
require 'connect.php';

$stmt = $pdo->query("SELECT id_member, name, email, tel FROM user");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>
