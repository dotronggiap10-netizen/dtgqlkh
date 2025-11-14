<?php
require 'config.php';
require 'functions.php';

// Lấy danh sách khoa và bộ môn từ DB
$faculties = $conn->query("SELECT * FROM faculties ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$facultyFilter = isset($_GET['faculty']) ? intval($_GET['faculty']) : 0;
$deptFilter = isset($_GET['department']) ? intval($_GET['department']) : 0;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = [];
$params = [];
$types = '';

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

// Điều kiện lọc
if ($facultyFilter > 0) {
    $sql .= " AND s.faculty_id = ?";
    $params[] = $facultyFilter;
    $types .= 'i';
}
if ($deptFilter > 0) {
    $sql .= " AND s.department_id = ?";
    $params[] = $deptFilter;
    $types .= 'i';
}
if ($search !== '') {
    $sql .= " AND s.name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($r = $result->fetch_assoc()) {
    // Xử lý đề tài
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

    // Xử lý bài báo
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

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Hệ thống quản lý khoa học - Xem dữ liệu</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container" style="padding:20px">
        <div class="header">
            <div class="title">Hệ thống quản lý khoa học</div>
            <div>
                <a class="btn" href="index.php">Quay lại</a>
            </div>
        </div>

        <!-- Form lọc -->
        <form method="get" style="border:1px solid #e6e6e6;padding:10px;border-radius:6px;background:#fff;">
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <!-- Dropdown Khoa -->
                <select name="faculty" id="facultySelect">
                    <option value="0">Tất cả khoa</option>
                    <?php foreach ($faculties as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= ($facultyFilter == $f['id'] ? 'selected' : '') ?>>
                            <?= htmlspecialchars($f['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Dropdown Bộ môn (sẽ tải qua JS) -->
                <select name="department" id="departmentSelect">
                    <option value="0">Tất cả bộ môn</option>
                </select>

                <input type="text" name="q" placeholder="Tìm kiếm theo họ tên" value="<?= htmlspecialchars($search) ?>">

                <div style="margin-left:auto;display:flex;gap:8px">
                    <button class="btn" type="submit">Tìm</button>
                    <a class="btn" href="?export=csv">Xuất Excel</a>
                    <a class="btn" href="download_all_files.php">Tải tất cả file</a>
                </div>
            </div>
        </form>

        <!-- Bảng kết quả -->
        <table class="table">
            <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>Khoa</th>
                    <th>Bộ môn</th>
                    <th>Tên hoạt động</th>
                    <th>Loại</th>
                    <th>Số tiết quy đổi</th>
                    <th>Số tiết đã thực hiện</th>
                    <th>Minh chứng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= htmlspecialchars($r['faculty']) ?></td>
                        <td><?= htmlspecialchars($r['department']) ?></td>
                        <td><?= htmlspecialchars($r['activity_name']) ?></td>
                        <td><?= htmlspecialchars($r['type']) ?></td>
                        <td><?= htmlspecialchars($r['total_hours']) ?></td>
                        <td><?= htmlspecialchars($r['completed_hours']) ?></td>
                        <td>
                            <?php if (!empty($r['files'])): ?>
                                <?php foreach ($r['files'] as $f): ?>
                                    <?php if (!empty($f['path'])): ?>
                                        <div>
                                            <a href="<?= htmlspecialchars($f['path']) ?>" download>
                                                <?= htmlspecialchars($f['original']) ?>
                                            </a> (<?= round(($f['size'] ?? 0) / 1024) ?> KB)
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Lấy bộ môn theo khoa khi trang load hoặc chọn khoa
        function loadDepartments(fid, selected = 0) {
            fetch(`get_departments.php?faculty_id=${fid}`)
                .then(res => res.json())
                .then(depts => {
                    const sel = document.getElementById('departmentSelect');
                    sel.innerHTML = '<option value="0">Tất cả bộ môn</option>';
                    depts.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.name;
                        if (selected == d.id) opt.selected = true;
                        sel.appendChild(opt);
                    });
                });
        }

        // Khi chọn khoa → load lại bộ môn
        document.getElementById('facultySelect').addEventListener('change', function() {
            loadDepartments(this.value);
        });

        // Tự động tải bộ môn khi trang mở nếu đã chọn khoa
        const selectedFaculty = <?= (int)$facultyFilter ?>;
        const selectedDept = <?= (int)$deptFilter ?>;
        if (selectedFaculty > 0) {
            loadDepartments(selectedFaculty, selectedDept);
        }
    </script>
</body>

</html>