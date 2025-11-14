# 🧠 Website Trắc Nghiệm Trực Tuyến

Một nền tảng trắc nghiệm trực tuyến đơn giản và mạnh mẽ được xây dựng bằng **PHP & MySQL**, cho phép người dùng làm bài kiểm tra, xem kết quả, và giúp quản trị viên dễ dàng quản lý câu hỏi, bài thi, cũng như kết quả của người dùng.

---

## 🚀 Tính năng chính

### 👨‍🎓 Dành cho người dùng:
- Đăng ký và đăng nhập tài khoản.  
- Làm bài trắc nghiệm theo từng **môn học**.  
- Xem **điểm số và kết quả chi tiết** sau khi hoàn thành bài thi.  
- Hỗ trợ câu hỏi có **hình ảnh minh họa** (sơ đồ, công thức,...).

### 🧑‍💼 Dành cho quản trị viên (Admin):
- Quản lý người dùng, môn học, và bài trắc nghiệm.  
- **Thêm / sửa / xóa** câu hỏi trực tiếp hoặc **nhập nhanh từ file CSV**.  
- Xem và thống kê kết quả chi tiết của từng người làm bài.  
- Giao diện **thân thiện, responsive** – hiển thị tốt trên cả máy tính và điện thoại.

---

## 🛠️ Công nghệ sử dụng
- **PHP (thuần)**  
- **MySQL**  
- **HTML / CSS / JavaScript**  
- **Responsive design** với CSS3 & Flexbox/Grid  

---

## ⚙️ Cài đặt và chạy thử

1. Clone dự án:
   ```bash
   git clone https://github.com/Dquyen2k3/Quiz-app.git
   ```

2. Mở thư mục dự án trong XAMPP (ví dụ: `htdocs/quiz_app`).

3. Tạo database `quiz_app` trong **phpMyAdmin**, sau đó import file `quiz_app.sql`.

4. Cập nhật thông tin kết nối trong `includes/db.php`:
   ```php
   $host = "localhost";
   $user = "root"; // mặc định XAMPP
   $pass = "";     // mật khẩu rỗng (nếu bạn không đặt)
   $db   = "quiz_app";//tên database
   ```

5. Mở trình duyệt và truy cập:
   ```
   http://localhost/quiz_app/
   ```

---

## 📸 Giao diện mẫu
![Giao diện chính](img/demo1.png)
![Làm bài thi](img/demo2.png)
![Trang quản trị](img/demo3.png)

---

## 🧑‍💻 Tác giả
**Đặng Ngọc Quyền**  
📧 Liên hệ: dquyen104@gmail.com
🌐 Dự án chia sẻ miễn phí cho mục đích học tập và phát triển.

---

## 🪪 Giấy phép
Dự án được phát hành theo giấy phép **MIT License** – bạn có thể tự do sử dụng và chỉnh sửa.
