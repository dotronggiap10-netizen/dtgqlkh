DROP TABLE IF EXISTS articles CASCADE;
DROP TABLE IF EXISTS topics CASCADE;
DROP TABLE IF EXISTS submissions CASCADE;
DROP TABLE IF EXISTS departments CASCADE;
DROP TABLE IF EXISTS faculties CASCADE;

-- ===========================
-- BẢNG faculties
-- ===========================
CREATE TABLE faculties (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

INSERT INTO faculties (id, name) VALUES
(1, 'Khoa Toán'),
(2, 'Khoa Vật lý'),
(3, 'Trung tâm CNTT');

SELECT setval('faculties_id_seq', 4, false);

-- ===========================
-- BẢNG departments
-- ===========================
CREATE TABLE departments (
    id SERIAL PRIMARY KEY,
    faculty_id INTEGER NOT NULL REFERENCES faculties(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL
);

INSERT INTO departments (id, faculty_id, name) VALUES
(1, 1, 'Toán thuần'),
(2, 1, 'Toán Ứng dụng'),
(3, 2, 'Vật lý lý thuyết'),
(4, 2, 'Vật lý ứng dụng'),
(5, 3, 'Phát triển phần mềm'),
(6, 3, 'Mạng & Hệ thống');

SELECT setval('departments_id_seq', 7, false);

-- ===========================
-- BẢNG submissions
-- ===========================
CREATE TABLE submissions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    faculty_id INTEGER REFERENCES faculties(id),
    department_id INTEGER REFERENCES departments(id)
);

INSERT INTO submissions (id, name, created_at, faculty_id, department_id) VALUES
(5, 'Đặng Ngọc Quyền', '2025-10-17 01:13:41', 1, 1);

SELECT setval('submissions_id_seq', 6, false);

-- ===========================
-- BẢNG topics
-- ===========================
CREATE TABLE topics (
    id SERIAL PRIMARY KEY,
    submission_id INTEGER NOT NULL REFERENCES submissions(id) ON DELETE CASCADE,
    title VARCHAR(500) NOT NULL,
    topic_type VARCHAR(255),
    members JSONB,
    grant_type VARCHAR(255),
    total_hours NUMERIC DEFAULT 0,
    completed_hours NUMERIC DEFAULT 0,
    files JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT setval('topics_id_seq', 5, false);

-- ===========================
-- BẢNG articles
-- ===========================
CREATE TABLE articles (
    id SERIAL PRIMARY KEY,
    submission_id INTEGER NOT NULL REFERENCES submissions(id) ON DELETE CASCADE,
    main_author VARCHAR(255),
    collaborators JSONB,
    title VARCHAR(500) NOT NULL,
    rank VARCHAR(255),
    journal VARCHAR(500),
    volume VARCHAR(255),
    doi VARCHAR(255),
    total_hours NUMERIC DEFAULT 0,
    completed_hours NUMERIC DEFAULT 0,
    files JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT setval('articles_id_seq', 2, false);
