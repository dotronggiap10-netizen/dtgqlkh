<?php
require 'config.php';

$data = $pdo->query("SELECT id, name FROM faculties ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
