<?php
// config.php - Kết nối PostgreSQL trên Render

$host = "dpg-d4a9bu1r0fns73fgi0mg-a.singapore-postgres.render.com";
$port = "5432";
$dbname = "qlkh_xycs";
$user = "qlkh";
$password = "uC0DeaVBIuoanRkmyjOtryFEnf4ciJXc";

// Chuẩn DSN PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die("Kết nối DB lỗi: " . $e->getMessage());
}
?>
