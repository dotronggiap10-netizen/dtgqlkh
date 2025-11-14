<?php
require 'config.php';
require 'functions.php';

// helper to get POST safely
function post($k, $default = '')
{
    return isset($_POST[$k]) ? $_POST[$k] : $default;
}

$name = trim(post('name'));
$faculty_id = intval(post('faculty'));      // giờ faculty là id
$department_id = intval(post('department')); // giờ department là id

if (empty($name) || $faculty_id <= 0 || $department_id <= 0) {
    die('Thiếu thông tin chung hoặc chưa chọn Khoa/Bộ môn hợp lệ.');
}

try {
    $pdo->beginTransaction();

    // ✅ 1. insert submission với ID Khoa và Bộ môn
    $stmt = $pdo->prepare("INSERT INTO submissions (name, faculty_id, department_id) VALUES (?, ?, ?)");
    $stmt->execute([$name, $faculty_id, $department_id]);
    $submission_id = $pdo->lastInsertId();

    // ✅ 2. xử lý danh sách đề tài (topics)
    if (!empty($_POST['topics'])) {
        foreach ($_POST['topics'] as $idx => $t) {
            $title = trim($t['title'] ?? '');
            if ($title === '') continue;

            $type = trim($t['type'] ?? '');
            $members = isset($t['members']) ? array_values(array_filter(array_map('trim', $t['members']))) : [];
            $grant_type = trim($t['grant_type'] ?? '');
            $total_hours = floatval($t['total_hours'] ?? 0);
            $completed_hours = floatval($t['completed_hours'] ?? 0);

            // Xử lý file upload
            $filesSaved = [];
            $fileField = 'topics_files_' . $idx;
            if (!empty($_FILES[$fileField])) {
                $filesSaved = save_uploaded_files($_FILES[$fileField]);
            }

            $stmt = $pdo->prepare("
                INSERT INTO topics (submission_id, title, topic_type, members, grant_type, total_hours, completed_hours, files)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $submission_id,
                $title,
                $type,
                json_encode($members, JSON_UNESCAPED_UNICODE),
                $grant_type,
                $total_hours,
                $completed_hours,
                json_encode($filesSaved, JSON_UNESCAPED_UNICODE)
            ]);
        }
    }

    // ✅ 3. xử lý danh sách bài báo (articles)
    if (!empty($_POST['articles'])) {
        foreach ($_POST['articles'] as $idx => $a) {
            $title = trim($a['title'] ?? '');
            if ($title === '') continue;

            $main_author = trim($a['main_author'] ?? '');
            $collaborators = isset($a['collaborators']) ? array_values(array_filter(array_map('trim', $a['collaborators']))) : [];
            $rank = trim($a['rank'] ?? '');
            $journal = trim($a['journal'] ?? '');
            $volume = trim($a['volume'] ?? '');
            $doi = trim($a['doi'] ?? '');
            $total_hours = floatval($a['total_hours'] ?? 0);
            $completed_hours = floatval($a['completed_hours'] ?? 0);

            // Xử lý file upload
            $filesSaved = [];
            $fileField = 'articles_files_' . $idx;
            if (!empty($_FILES[$fileField])) {
                $filesSaved = save_uploaded_files($_FILES[$fileField]);
            }

            $stmt = $pdo->prepare("
                INSERT INTO articles (submission_id, main_author, collaborators, title, rank, journal, volume, doi, total_hours, completed_hours, files)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $submission_id,
                $main_author,
                json_encode($collaborators, JSON_UNESCAPED_UNICODE),
                $title,
                $rank,
                $journal,
                $volume,
                $doi,
                $total_hours,
                $completed_hours,
                json_encode($filesSaved, JSON_UNESCAPED_UNICODE)
            ]);
        }
    }

    $pdo->commit();

    // ✅ chuyển hướng lại index
    header('Location: index.php?saved=1');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die('Lỗi khi lưu: ' . $e->getMessage());
}
