<?php
require 'config.php';

$faculty_id = isset($_GET['faculty_id']) ? intval($_GET['faculty_id']) : 0;
$stmt = $conn->prepare("SELECT * FROM departments WHERE faculty_id = ? ORDER BY name");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}
echo json_encode($departments, JSON_UNESCAPED_UNICODE);
?>
