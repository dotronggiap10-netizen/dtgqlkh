<?php
require 'config.php';
require 'functions.php';

// Lấy danh sách khoa (PDO)
$faculties = $pdo->query("SELECT * FROM faculties ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$facultyFilter = isset($_GET['faculty']) ? intval($_GET['faculty']) : 0;
$deptFilter = isset($_GET['department']) ? intval($_GET['department']) : 0;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "
SELECT 
    s.id AS submission_id,
    s.name,
    f.name AS faculty_name,
    d.name AS department_name,
    t.title AS topic_title,
    t.total_hours AS topic_hours,
    t.completed_hours AS topic_done,
    t.files AS topic_files,
    a.title AS article_title,
    a.total_hours AS article_hours,
    a.completed_hours AS article_done,
    a.files AS article_files
FROM submissions s
LEFT JOIN faculties f ON s.faculty_id = f.id
LEFT JOIN departments d ON s.department_id = d.id
LEFT JOIN topics t ON s.id = t.submission_id
LEFT JOIN articles a ON s.id = a.submission_id
WHERE 1=1
";

$params = [];

if ($facultyFilter > 0) {
    $sql .= " AND s.faculty_id = ?";
    $params[] = $facultyFilter;
}
if ($deptFilter > 0) {
    $sql .= " AND s.department_id = ?";
    $params[] = $deptFilter;
}
if ($search !== '') {
    $sql .= " AND s.name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rows = [];

foreach ($result as $r) {
    // Đề tài
    if (!empty($r['topic_title'])) {
        $files = $r['topic_files'] ? json_decode($r['topic_files'], true) : [];
        $rows[] = [
            'name' => $r['name'],
            'faculty' => $r['faculty_name'],
            'department' => $r['department_name'],
            'activity_name' => $r['topic_title'],
            'type' => 'Đề tài',
            'total_hours' => $r['topic_hours'],
            'completed_hours' => $r['topic_done'],
            'files' => $files
        ];
    }

    // Bài báo
    if (!empty($r['article_title'])) {
        $files = $r['article_files'] ? json_decode($r['article_files'], true) : [];
        $rows[] = [
            'name' => $r['name'],
            'faculty' => $r['faculty_name'],
            'department' => $r['department_name'],
            'activity_name' => $r['article_title'],
            'type' => 'Bài báo',
            'total_hours' => $r['article_hours'],
            'completed_hours' => $r['article_done'],
            'files' => $files
        ];
    }
}
?>
