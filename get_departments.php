<?php
require 'config.php';

$faculty = intval($_GET['faculty'] ?? 0);

$stmt = $pdo->prepare("SELECT id, name FROM departments WHERE faculty_id = ?");
$stmt->execute([$faculty]);

header("Content-Type: application/json");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));


