<?php
$servername = "localhost";
$username = "postgres";
$password = "root";
$dbname = "project";

// mysqli_connect("host","username","password","databasename");
$conn = new PDO("pgsql:host=$servername;dbname=project", $username, $password);
// ตั้งค่าให้ PDO โยนข้อผิดพลาดเป็นข้อยกเว้น
?>
