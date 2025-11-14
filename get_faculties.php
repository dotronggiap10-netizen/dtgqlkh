<?php
require 'config.php';
$result = $conn->query("SELECT * FROM faculties ORDER BY name");
$faculties = [];
while ($row = $result->fetch_assoc()) {
    $faculties[] = $row;
}
echo json_encode($faculties, JSON_UNESCAPED_UNICODE);
?>
