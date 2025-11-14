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

$rows = [];

foreach ($result as $r) {

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <style>
        .header-bar {
            background: #0d6efd;
            padding: 15px;
            color: white;
            font-size: 22px;
            font-weight: bold;
        }
        .badge-topic { background: #0d6efd !important; }
        .badge-article { background: #28a745 !important; }
        .table-box { background: white; padding: 15px; border-radius: 10px; }
    </style>
</head>
<body class="bg-light">

<!-- HEADER -->
<div class="header-bar d-flex justify-content-between">
    <div>📂 Xem dữ liệu</div>
    <a href="index.php" class="btn btn-light btn-sm">⬅ Quay về</a>
</div>

<div class="container mt-3">

    <!-- FORM LỌC + 3 NÚT EXPORT -->
    <form method="GET" class="row g-2 mb-3">

        <!-- Chọn khoa -->
        <div class="col-md-3">
            <select name="faculty" class="form-select">
                <option value="0">-- Chọn khoa --</option>
                <?php foreach ($faculties as $f): ?>
                <option value="<?= $f['id'] ?>" <?= $facultyFilter == $f['id'] ? 'selected' : '' ?>>
                    <?= $f['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Bộ môn -->
        <div class="col-md-3">
            <select name="department" id="departmentSelect" class="form-select">
                <option value="0">-- Chọn bộ môn --</option>
            </select>
        </div>

        <!-- Tìm kiếm -->
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($search) ?>">
        </div>

        <!-- Nút lọc -->
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Lọc</button>
        </div>

        <!-- Xuất Excel -->
        <div class="col-md-3">
            <a href="export_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-success w-100">📊 Xuất Excel</a>
        </div>

        <!-- Xuất PDF -->
        <div class="col-md-3">
            <a href="export_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger w-100">📄 Xuất PDF</a>
        </div>

        <!-- Tải tất cả file -->
        <div class="col-md-3">
            <a href="download_all.php?<?= http_build_query($_GET) ?>" class="btn btn-secondary w-100">⬇ Tải file</a>
        </div>

    </form>

    <!-- BẢNG DỮ LIỆU -->
    <div class="table-box shadow-sm">

        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Họ tên</th>
                    <th>Khoa</th>
                    <th>Bộ môn</th>
                    <th>Loại</th>
                    <th>Hoạt động</th>
                    <th>Tổng giờ</th>
                    <th>Hoàn thành</th>
                    <th>File</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['faculty'] ?></td>
                    <td><?= $row['department'] ?></td>

                    <td>
                        <?php if ($row['type'] == "Đề tài"): ?>
                            <span class="badge badge-topic">Đề tài</span>
                        <?php else: ?>
                            <span class="badge badge-article">Bài báo</span>
                        <?php endif; ?>
                    </td>

                    <td><?= $row['activity_name'] ?></td>
                    <td><?= $row['total_hours'] ?></td>
                    <td><?= $row['completed_hours'] ?></td>

                    <td>
                        <?php if (!empty($row['files'])): ?>
                            <?php foreach ($row['files'] as $f): ?>
                                <a href="<?= $f ?>" target="_blank" class="d-block">
                                    📎 <?= basename($f) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            Không có
                        <?php endif; ?>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- AJAX load bộ môn -->
<script>
$(document).ready(function() {
    function loadDepartments() {
        let facultyId = <?= $facultyFilter ?>;
        let deptSelected = <?= $deptFilter ?>;

        if (facultyId === 0) return;

        $.get("get_departments.php?faculty_id=" + facultyId, function(data) {
            $("#departmentSelect").html('<option value="0">-- Chọn bộ môn --</option>');
            let list = JSON.parse(data);

            list.forEach(item => {
                let sel = item.id == deptSelected ? "selected" : "";
                $("#departmentSelect").append(`<option value="${item.id}" ${sel}>${item.name}</option>`);
            });
        });
    }

    loadDepartments();
});
</script>

</body>
</html>
