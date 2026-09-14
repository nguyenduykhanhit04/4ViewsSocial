# Client Web (4ViewsSocial Frontend)

Giao diện Web Frontend của mạng xã hội 4ViewsSocial, xây dựng trên nền tảng **Vue 3**, **Vite**, **Pinia** và **Bootstrap 5**.

---

## 🏗️ Kiến trúc thư mục (Layered Architecture)

```
client-web/
├── public/                     # Static assets công khai
├── src/
│   ├── api/                    # Tầng giao tiếp HTTP API tới API Gateway
│   │   ├── http.client.js      # Axios instance tập trung với Interceptors & Auth token
│   │   ├── client.js           # Alias hỗ trợ tương thích ngược
│   │   ├── auth.api.js         # API Xác thực (Login, Register, Google OAuth, Logout)
│   │   ├── timeline.api.js     # API Bảng tin, Bài viết, Story, Like, Bình luận
│   │   ├── user.api.js         # API Trang cá nhân, Bạn bè, Tìm kiếm, Thông báo
│   │   ├── chat.api.js         # API Nhắn tin thời gian thực
│   │   ├── admin.api.js        # API Cổng quản trị Admin
│   │   └── index.js            # Re-export các module API
│   ├── assets/                 # Styles, images và font cục bộ
│   ├── components/             # Reusable UI Components
│   │   ├── admin/              # Component quản trị
│   │   ├── auth/               # Component đăng nhập / đăng ký
│   │   ├── layouts/            # Layouts (AdminLayout, ...)
│   │   ├── ChatWidget.vue      # Cửa sổ chat nổi
│   │   └── SidebarComponent.vue# Thanh điều hướng chính
│   ├── router/                 # Cấu hình Vue Router
│   ├── stores/                 # Quản lý State tập trung với Pinia
│   │   └── auth.store.js       # Store quản lý thông tin User và Auth Token
│   ├── views/                  # Các trang màn hình chính (HomePage, Profile, Explore, Message, Admin...)
│   ├── firebase.js             # Cấu hình Firebase & FCM Push Notifications
│   ├── presence.js             # Quản lý trạng thái Online/Offline Realtime
│   ├── App.vue                 # Root Component
│   └── main.js                 # Entry point khởi tạo Vue App
├── Dockerfile                  # Cấu hình đóng gói Container
├── package.json
└── vite.config.js
```

---

## 🚀 Khởi chạy dự án

### Chạy bằng Node / Vite:
```bash
npm install
npm run dev
```

### Chạy bằng Docker:
```bash
docker compose up -d client_service
```
Trang web sẽ khả dụng tại: `http://localhost:5173`