<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php';
require 'functions.php';

// Lấy danh sách khoa
$faculties = $pdo->query("SELECT * FROM faculties ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$facultyFilter = isset($_GET['faculty']) ? intval($_GET['faculty']) : 0;
$deptFilter    = isset($_GET['department']) ? intval($_GET['department']) : 0;
$search        = isset($_GET['q']) ? trim($_GET['q']) : '';

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

// Nếu không có dữ liệu
if (!$result) {
    echo "<h2>⚠️ Không có dữ liệu nào phù hợp!</h2>";
    exit;
}

// Chuẩn hoá dữ liệu cho giao diện
$rows = [];

foreach ($result as $r) {

    // Đề tài
    if (!empty($r['topic_title'])) {
        $files = $r['topic_files'] ? json_decode($r['topic_files'], true) : [];

        if (!is_array($files)) $files = [];

        $rows[] = [
            'name'            => $r['name'],
            'faculty'         => $r['faculty_name'],
            'department'      => $r['department_name'],
            'activity_name'   => $r['topic_title'],
            'type'            => 'Đề tài',
            'total_hours'     => $r['topic_hours'],
            'completed_hours' => $r['topic_done'],
            'files'           => $files
        ];
    }

    // Bài báo
    if (!empty($r['article_title'])) {
        $files = $r['article_files'] ? json_decode($r['article_files'], true) : [];

        if (!is_array($files)) $files = [];

        $rows[] = [
            'name'            => $r['name'],
            'faculty'         => $r['faculty_name'],
            'department'      => $r['department_name'],
            'activity_name'   => $r['article_title'],
            'type'            => 'Bài báo',
            'total_hours'     => $r['article_hours'],
            'completed_hours' => $r['article_done'],
            'files'           => $files
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem dữ liệu</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
        }
    </style>
</head>
<body>

<h2>📌 Danh sách hoạt động nghiên cứu</h2>

<table>
    <thead>
        <tr>
            <th>Họ tên</th>
            <th>Khoa</th>
            <th>Bộ môn</th>
            <th>Loại</th>
            <th>Tên hoạt động</th>
            <th>Tổng giờ</th>
            <th>Giờ hoàn thành</th>
            <th>File đính kèm</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['faculty']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['activity_name']) ?></td>
            <td><?= htmlspecialchars($row['total_hours']) ?></td>
            <td><?= htmlspecialchars($row['completed_hours']) ?></td>
            <td>
                <?php if (!empty($row['files'])): ?>
                    <?php foreach ($row['files'] as $f): ?>
                        <a href="<?= htmlspecialchars($f) ?>" target="_blank">Tải</a><br>
                    <?php endforeach; ?>
                <?php else: ?>
                    Không có
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

