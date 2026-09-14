<template>
    <div class="sidebar-wrapper">
        <div class="sidebar-container">
            <!-- Brand Logo -->
            <div class="sidebar-brand mb-4">
                <a href="/homepage" class="brand-link">
                    <div class="brand-logo-mark">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="6" ry="6"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </div>
                    <span class="brand-text">4Views</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="sidebar-nav">
                <!-- Trang chu -->
                <a href="/homepage" class="nav-item" :class="{ 'active': $route.path === '/homepage' }">
                    <span class="nav-icon-box">
                        <svg v-if="$route.path === '/homepage'" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.707 2.293a1 1 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13h1v7a2 2 0 0 0 2 2h4a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h4a2 2 0 0 0 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9z"/>
                        </svg>
                        <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </span>
                    <span class="nav-label">Trang chủ</span>
                </a>

                <!-- Tim kiem -->
                <a class="nav-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch">
                    <span class="nav-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <span class="nav-label">Tìm kiếm</span>
                </a>

                <!-- Kham pha -->
                <a href="/explore" class="nav-item" :class="{ 'active': $route.path === '/explore' }">
                    <span class="nav-icon-box">
                        <svg v-if="$route.path === '/explore'" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"></circle>
                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                        </svg>
                        <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                        </svg>
                    </span>
                    <span class="nav-label">Khám phá</span>
                </a>

                <!-- Tin nhan -->
                <a href="/message" class="nav-item" :class="{ 'active': $route.path === '/message' }">
                    <span class="nav-icon-box">
                        <svg v-if="$route.path === '/message'" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                        <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Tin nhắn</span>
                </a>

                <!-- Thong bao -->
                <a class="nav-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNotify" @click="fetchNotifications">
                    <div class="icon-badge-wrapper">
                        <span class="nav-icon-box">
                            <svg v-if="unreadCount > 0" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </span>
                        <span v-if="unreadCount > 0" class="nav-badge">
                            {{ unreadCount > 9 ? '9+' : unreadCount }}
                        </span>
                    </div>
                    <span class="nav-label">Thông báo</span>
                </a>

                <!-- Tao bai viet -->
                <a href="#" class="nav-item create-post-nav" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <span class="nav-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="4" ry="4"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </span>
                    <span class="nav-label">Tạo bài viết</span>
                </a>

                <!-- Trang ca nhan -->
                <a :href="currentUser ? `/profile/${currentUser.id}` : '/profile'" class="nav-item user-profile-nav" :class="{ 'active': $route.path.startsWith('/profile') }">
                    <img :src="currentUser?.avatar_url || getAvatarUrl(currentUser?.user_name || 'User')" class="nav-avatar" />
                    <span class="nav-label">Trang cá nhân</span>
                </a>
            </nav>

            <!-- Bottom User Card / Logout -->
            <div class="sidebar-bottom mt-auto" v-if="currentUser">
                <div class="user-mini-card">
                    <img :src="currentUser.avatar_url || getAvatarUrl(currentUser.user_name)" class="mini-avatar" />
                    <div class="user-mini-info">
                        <span class="user-mini-name">{{ currentUser.full_name || currentUser.user_name }}</span>
                        <span class="user-mini-handle">@{{ currentUser.user_name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Thông báo -->
    <div class="offcanvas offcanvas-start modern-offcanvas" tabindex="-1" id="offcanvasNotify">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2 m-0 fs-5">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="text-pink">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                Thông báo
            </h5>
            <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close" @click="closeOffcanvas('offcanvasNotify')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="offcanvas-body p-0">
            <div v-if="unreadCount > 0" class="d-flex justify-content-between align-items-center px-4 py-2 bg-pink-light border-bottom">
                <span class="text-pink-dark small font-semibold">Bạn có {{ unreadCount }} thông báo mới</span>
                <button class="btn btn-sm btn-link text-decoration-none p-0 text-pink fw-semibold" @click="markNotificationsRead">
                    Đánh dấu đã đọc
                </button>
            </div>

            <div v-if="notifications.length === 0" class="text-center py-5 text-muted">
                <div class="empty-noti-icon mb-3">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f472b6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                <p class="m-0 text-secondary">Chưa có thông báo nào</p>
            </div>

            <div 
                v-for="noti in notifications" 
                :key="noti.id" 
                class="notification-card d-flex align-items-center gap-3 px-4 py-3 border-bottom"
                :class="{ 'unread-card': noti.is_view === 0 }"
                @click="handleNotificationClick(noti)"
            >
                <div class="position-relative">
                    <img :src="noti.actor?.avatar_url || getAvatarUrl(noti.actor?.full_name || noti.actor?.user_name || 'User')" class="rounded-circle shadow-sm" width="44" height="44" style="object-fit: cover;" />
                    <span v-if="noti.type == 1" class="noti-badge-type bg-pink">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#fff"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </span>
                    <span v-else-if="noti.type == 2" class="noti-badge-type bg-purple">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#fff"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    </span>
                    <span v-else class="noti-badge-type bg-rose">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#fff"><path d="M12 2a5 5 0 1 0 5 5 5 5 0 0 0-5-5zm0 12c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5z"/></svg>
                    </span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="noti-text text-truncate">
                        <strong class="text-dark">{{ noti.actor?.full_name || noti.actor?.user_name }}</strong> 
                        {{ noti.content }}
                    </div>
                    <small class="text-muted">{{ formatTime(noti.created_at) }}</small>
                </div>
                <div v-if="noti.is_view === 0" class="unread-pulse-dot"></div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Tìm kiếm -->
    <div class="offcanvas offcanvas-start modern-offcanvas" tabindex="-1" id="offcanvasSearch">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2 m-0 fs-5 text-pink">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Tìm kiếm
            </h5>
            <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close" @click="closeOffcanvas('offcanvasSearch')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="offcanvas-body p-4">
            <div class="search-input-wrapper mb-3">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input 
                    type="text" 
                    class="form-control modern-search-input" 
                    placeholder="Tìm kiếm tài khoản, bạn bè..." 
                    v-model="searchKeyword"
                    @input="handleSearch"
                    ref="searchInputRef"
                />
                <button v-if="searchKeyword" class="btn-clear-search" @click="searchKeyword = ''; searchResults = [];">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div v-if="searchResults.length > 0" class="search-results-list">
                <div 
                    class="d-flex align-items-center p-2 modern-search-item" 
                    v-for="user in searchResults" 
                    :key="user.id"
                    @click="goToProfile(user.id)"
                >
                    <img :src="user.avatar_url || getAvatarUrl(user.user_name)" class="rounded-circle me-3 shadow-sm" width="44" height="44" style="object-fit: cover;" />
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold text-truncate text-dark" style="font-size: 14px;">{{ user.full_name || user.user_name }}</div>
                        <div class="text-muted small text-truncate">@{{ user.user_name }}</div>
                    </div>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </div>

            <div v-else-if="searchKeyword.length > 0" class="text-center text-muted py-5">
                <div class="empty-search-icon mb-2">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#f472b6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                </div>
                <span>Không tìm thấy người dùng phù hợp.</span>
            </div>
            <div v-else class="text-center text-muted py-5 small">
                Nhập tên hoặc username để bắt đầu tìm kiếm
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
        getAvatarUrl(name) {
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'User')}&background=db2777&color=fff&bold=true&rounded=true`;
        },

        closeOffcanvas(elementId) {
            const offcanvasElement = document.getElementById(elementId);
            if (offcanvasElement && window.bootstrap) {
                const instance = window.bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (instance) instance.hide();
            }
        },

        goToProfile(userId) {
            this.closeOffcanvas('offcanvasSearch');
            this.searchKeyword = '';
            this.searchResults = [];
            this.$router.push(`/profile/${userId}`);
        },

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
                        this.searchResults = res.data.data.users || [];
                    }
                } catch (err) {
                    console.error("Lỗi tìm kiếm:", err);
                    this.searchResults = [];
                }
            }, 300);
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
                    this.notifications = res.data.data.notifications || [];
                    this.unreadCount = res.data.data.unread_count || 0;
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
            if (noti.is_view === 0) {
                try {
                    await api.post(`/api/post/notifications/mark-read/${noti.id}`);
                    noti.is_view = 1;
                    if (this.unreadCount > 0) this.unreadCount--;
                } catch (err) {
                    console.error("Lỗi cập nhật thông báo:", err);
                }
            }

            if (noti.type === 0 && noti.actor_id) { 
                this.closeOffcanvas('offcanvasNotify');
                this.$router.push(`/profile/${noti.actor_id}`);
            }
        },

        formatTime(t) {
            if (!t) return "";
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
.sidebar-wrapper {
    width: 250px;
    height: 100vh;
    position: sticky;
    top: 0;
    left: 0;
    background: #ffffff;
    border-right: 1px solid #fce7f3;
    z-index: 100;
    flex-shrink: 0;
}

.sidebar-container {
    height: 100%;
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
}

/* Brand */
.brand-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    padding: 6px 10px;
}

.brand-logo-mark {
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #f43f5e, #ec4899 50%, #db2777 100%);
    color: #fff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(236, 72, 153, 0.35);
}

.brand-text {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.6px;
    background: linear-gradient(135deg, #ec4899, #db2777);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Navigation Links */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 14px;
    border-radius: 12px;
    text-decoration: none;
    color: #475569;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.nav-icon-box {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    color: #475569;
    transition: transform 0.15s ease, color 0.15s ease;
}

.nav-item:hover {
    background: #fdf2f8;
    color: #db2777;
}

.nav-item:hover .nav-icon-box {
    transform: scale(1.1);
    color: #db2777;
}

.nav-item.active {
    font-weight: 700;
    color: #db2777;
    background: #fdf2f8;
}

.nav-item.active .nav-icon-box {
    color: #db2777;
}

.create-post-nav {
    margin-top: 6px;
    background: linear-gradient(135deg, #fdf2f8, #fce7f3);
    color: #db2777;
    font-weight: 600;
}

.create-post-nav .nav-icon-box {
    color: #db2777;
}

.create-post-nav:hover {
    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
    color: #be185d;
}

.nav-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #db2777;
}

/* Badge */
.icon-badge-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.nav-badge {
    position: absolute;
    top: -5px;
    right: -7px;
    background: linear-gradient(135deg, #f43f5e, #db2777);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(219, 39, 119, 0.4);
}

/* Bottom User Mini Card */
.user-mini-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    background: #fdf2f8;
    border: 1px solid #fce7f3;
}

.mini-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid #ec4899;
}

.user-mini-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.user-mini-name {
    font-size: 13px;
    font-weight: 700;
    color: #831843;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

.user-mini-handle {
    font-size: 11px;
    color: #db2777;
    opacity: 0.8;
}

/* Offcanvas Styles */
.modern-offcanvas {
    width: 380px !important;
    border-radius: 0 20px 20px 0;
    box-shadow: 6px 0 30px rgba(219, 39, 119, 0.08);
    border: none;
    border-right: 1px solid #fce7f3;
}

.text-pink {
    color: #db2777 !important;
}

.text-pink-dark {
    color: #9d174d !important;
}

.bg-pink-light {
    background: #fdf2f8 !important;
}

.btn-close-custom {
    background: transparent;
    border: none;
    padding: 6px;
    border-radius: 8px;
    color: #9d174d;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-close-custom:hover {
    background: #fce7f3;
    color: #be185d;
}

.notification-card {
    cursor: pointer;
    transition: background 0.15s;
}

.notification-card:hover {
    background: #fdf2f8;
}

.unread-card {
    background: #fff1f2;
}

.unread-pulse-dot {
    width: 8px;
    height: 8px;
    background: #ec4899;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.25);
    flex-shrink: 0;
}

.noti-badge-type {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
}

.bg-pink {
    background: #ec4899;
}

.bg-purple {
    background: #a855f7;
}

.bg-rose {
    background: #f43f5e;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 14px;
    color: #ec4899;
    pointer-events: none;
}

.modern-search-input {
    padding-left: 42px;
    padding-right: 36px;
    height: 44px;
    border-radius: 12px;
    background: #fdf2f8;
    border: 1px solid #fbcfe8;
    font-size: 14px;
    color: #831843;
}

.modern-search-input:focus {
    background: #fff;
    border-color: #ec4899;
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
}

.btn-clear-search {
    position: absolute;
    right: 12px;
    background: transparent;
    border: none;
    color: #ec4899;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.modern-search-item {
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.15s;
}

.modern-search-item:hover {
    background: #fdf2f8;
}
</style>
