# 4ViewsSocial

Hệ sinh thái Mạng xã hội 4ViewsSocial được xây dựng theo kiến trúc Microservices.

## 🏗️ Kiến trúc Hệ thống

- **Frontend Client**: Vue 3 + Vite (`http://localhost:5173`)
- **API Gateway**: Express.js + TypeScript (`http://localhost:4000`)
- **Auth Service**: Laravel 12 / PHP 8.2 (`http://localhost:8001`)
- **Chat Service**: Node.js + Socket.IO (`http://localhost:8002`)
- **Admin Service**: Laravel 12 / PHP 8.2 (`http://localhost:8003`)
- **Timeline Service**: Laravel 12 / PHP 8.2 (`http://localhost:8004`)
- **Databases**: MySQL 8.0 (Port 3306) & MongoDB 7.0 (Port 27017)

---

## 🚀 Khởi chạy dự án bằng Docker (Khuyên dùng)

### Yêu cầu:
- Đã cài đặt [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Khởi động (1 lệnh duy nhất):
```bash
docker compose up --build -d
```

### Dừng hệ thống:
```bash
docker compose down
```

---

## 💻 Thiết lập Môi trường Thủ công (Không dùng Docker)

1. Sao chép các file cấu hình `.env.example` thành `.env` trong từng thư mục service tương ứng.
2. Cài đặt dependencies cho từng service:
   - Laravel services: `composer install`
   - Node.js services & Client: `npm install`
3. Chạy migrations cho database MySQL:
   ```bash
   php artisan migrate
   ```
4. Khởi chạy toàn bộ hệ thống bằng script Python:
   ```bash
   python run_all.py
   ```
