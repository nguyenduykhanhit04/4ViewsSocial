<template>
    <div class="col-md-2 sidebar" style="padding: 50px;">
        <h3 class="mb-4">4ViewsSocial</h3>
        <div class="nav flex-column gap-3 sidebar-menu">
            <a href="/homepage" class="text-dark"><i class="bi bi-house-door"></i> Trang chủ</a>
            <a 
                class="text-dark"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasSearch"
                >
                <i class="bi bi-search"></i> Tìm kiếm
            </a>
            <a href="/explore" class="text-dark"><i class="bi bi-compass"></i> Khám phá</a>
            <a href="/message" class="text-dark"><i class="bi bi-chat-dots"></i> Tin nhắn</a>
            <a 
                class="text-dark sidebar-item"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNotify"
                @click="fetchNotifications" 
            >
                <div class="icon-wrapper">
                    <i class="bi bi-bell"></i>
                    <span v-if="unreadCount > 0" class="custom-badge">
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </span>
                </div>
                <span class="menu-label">Thông báo</span>
            </a>
            <a href="#" 
                class="text-dark"
                data-bs-toggle="modal" 
                data-bs-target="#uploadModal">
                <i class="bi bi-plus-circle"></i> Tạo bài viết
            </a>
            <a href="/profile" class="text-dark"><i class="bi bi-person-circle"></i> Trang cá nhân</a>
        </div>
    </div>

    <!-- Thông báo -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNotify" style="border-radius: 10px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">Thông báo</h5>
            <button class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div v-if="unreadCount > 0" class="text-end p-2">
                <small class="text-primary cursor-pointer" @click="markNotificationsRead">Đánh dấu tất cả đã đọc</small>
            </div>

            <div v-if="notifications.length === 0" class="text-center mt-5 text-muted">
                <i class="bi bi-bell-slash fs-1"></i>
                <p>Chưa có thông báo nào</p>
            </div>

            <div 
                v-for="noti in notifications" 
                :key="noti.id" 
                class="notification-item d-flex align-items-center gap-3 p-3 border-bottom transition-all"
                :class="{ 
                    'unread-bg': noti.is_view === 0,
                    'is-clickable': noti.type === 0 
                }"
                @click="handleNotificationClick(noti)"
            >
                <div class="avatar-box-sm">
                    <img :src="noti.actor?.avatar_url || '../assets/logo.png'" class="rounded-circle" width="45" height="45" />
                </div>
                <div class="flex-grow-1">
                    <div class="noti-text">
                        <span class="fw-bold">{{ noti.actor?.full_name }}</span> 
                        {{ noti.content }} 
                        <span v-if="noti.type == 1">❤️</span>
                        <span v-if="noti.type == 2">💬</span>
                        <span v-if="noti.type == 3">🔥</span>
                        <span v-if="noti.type == 0">👤</span>
                    </div>
                    <div class="text-muted small">{{ formatTime(noti.created_at) }}</div>
                </div>
                <div v-if="noti.is_view === 0" class="unread-dot"></div>
            </div>
        </div>
    </div>

    <!-- Tìm kiếm -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSearch" style="border-radius: 10px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold">Tìm kiếm</h5>
            <button class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <input 
                type="text" 
                class="form-control mb-3" 
                placeholder="Nhập tên người dùng..." 
                v-model="searchKeyword"
                @input="handleSearch"
            />

            <div v-if="searchResults.length > 0">
                <div 
                    class="d-flex align-items-center mb-3 p-2 search-result-item" 
                    v-for="user in searchResults" 
                    :key="user.id"
                    @click="goToProfile(user.id)"
                    style="cursor: pointer; border-radius: 8px;"
                >
                    <img :src="user.avatar_url || 'https://via.placeholder.com/40'" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;" />
                    <div>
                        <div class="fw-bold" style="font-size: 14px;">{{ user.user_name }}</div>
                        <div class="text-muted" style="font-size: 13px;">{{ user.full_name }}</div>
                    </div>
                </div>
            </div>

            <div v-else-if="searchKeyword.length > 0" class="text-center text-muted mt-4">
                Không tìm thấy người dùng nào.
            </div>
        </div>
    </div>
</template>

<script>
import api from "../api/client";

