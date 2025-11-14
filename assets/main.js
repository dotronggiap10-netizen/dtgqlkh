// assets/main.js

document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const addTopicBtn = document.getElementById('addTopicBtn');
    const topicsContainer = document.getElementById('topicsContainer');
    const maxTopics = 10;

    const addArticleBtn = document.getElementById('addArticleBtn');
    const articlesContainer = document.getElementById('articlesContainer');
    const maxArticles = 10;

    // Template helpers
    function createTopicCard(index) {
        const div = document.createElement('div');
        div.className = 'card topic-card';
        div.dataset.index = index;
        div.innerHTML = `
    <div class="card-title">
        <strong>Đề tài ${index + 1}</strong>
        <div>
            <button type="button" class="small-btn btn-delete-topic">🗑️</button>
        </div>
    </div>
    <div class="card-body">
        <div style="display:flex;gap:8px; align-items:flex-end;">
            <div style="flex:1">
                <label>Tên đề tài</label>
                <input type="text" name="topics[${index}][title]" placeholder="Nhập tên đề tài">
            </div>
            <div style="flex:1">
                <label>Loại đề tài</label>
                <select name="topics[${index}][type]">
                    <option value="">-- Chọn loại --</option>
                    <option>Cấp Khoa</option>
                    <option>Cấp Trường</option>
                </select>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
            <strong>Danh sách thành viên</strong>
            <button type="button" class="small-btn add-member">Thêm thành viên</button>
        </div>
        <div class="members-list" style="margin-top:8px">
            <div class="member-item" style="gap:8px">
                <input type="text" name="topics[${index}][members][]" placeholder="Họ và tên thành viên">
                <button type="button" class="small-btn btn-remove-member">🗑️</button>
            </div>
        </div>
        
        <div style="display:flex;gap:8px;margin-top:8px;align-items:flex-end;">
            <div style="flex:1">
                <label>Nhiệm thu loại</label>
                <select name="topics[${index}][grant_type]">
                    <option value="">-- Chọn --</option>
                    <option>Loại A</option>
                    <option>Loại B</option>
                </select>
            </div>
            <div style="flex:1">
                <label>Số tiết quy đổi</label>
                <input type="text" name="topics[${index}][total_hours]">
            </div>
            <div style="flex:1">
                <label>Số tiết đã thực hiện</label>
                <input type="text" name="topics[${index}][completed_hours]">
            </div>
        </div>

        <label style="margin-top:8px">Tài liệu đính kèm</label>
        <div class="file-drop" data-target="topics[${index}][files]">Kéo/thả hoặc bấm để chọn file</div>
        <input type="file" multiple name="topics_files_${index}[]" style="display:none">
        <div class="files-list"></div>

    </div>
    `;
        bindTopicBehaviors(div);
        return div;
    }

    function bindTopicBehaviors(card) {
        // delete
        card.querySelector('.btn-delete-topic').addEventListener('click', function () {
            card.remove();
            updateTopicIndexes();
        });
        // add member
        const membersList = card.querySelector('.members-list');
        card.querySelector('.add-member').addEventListener('click', function () {
            const idx = card.dataset.index;
            const item = document.createElement('div');
            item.className = 'member-item';
            item.innerHTML = `<input type="text" name="topics[${idx}][members][]" placeholder="Họ và tên thành viên"><button type="button" class="small-btn btn-remove-member">🗑️</button>`;
            membersList.appendChild(item);
            item.querySelector('.btn-remove-member').addEventListener('click', () => item.remove());
        });
        // initial remove member btns
        membersList.querySelectorAll('.btn-remove-member').forEach(btn => {
            btn.addEventListener('click', (e) => { e.target.closest('.member-item').remove(); });
        });

        // file drop behavior
        const fileDrop = card.querySelector('.file-drop');
        const fileInput = card.querySelector('input[type=file]');
        const filesListDiv = card.querySelector('.files-list');

        fileDrop.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => renderFiles(e.target.files, filesListDiv, fileInput));
        ['dragenter', 'dragover'].forEach(ev => {
            fileDrop.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); fileDrop.style.borderColor = '#999'; });
        });
        ['dragleave', 'drop'].forEach(ev => {
            fileDrop.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); fileDrop.style.borderColor = '#ddd'; });
        });
        fileDrop.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            renderFiles(files, filesListDiv, fileInput);
        });
    }

    function renderFiles(files, container, fileInput) {
        container.innerHTML = '';
        for (let i = 0; i < files.length; i++) {
            const f = files[i];
            const div = document.createElement('div');
            div.className = 'file-item';
            div.innerHTML = `<div>${f.name} (${Math.round(f.size / 1024)} KB)</div><div><button type="button" class="small-btn remove-file">x</button></div>`;
            container.appendChild(div);
            div.querySelector('.remove-file').addEventListener('click', () => {
                // remove file from FileList is hard -> create DataTransfer
                const dt = new DataTransfer();
                for (let j = 0; j < fileInput.files.length; j++) {
                    if (j !== i) dt.items.add(fileInput.files[j]);
                }
                fileInput.files = dt.files;
                renderFiles(fileInput.files, container, fileInput);
            });
        }
    }

    // Add topic
    addTopicBtn.addEventListener('click', () => {
        const count = topicsContainer.querySelectorAll('.topic-card').length;
        if (count >= maxTopics) {
            alert('Tối đa ' + maxTopics + ' đề tài.');
            return;
        }
        const newCard = createTopicCard(count);
        topicsContainer.appendChild(newCard);
        updateTopicIndexes();
    });

    function updateTopicIndexes() {
        const cards = topicsContainer.querySelectorAll('.topic-card');
        cards.forEach((card, i) => {
            card.dataset.index = i;
            card.querySelector('.card-title strong').textContent = 'Đề tài ' + (i + 1);
            // update name attributes for inputs & selects & member items
            card.querySelectorAll('input, select, .file-drop, input[type=file]').forEach(el => {
                if (el.name) {
                    el.name = el.name.replace(/topics\[\d+\]/, 'topics[' + i + ']');
                }
                if (el.getAttribute('data-target')) {
                    el.setAttribute('data-target', 'topics[' + i + '][files]');
                }
            });
            // members rename
            card.querySelectorAll('.members-list .member-item input').forEach(inp => {
                inp.name = 'topics[' + i + '][members][]';
            });
        });
    }

    // Articles (similar simplified)
    function createArticleCard(index) {
        const div = document.createElement('div');
        div.className = 'card article-card';
        div.dataset.index = index;
        div.innerHTML = `
    <div class="card-title">
        <strong>Bài báo ${index + 1}</strong>
        <div>
            <button type="button" class="small-btn btn-delete-article">🗑️</button>
        </div>
    </div>
    <div class="card-body">
        <label>Tên tác giả chính</label>
        <input type="text" name="articles[${index}][main_author]">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
            <strong>Danh sách thành viên</strong>
            <button type="button" class="small-btn add-collab">Thêm thành viên</button>
        </div>
        <div class="collab-list" style="margin-top:8px">
        <div class="collab-item" style="gap:8px">
            <input type="text" name="articles[${index}][collaborators][]" placeholder="Thành viên 1">
            <button type="button" class="small-btn btn-remove-collab">🗑️</button>
        </div>
        </div>

        <label>Tên bài báo</label>
        <input type="text" name="articles[${index}][title]" placeholder="Nhập tên bài báo">
        <div style="display:flex;gap:8px;margin-top:8px">
            <div style="flex:1">
                <label>Xếp hạng tạp chí</label>
                <input type="text" name="articles[${index}][rank]">
            </div>
            <div style="flex:1">
                <label>Đăng trên tạp chí</label>
                <input type="text" name="articles[${index}][journal]">
            </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px">
            <div style="flex:1"><label>Số tập</label><input type="text" name="articles[${index}][volume]"></div>
            <div style="flex:1"><label>Số DOI</label><input type="text" name="articles[${index}][doi]"></div>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px">
            <div style="flex:1"><label>Số tiết quy đổi</label><input type="text" name="articles[${index}][total_hours]"></div>
            <div style="flex:1"><label>Số tiết đã thực hiện</label><input type="text" name="articles[${index}][completed_hours]"></div>
        </div>

        <label style="margin-top:8px">Tài liệu đính kèm</label>
        <div class="file-drop" data-target="articles[${index}][files]">Kéo/thả hoặc bấm để chọn file</div>
        <input type="file" multiple name="articles_files_${index}[]" style="display:none">
        <div class="files-list"></div>
    </div>
    `;
        bindArticleBehaviors(div);
        return div;
    }

    function bindArticleBehaviors(card) {
        card.querySelector('.btn-delete-article').addEventListener('click', () => { card.remove(); updateArticleIndexes(); });
        const collabList = card.querySelector('.collab-list');
        card.querySelector('.add-collab').addEventListener('click', () => {
            const idx = card.dataset.index;
            const item = document.createElement('div');
            item.className = 'collab-item';
            item.innerHTML = `<input type="text" name="articles[${idx}][collaborators][]" placeholder="Thành viên"><button type="button" class="small-btn btn-remove-collab">🗑️</button>`;
            collabList.appendChild(item);
            item.querySelector('.btn-remove-collab').addEventListener('click', () => item.remove());
        });
        collabList.querySelectorAll('.btn-remove-collab').forEach(btn => btn.addEventListener('click', (e) => e.target.closest('.collab-item').remove()));

        // file behavior as with topics
        const fileDrop = card.querySelector('.file-drop');
        const fileInput = card.querySelector('input[type=file]');
        const filesListDiv = card.querySelector('.files-list');
        fileDrop.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => renderFiles(e.target.files, filesListDiv, fileInput));
        ['dragenter', 'dragover'].forEach(ev => {
            fileDrop.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); fileDrop.style.borderColor = '#999'; });
        });
        ['dragleave', 'drop'].forEach(ev => {
            fileDrop.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); fileDrop.style.borderColor = '#ddd'; });
        });
        fileDrop.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            renderFiles(files, filesListDiv, fileInput);
        });
    }

    addArticleBtn.addEventListener('click', () => {
        const count = articlesContainer.querySelectorAll('.article-card').length;
        if (count >= maxArticles) {
            alert('Tối đa ' + maxArticles + ' bài báo.');
            return;
        }
        const newCard = createArticleCard(count);
        articlesContainer.appendChild(newCard);
        updateArticleIndexes();
    });

    function updateArticleIndexes() {
        const cards = articlesContainer.querySelectorAll('.article-card');
        cards.forEach((card, i) => {
            card.dataset.index = i;
            card.querySelector('.card-title strong').textContent = 'Bài báo ' + (i + 1);
            card.querySelectorAll('input, select, .file-drop, input[type=file]').forEach(el => {
                if (el.name) {
                    el.name = el.name.replace(/articles\[\d+\]/, 'articles[' + i + ']');
                }
                if (el.getAttribute('data-target')) {
                    el.setAttribute('data-target', 'articles[' + i + '][files]');
                }
            });
            card.querySelectorAll('.collab-list .collab-item input').forEach(inp => {
                inp.name = 'articles[' + i + '][collaborators][]';
            });
        });
    }

    // initial: ensure at least 1 topic and 1 article exist
    if (topicsContainer.querySelectorAll('.topic-card').length === 0) {
        topicsContainer.appendChild(createTopicCard(0));
    }
    if (articlesContainer.querySelectorAll('.article-card').length === 0) {
        articlesContainer.appendChild(createArticleCard(0));
    }

    // form submit: combine file inputs & dynamic fields into FormData for upload
    const mainForm = document.getElementById('mainForm');
    mainForm.addEventListener('submit', function (e) {
        // We let the form submit normally to process_save.php which handles dynamic files via named file inputs.
        // But ensure that files inputs have unique names already: topics_files_0[], articles_files_0[], etc.
        // Nothing else to do here.
    });

    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
            document.getElementById(tab.dataset.target).style.display = 'block';
        });
    });

});

