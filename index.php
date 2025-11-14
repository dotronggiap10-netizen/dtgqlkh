<?php
require 'config.php';
?>

<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Hệ thống quản lý sản phẩm khoa học</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="assets/style.css">
    
</head>

<body>
    <?php include 'header.php';?>
    <div class="container" style="padding:20px">
        <div class="header">
            <div class="title">Hệ thống quản lý sản phẩm khoa học</div>
            <div>
                <a class="btn btn-black" href="view.php">Xem dữ liệu</a>
            </div>
        </div>

        <form id="mainForm" method="post" action="process_save.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="field">
                    <label>Họ và tên</label>
                    <input type="text" name="name" required>
                </div>
                <div class="field">
                    <label>Khoa/Trung tâm</label>
                    <select name="faculty" id="facultySelect" required>
                        <option value="">-- Chọn Khoa --</option>
                    </select>
                </div>
                <div class="field">
                    <label>Bộ môn</label>
                    <select name="department" id="departmentSelect" required>
                        <option value="">-- Chọn Bộ môn --</option>
                    </select>
                </div>

            </div>

            <div class="note-success" id="successNote">Thành công: Đã lưu thông tin thành công.</div>

            <div class="tabs" style="margin-top:12px">
                <div class="tab active" data-target="tabTopics">Đề tài nghiên cứu</div>
                <div class="tab" data-target="tabArticles">Bài báo khoa học</div>
                <div class="tab" data-target="tabBook">Sách/Giáo trình</div>
                <div class="tab" data-target="tabGuide">Hướng dẫn sinh viên</div>
                <div class="tab" data-target="tabPrize">Giải thưởng</div>
            </div>

            <div id="tabTopics" class="tab-content" style="display:block">
                <div class="section-header">
                    <h3>Danh sách đề tài</h3>
                    <button type="button" id="addTopicBtn" class="btn small-btn">Thêm đề tài</button>
                </div>

                <div id="topicsContainer">
                    <!-- JS sẽ tạo tối thiểu 1 form đề tài -->
                </div>

                <div style="text-align:center;margin-top:8px">
                    <button style="min-width: 100%;" type="submit" name="action" value="save_topics" class="btn">Lưu thông tin</button>
                </div>
            </div>

            <div id="tabArticles" class="tab-content" style="display:none">
                <div class="section-header">
                    <h3>Danh sách bài báo</h3>
                    <button type="button" id="addArticleBtn" class="btn small-btn">Thêm bài báo</button>
                </div>

                <div id="articlesContainer">
                    <!-- JS sẽ tạo tối thiểu 1 form bài báo -->
                </div>

                <div style="text-align:right;margin-top:8px">
                    <button type="submit" name="action" value="save_articles" class="btn">Lưu thông tin</button>
                </div>
            </div>
            <div id="tabBook" class="tab-content" style="display:none">
                <p>
                    Tính năng thêm Sách/Giáo trình đang phát triển.
                </p>
            </div>
            <div id="tabGuide" class="tab-content" style="display:none">
                <p>
                    Tính năng thêm Hướng dẫn sinh viên đang phát triển.
                </p>
            </div>
            <div id="tabPrize" class="tab-content" style="display:none">
                <p>
                    Tính năng thêm Giải thưởng đang phát triển.
                </p>
            </div>
        </form>
    </div>

    <script>
        // Khi trang tải xong, lấy danh sách khoa từ DB
        document.addEventListener("DOMContentLoaded", function() {
            fetch('get_faculties.php')
                .then(res => res.json())
                .then(data => {
                    const facultySelect = document.getElementById('facultySelect');
                    data.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.textContent = f.name;
                        facultySelect.appendChild(opt);
                    });
                });

            // Khi chọn khoa → tải bộ môn tương ứng
            document.getElementById('facultySelect').addEventListener('change', function() {
                const facultyId = this.value;
                const deptSelect = document.getElementById('departmentSelect');
                deptSelect.innerHTML = '<option value="">-- Chọn Bộ môn --</option>';

                if (facultyId) {
                    fetch(`get_departments.php?faculty_id=${facultyId}`)
                        .then(res => res.json())
                        .then(depts => {
                            depts.forEach(d => {
                                const opt = document.createElement('option');
                                opt.value = d.id;
                                opt.textContent = d.name;
                                deptSelect.appendChild(opt);
                            });
                        });
                }
            });
        });
    </script>


    <!-- include main JS: you may put the script contents of assets/main.js here or reference file -->
    <script src="assets/main.js"></script>

</body>
<?php include 'footer.php';?>
</html>