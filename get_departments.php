<?php
require 'config.php';

$facultyId = isset($_GET['faculty_id']) ? intval($_GET['faculty_id']) : 0;

if ($facultyId <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM departments WHERE faculty_id = ? ORDER BY name");
$stmt->execute([$facultyId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
