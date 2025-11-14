<?php
require 'config.php';

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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xuất PDF</title>
<style>
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #000; padding:6px; font-size:14px; }
h2 { text-align:center; }
.btn { padding:10px; background:#3498db; color:#fff; border-radius:4px; text-decoration:none; }
</style>
</head>
<body>

<h2>BÁO CÁO NGHIÊN CỨU</h2>
<p>Nhấn <b>Ctrl + P</b> → chọn <b>Save as PDF</b>.</p>

<table>
<thead>
<tr>
    <th>Họ tên</th>
    <th>Khoa</th>
    <th>Bộ môn</th>
    <th>Đề tài</th>
    <th>Giờ</th>
    <th>HT</th>
    <th>Bài báo</th>
    <th>Giờ</th>
    <th>HT</th>
</tr>
</thead>
<tbody>
<?php foreach ($data as $r): ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['faculty'] ?></td>
    <td><?= $r['department'] ?></td>
    <td><?= $r['topic_title'] ?></td>
    <td><?= $r['th'] ?></td>
    <td><?= $r['ch'] ?></td>
    <td><?= $r['article_title'] ?></td>
    <td><?= $r['ath'] ?></td>
    <td><?= $r['ach'] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>