export default {
    data() {
        return {
            notifications: [],
            unreadCount: 0,
            // search user
            searchKeyword: '',
            searchResults: [],
            searchTimeout: null,
            currentUser: null
        };
    },

    mounted() {
        const user = JSON.parse(sessionStorage.getItem('user_info'));
        if (user) {
            this.currentUser = user;
            this.fetchNotifications();
        }
    },

    methods: {
        // Chuyển hướng đến trang cá nhân
        goToProfile(userId) {
            // Chuyển sang route /profile/id
            this.$router.push(`/profile/${userId}`);
            
            // Đóng Offcanvas tìm kiếm sau khi click
            const offcanvasElement = document.getElementById('offcanvasSearch');
            // Kiểm tra nếu bootstrap đã được import toàn cục hoặc dùng window.bootstrap
            const bootstrap = window.bootstrap; 
            if (offcanvasElement && bootstrap) {
                const modal = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (modal) modal.hide();
            }
            
            // Xóa kết quả tìm kiếm cũ
            this.searchKeyword = '';
            this.searchResults = [];
        },

        // function handle search user
        handleSearch() {
            clearTimeout(this.searchTimeout);

            if (!this.searchKeyword.trim()) {
                this.searchResults = [];
                return;
            }

            this.searchTimeout = setTimeout(async () => {
                try {
                    const res = await api.post('/api/post/search-user', { 
                        keyword: this.searchKeyword 
                    });
                    
                    if (res.status === 200) {
                        // Laravel trả về object { users: [...] } trong data
                        this.searchResults = res.data.data.users || [];
                    }
                } catch (err) {
                    console.error("Lỗi tìm kiếm:", err);
                    this.searchResults = [];
                }
            }, 500);
        },

        async fetchNotifications() {
            if (!this.currentUser) {
                const user = JSON.parse(sessionStorage.getItem('user_info'));
                if (user) this.currentUser = user;
                else return;
            }

            try {
                const res = await api.get('/api/post/notifications', { 
                    params: { user_id: this.currentUser.id } 
                });
                if (res.status === 200) {
                    this.notifications = res.data.data.notifications;
                    this.unreadCount = res.data.data.unread_count;
                }
            } catch (err) {
                console.error("Lỗi lấy thông báo:", err);
            }
        },

        async markNotificationsRead() {
            try {
                const res = await api.post('/api/post/notifications/mark-read', { 
                    user_id: this.currentUser.id 
                });
                if (res.status === 200) {
                    this.notifications.forEach(n => n.is_view = 1);
                    this.unreadCount = 0;
                }
            } catch (err) {
                console.error(err);
            }
        },
        
        async handleNotificationClick(noti) {
            // 1. Luôn cho phép đánh dấu đã đọc khi bấm vào (để mất dấu chấm xanh)
            if (noti.is_view === 0) {
                try {
                    await api.post(`/api/post/notifications/mark-read/${noti.id}`);
                    noti.is_view = 1;
                    if (this.unreadCount > 0) this.unreadCount--;
                } catch (err) {
                    console.error("Lỗi cập nhật thông báo:", err);
                }
            }

            // 2. Kiểm tra loại thông báo
            if (noti.type === 0) { 
                // CHỈ XỬ LÝ CHO FOLLOW (TYPE 0)
                
                // Đóng Offcanvas
                const offcanvasElement = document.getElementById('offcanvasNotify');
                const bootstrap = window.bootstrap;
                if (offcanvasElement && bootstrap) {
                    const instance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (instance) instance.hide();
                }

                // Chuyển hướng đến trang cá nhân
                this.$router.push(`/profile/${noti.actor_id}`);
            } else {
                // CÁC LOẠI KHÁC: Không làm gì cả (Không đóng bảng, không chuyển trang)
                console.log("Thông báo này chỉ để hiển thị, không có liên kết.");
            }
        },

        formatTime(t) {
            const d = (new Date() - new Date(t)) / 1000;
            if (d < 60) return "Vừa xong";
            if (d < 3600) return Math.floor(d / 60) + " phút trước";
            if (d < 86400) return Math.floor(d / 3600) + " giờ trước";
            return Math.floor(d / 86400) + " ngày trước";
        },
    }
}
</script>

<style scoped>
.sidebar {
    border-right: 1px solid #ddd;
    background: #fff;
}

/* Sidebar giống Instagram */
.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    padding: 10px 14px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    transition: 0.2s;
    color: #222 !important;
}

.sidebar-menu a i {
    font-size: 20px;
}

.sidebar-menu a:hover {
    background: #ffebf4;
    color: #e91e63 !important;
}

.search-result-item:hover {
    background-color: #f1f1f1;
    transition: 0.2s;
}

.search-result-item img {
    border: 1px solid #eee;
}

.notification-item {
    cursor: pointer;
    transition: background 0.3s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

/* Nền tối hơn cho thông báo chưa đọc */
.unread-bg {
    background-color: #f0f7ff; /* Xanh nhạt rất nhẹ */
}

.unread-dot {
    width: 8px;
    height: 8px;
    background-color: #0095f6; /* Màu xanh Instagram */
    border-radius: 50%;
}

.noti-text {
    font-size: 14px;
    line-height: 1.4;
    color: #262626;
}

.cursor-pointer {
    cursor: pointer;
}

.transition-all {
    transition: all 0.2s ease;
}

/* Container chứa icon */
.icon-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Biểu tượng chuông */
.icon-wrapper i {
    font-size: 24px; /* Chỉnh icon to lên một chút cho đẹp */
}

/* Badge số thông báo kiểu Messenger */
.custom-badge {
    position: absolute;
    top: -5px;      /* Đẩy lên phía trên icon */
    right: -8px;    /* Đẩy sang phải icon */
    background-color: #ff3b30; /* Màu đỏ nổi bật */
    color: white;
    font-size: 11px;
    font-weight: bold;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 10px; /* Bo tròn */
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff; /* Viền trắng để tách biệt với icon - giống hệt ảnh bạn gửi */
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Chỉnh lại khoảng cách cho sidebar item */
.sidebar-menu a {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 15px; /* Khoảng cách giữa icon và chữ */
}

.menu-label {
    font-size: 16px;
}
</style>
