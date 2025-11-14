<?php
require 'config.php';

// Lấy dữ liệu
$sql = "
SELECT s.name, f.name AS faculty, d.name AS department,
       t.title AS topic_title, t.total_hours AS th, t.completed_hours AS ch,
       a.title AS article_title, a.total_hours AS ath, a.completed_hours AS ach
FROM submissions s
LEFT JOIN faculties f ON s.faculty_id = f.id
LEFT JOIN departments d ON s.department_id = d.id
LEFT JOIN topics t ON t.submission_id = s.id
LEFT JOIN articles a ON a.submission_id = s.id
ORDER BY s.name
";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Header để tải file CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="data_export.csv"');

$fp = fopen('php://output', 'w');

// BOM cho Excel đọc Unicode
fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

// Header cột
fputcsv($fp, [
    'Họ tên', 'Khoa', 'Bộ môn', 
    'Tên đề tài', 'Tổng giờ', 'Hoàn thành',
    'Tên bài báo', 'Tổng giờ', 'Hoàn thành'
]);

foreach ($data as $r) {
    fputcsv($fp, [
        $r['name'],
        $r['faculty'],
        $r['department'],
        $r['topic_title'],
        $r['th'],
        $r['ch'],
        $r['article_title'],
        $r['ath'],
        $r['ach']
    ]);
}

fclose($fp);
exit;
