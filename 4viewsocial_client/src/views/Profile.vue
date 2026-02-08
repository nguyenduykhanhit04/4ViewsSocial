<template>
  <div class="container-fluid">
    <div class="row">
      <SidebarComponent />

      <main class="col-md-10 profile-main-container">
        <div class="profile-inner-content">
          
          <header class="profile-header">
            <div class="avatar-section">
              <div class="avatar-container">
                <img :src="user_info.avatar_url" @error="handleAvatarError" alt="avatar" />
              </div>
            </div>

            <div class="info-section">
              <div class="info-top-block">
                <h2 class="username-large">{{ user_info.user_name ?? user_info.id }}</h2>
              </div>

              <div class="stats-container">
                <div class="stat-item"><b>{{ stats.count_posts }}</b> bài viết</div>
                <div class="stat-item pointer" @click="openModal('followers')">
                  <b>{{ stats.count_followers }}</b> người theo dõi
                </div>
                <div class="stat-item pointer" @click="openModal('following')">
                  <b>{{ stats.count_following }}</b> đang theo dõi
                </div>
              </div>

              <div class="bio-container">
                <h1 class="full-name">{{ user_info.full_name }}</h1>
                <p class="bio-text" v-if="user_info.bio">{{ user_info.bio }}</p>
              </div>

              <div class="action-buttons-row mt-3">
                <template v-if="isMyProfile">
                  <button class="btn-profile-gray flex-grow-1" @click="$router.push('/edit-profile')">
                    Chỉnh sửa trang cá nhân
                  </button>
                  <button class="btn-profile-gray ms-2">
                    <i class="bi bi-gear-wide"></i>
                  </button>
                </template>

                <template v-else>
                  <button class="btn-profile-gray flex-grow-1">
                    Đang theo dõi <i class="bi bi-chevron-down ms-1"></i>
                  </button>
                  <button class="btn-profile-gray flex-grow-1 ms-2">Nhắn tin</button>
                  <button class="btn-profile-gray ms-2">
                    <i class="bi bi-person-plus"></i>
                  </button>
                </template>
              </div>
            </div>
          </header>

          <nav class="tabs-nav">
              <button class="tab-item" :class="{ active: activeTab === 'posts' }" @click="activeTab = 'posts'">
                  <i class="bi bi-grid-3x3-gap"></i> <span>BÀI VIẾT</span>
              </button>
              
              <button 
                  v-if="isMyProfile" 
                  class="tab-item" 
                  :class="{ active: activeTab === 'saved' }" 
                  @click="activeTab = 'saved'"
              >
                  <i class="bi bi-bookmark"></i> <span>ĐÃ LƯU</span>
              </button>
          </nav>

          <section class="gallery-wrapper">
            <div v-if="activeTab === 'posts'" class="gallery-grid">
              <div class="gallery-item" v-for="post in postsData.list" :key="post.id" @click="openPostDetail(post)">
                <img :src="post.thumbnail_url" alt="post" loading="lazy" />
                <div class="gallery-item-overlay">
                  <span><i class="bi bi-heart-fill"></i> {{ post.total_like || 0 }}</span>
                  <span><i class="bi bi-chat-fill"></i> {{ post.total_comment || 0 }}</span>
                </div>
              </div>
            </div>

            <div v-if="activeTab === 'saved'" class="gallery-grid">
              <div class="gallery-item" v-for="post in savedData.list" :key="'saved-' + post.id" @click="openPostDetail(post)">
                <img :src="post.thumbnail_url" alt="saved post" loading="lazy" />
                <div class="gallery-item-overlay">
                  <span><i class="bi bi-heart-fill"></i> {{ post.total_like || 0 }}</span>
                  <span><i class="bi bi-chat-fill"></i> {{ post.total_comment || 0 }}</span>
                </div>
              </div>
            </div>

            <div v-if="currentLoading" class="loading-area">
              <div class="spinner-border spinner-border-sm text-secondary"></div>
            </div>

            <div v-if="!currentLoading && currentList.length === 0" class="empty-state">
              <i class="bi bi-camera"></i>
              <p>Chưa có nội dung nào</p>
            </div>
          </section>
        </div>
      </main>
    </div>

    <div v-if="isPostDetailOpen" class="post-detail-overlay" @click.self="closePostDetail">
        <div class="post-detail-card" v-if="currentPost">
            
            <div class="media-container">
                <video 
                    v-if="currentPost.thumbnail_url && !isImage(currentPost.thumbnail_url)" 
                    :src="currentPost.thumbnail_url" 
                    controls 
                    autoplay 
                    class="media-full-style"
                ></video>
                
                <img 
                    v-else 
                    :src="currentPost.thumbnail_url || '../assets/logo.png'" 
                    alt="Media" 
                    class="media-full-style"
                />
            </div>
            
            <div class="details-container">
                <div class="details-header d-flex align-items-center">
                    <img :src="user_info.avatar_url" @error="handleAvatarError" class="avatar-xs" />
                    <span class="fw-bold ms-2">{{ user_info.user_name }}</span>
                    <i class="bi bi-x-lg ms-auto pointer fs-4" @click="closePostDetail"></i>
                </div>

                <div class="comments-scroll">
                    <div class="comment-row">
                        <img :src="user_info.avatar_url" class="avatar-xs" />
                        <p><strong>{{ user_info.user_name }}</strong> {{ currentPost.caption }}</p>
                    </div>
                    <hr>
                    <div v-if="loadingComments" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-muted"></div>
                    </div>
                    <div v-for="c in comments" :key="c.id" class="comment-row">
                        <img :src="c.user?.avatar_url || 'https://via.placeholder.com/32'" class="avatar-xs" />
                        <div>
                            <p><strong>{{ c.user?.user_name || c.user?.full_name }}</strong> {{ c.comment }}</p>
                            <div class="comment-meta">{{ formatTime(c.created_at) }}</div>
                        </div>
                    </div>
                </div>

                <div class="details-footer">
                    <div class="actions-row">
                        <i :class="currentPost.isLiked ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'" @click="likePost(currentPost)"></i>
                        <i class="bi bi-chat ms-3"></i>
                        <i :class="currentPost.isSaved ? 'bi bi-bookmark-fill text-warning' : 'bi bi-bookmark'" class="ms-auto" @click="savePost(currentPost)"></i>
                    </div>
                    <div class="likes-count">{{ currentPost.total_like || 0 }} lượt thích</div>
                    <div class="time-stamp">{{ formatTime(currentPost.created_at) }}</div>
                    <div class="comment-input-box mt-2">
                        <input v-model="commentText" @keyup.enter="sendComment" placeholder="Thêm bình luận..." />
                        <button @click="sendComment" :disabled="!commentText.trim()">Đăng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isModalOpen" class="follow-modal-overlay" @click.self="closeModal">
      <div class="follow-modal-card">
        <div class="follow-modal-header">
          <span></span> <span class="fw-bold">{{ modalTitle }}</span>
          <button @click="closeModal" class="border-0 bg-transparent fs-4">&times;</button>
        </div>
        <div class="follow-modal-body" @scroll="handleModalScroll">
          <div v-for="user in displayList" :key="user.id" class="follow-user-row">
            <div class="d-flex align-items-center gap-3 pointer" @click="goToUserProfile(user.id)">
              <img :src="user.avatar_url || 'https://via.placeholder.com/150'" class="avatar-sm rounded-circle" />
              <div class="user-meta">
                <div class="fw-bold" style="font-size: 14px;">{{ user.user_name }}</div>
                <div class="text-muted" style="font-size: 13px;">{{ user.full_name }}</div>
              </div>
            </div>
            <button v-if="user.id !== loggedInUser.id" class="btn-follow-blue">Theo dõi</button>
          </div>
        </div>
      </div>
    </div>

    <ChatWidget />
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, reactive, computed, watch, nextTick } from "vue";
import { useRoute } from "vue-router"; // Import useRoute để lấy ID từ URL
import api from "@/api/client";
import SidebarComponent from "@/components/SidebarComponent.vue";
import ChatWidget from "@/components/ChatWidget.vue";

const route = useRoute();
const loggedInUser = JSON.parse(sessionStorage.getItem("user_info")) || {};

// user_info giờ là ref để có thể thay đổi dữ liệu khi xem profile khác
const user_info = ref({});
const activeTab = ref('posts');

// States Data
const postsData = reactive({ list: [], offset: 0, hasMore: true, loading: false });
const savedData = reactive({ list: [], offset: 0, hasMore: true, loading: false });
const stats = reactive({ count_followers: 0, count_following: 0, count_posts: 0 });

// Biến kiểm tra xem có phải đang xem chính mình không
const isMyProfile = computed(() => {
  return !route.params.id || route.params.id == loggedInUser.id;
});

// Detail Modal States
const isPostDetailOpen = ref(false);
const currentPost = ref(null);
const comments = ref([]);
const commentText = ref("");
const loadingComments = ref(false);

const currentList = computed(() => activeTab.value === 'posts' ? postsData.list : savedData.list);
const currentLoading = computed(() => activeTab.value === 'posts' ? postsData.loading : savedData.loading);

// --- HÀM KHỞI TẠO PROFILE ---
async function initProfile() {
  // Lấy ID từ URL, nếu không có thì lấy ID người đang đăng nhập
  const viewId = route.params.id || loggedInUser.id;

  // Reset data cũ trước khi load mới
  postsData.list = []; postsData.offset = 0; postsData.hasMore = true;
  savedData.list = []; savedData.offset = 0; savedData.hasMore = true;
  activeTab.value = 'posts';

  try {
    // Gọi API lấy thông tin profile (stats + followers + info)
    const res = await api.get("/api/post/get-profile", { params: { user_id: viewId } });
    if (res.data.code === 200) {
      Object.assign(stats, res.data.data);
      // Giả sử API get-profile của bạn trả về thêm thông tin user trong data.user
      // Nếu không, bạn cần lấy từ res.data.data.user_info tùy cấu trúc Backend của bạn
      user_info.value = res.data.data.user || {}; 
    }
    
    // Tải bài viết của user đang xem
    loadUserPosts(viewId);
  } catch (e) {
    console.error("Lỗi init profile:", e);
  }
}

// Theo dõi sự thay đổi của ID trên URL (Khi bấm tìm kiếm user khác lúc đang ở Profile)
watch(() => route.params.id, () => {
  initProfile();
});

// --- LOGIC CHI TIẾT ---
async function openPostDetail(post) {
  currentPost.value = post;
  isPostDetailOpen.value = true;
  document.body.style.overflow = 'hidden';
  loadingComments.value = true;
  try {
    const res = await api.get("/api/post/list-comment", { params: { post_id: post.id } });
    if (res.status === 200) comments.value = res.data.data.comments;
  } finally { loadingComments.value = false; }
}
function closePostDetail() { isPostDetailOpen.value = false; document.body.style.overflow = 'auto'; }

// Hàm gửi comment (nếu bạn chưa hoàn thiện)
async function sendComment() {
  if (!commentText.value.trim()) return;
  try {
    const res = await api.post("/api/post/comment", {
      post_id: currentPost.value.id, 
      user_id: loggedInUser.id, 
      comment: commentText.value
    });
    if (res.status === 200) {
      comments.value.unshift(res.data.data.comment);
      currentPost.value.total_comment++;
      commentText.value = "";
    }
  } catch (error) {
    console.error(error);
  }
}

async function checkAndFillScroll() {
  await nextTick();
  const scrollHeight = document.documentElement.scrollHeight;
  const clientHeight = window.innerHeight;
  if (scrollHeight <= clientHeight + 100) {
    const viewId = route.params.id || loggedInUser.id;
    if (activeTab.value === 'posts' && postsData.hasMore) await loadUserPosts(viewId);
    else if (activeTab.value === 'saved' && savedData.hasMore) await loadSavedPosts(viewId);
  }
}

// --- 1. Tải bài viết của người dùng ---
async function loadUserPosts(id) {
    const viewId = id || route.params.id || loggedInUser.id;
    if (postsData.loading || !postsData.hasMore) return;
    postsData.loading = true;
    try {
        const res = await api.get("/api/post/get-post-user", { 
            params: { 
                user_id: viewId, 
                offset: postsData.offset, 
                limit: 12,
                current_user_id: loggedInUser.id // Gửi ID của mình để Backend check status
            } 
        });
        if (res.data.code === 200) {
            // Mapping để ép kiểu về Boolean chuẩn
            const mappedPosts = res.data.data.map(p => ({
                ...p,
                isLiked: p.isLiked == 1, // Ép kiểu 0/1 thành false/true
                isSaved: p.isSaved == 1 
            }));
            
            postsData.list.push(...mappedPosts);
            postsData.offset += 12;
            if (res.data.data.length < 12) postsData.hasMore = false;
        }
    } finally { postsData.loading = false; }
}

// --- 2. Tải bài viết đã lưu ---
async function loadSavedPosts(id) {
    const viewId = id || route.params.id || loggedInUser.id;
    if (savedData.loading || !savedData.hasMore) return;
    savedData.loading = true;
    try {
        const res = await api.get("/api/post/get-post-saved", { 
            params: { user_id: viewId, offset: savedData.offset, limit: 12 } 
        });
        if (res.data.code === 200) {
            const mappedPosts = res.data.data.map(p => ({
                ...p,
                isLiked: p.isLiked == 1,
                isSaved: true // Ở tab này thì chắc chắn là true
            }));
            savedData.list.push(...mappedPosts);
            savedData.offset += 12;
            if (res.data.data.length < 12) savedData.hasMore = false;
        }
    } finally { savedData.loading = false; }
}

// --- 3. Hàm xử lý Like (Đồng bộ mọi nơi) ---
async function likePost(post) {
    if (!post) return;
    try {
        const res = await api.post('/api/post/like-post', { 
            post_id: post.id, 
            user_id: loggedInUser.id 
        });
        if (res.status === 200) {
            // Cập nhật giá trị mới từ Backend trả về
            const newTotalLike = res.data.data.total_like;
            const newStatus = !post.isLiked;

            // Cập nhật cho bài viết trong Modal
            post.total_like = newTotalLike;
            post.isLiked = newStatus;

            // ĐỒNG BỘ: Cập nhật ở danh sách Grid ngoài màn hình
            [postsData.list, savedData.list].forEach(list => {
                const found = list.find(p => p.id === post.id);
                if (found) {
                    found.isLiked = newStatus;
                    found.total_like = newTotalLike;
                }
            });
        }
    } catch (err) { console.error("Lỗi like:", err); }
}

// --- 4. Hàm xử lý Save (Đồng bộ mọi nơi) ---
async function savePost(post) {
    if (!post) return;
    try {
        const res = await api.post('/api/post/save-post', { 
            post_id: post.id, 
            user_id: loggedInUser.id 
        });
        if (res.status === 200) {
            const isSavedResult = res.data.data.saved == 1;
            post.isSaved = isSavedResult;

            // Cập nhật Grid ngoài màn hình
            const found = postsData.list.find(p => p.id === post.id);
            if (found) found.isSaved = isSavedResult;

            // Đặc biệt: Nếu đang ở tab Saved mà bỏ lưu, xóa bài viết khỏi danh sách luôn
            if (activeTab.value === 'saved' && !isSavedResult) {
                savedData.list = savedData.list.filter(p => p.id !== post.id);
            } else {
                const foundInSaved = savedData.list.find(p => p.id === post.id);
                if (foundInSaved) foundInSaved.isSaved = isSavedResult;
            }
        }
    } catch (err) { console.error("Lỗi save:", err); }
}

// Logic Modal Followers
const isModalOpen = ref(false);
const modalTitle = ref("");
const displayList = ref([]);
const modalOffset = ref(0);
const modalHasMore = ref(true);

const openModal = (mode) => {
  isModalOpen.value = true;
  modalTitle.value = mode === 'followers' ? 'Người theo dõi' : 'Đang theo dõi';
  displayList.value = []; modalOffset.value = 0; modalHasMore.value = true;
  loadModalListData(mode);
};
const closeModal = () => isModalOpen.value = false;

async function loadModalListData(mode) {
  const viewId = route.params.id || loggedInUser.id;
  try {
    const res = await api.get("/api/post/get-profile", { params: { user_id: viewId, offset: modalOffset.value, limit: 20 } });
    const users = mode === 'followers' ? res.data.data.followers : res.data.data.following;
    displayList.value.push(...users);
    modalOffset.value += 20;
    if (users.length < 20) modalHasMore.value = false;
  } catch (e) { console.error(e); }
}

const handleModalScroll = (e) => {
  const { scrollTop, scrollHeight, clientHeight } = e.target;
  // Nếu cuộn gần đến đáy và vẫn còn dữ liệu thì load tiếp
  if (scrollTop + clientHeight >= scrollHeight - 5 && modalHasMore.value) {
    // Xác định mode hiện tại dựa trên modalTitle
    const mode = modalTitle.value === 'Người theo dõi' ? 'followers' : 'following';
    loadModalListData(mode);
  }
};

// Nên thêm hàm chuyển hướng khi click vào user trong modal
const goToUserProfile = (userId) => {
  isModalOpen.value = false; // Đóng modal
  document.body.style.overflow = 'auto'; // Trả lại scroll cho body
  router.push(`/profile/${userId}`);
};

const handleAvatarError = (e) => e.target.src = new URL('@/assets/avtgd2.jpg', import.meta.url).href;
const isImage = (url) => url && /\.(jpg|jpeg|png|webp|gif|bmp)$/i.test(url);
const formatTime = (t) => {
  const d = (new Date() - new Date(t)) / 1000;
  if (d < 60) return "Vừa xong";
  if (d < 3600) return Math.floor(d / 60) + " phút";
  if (d < 86400) return Math.floor(d / 3600) + " giờ";
  return Math.floor(d / 86400) + " ngày";
};

// Theo dõi tab để load dữ liệu
watch(activeTab, () => { 
    if (currentList.value.length === 0) {
        const viewId = route.params.id || loggedInUser.id;
        activeTab.value === 'posts' ? loadUserPosts(viewId) : loadSavedPosts(viewId); 
    }
});

onMounted(() => {
  initProfile();
  window.addEventListener("scroll", () => {
    if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 150) {
      const viewId = route.params.id || loggedInUser.id;
      activeTab.value === 'posts' ? loadUserPosts(viewId) : loadSavedPosts(viewId);
    }
  });
});
</script>

<style scoped>
/* ======== LAYOUT TỔNG QUÁT ======== */
.profile-main-container {
  background: #fff;
  min-height: 100vh;
  padding: 0;
  display: flex;
  justify-content: center;
}

.profile-inner-content {
  width: 100%;
  max-width: 935px; /* Chiều rộng chuẩn Instagram */
  padding: 30px 20px;
}

/* ======== PROFILE HEADER ======== */
.profile-header {
  display: flex;
  margin-bottom: 44px;
  gap: 40px;
  align-items: flex-start;
}

.avatar-section {
  flex: 1;
  display: flex;
  justify-content: center;
}

.avatar-container img {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid #efefef;
  padding: 3px;
  transition: transform 0.3s ease;
}

.avatar-container img:hover {
  transform: scale(1.02);
}

.info-section {
  flex: 2;
  display: flex;
  flex-direction: column;
}

/* Username Style */
.username-large {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 15px;
  color: #262626;
  letter-spacing: -0.5px;
}

/* Stats (Bài viết, Followers...) */
.stats-container {
  display: flex;
  gap: 30px;
  margin-bottom: 20px;
  font-size: 16px;
  color: #262626;
}

.stat-item b {
  font-weight: 700;
}

/* Bio Section */
.bio-container {
  line-height: 1.4;
  margin-bottom: 10px;
}

.full-name {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 4px 0;
  color: #262626;
}

.bio-text {
  font-size: 15px;
  white-space: pre-wrap;
  color: #262626;
  margin: 0;
}

/* Action Buttons (Hàng nút bấm xám) */
.action-buttons-row {
  display: flex;
  align-items: center;
  width: 100%;
}

.btn-profile-gray {
  background-color: #efefef;
  border: none;
  color: #000;
  font-weight: 600;
  font-size: 14px;
  padding: 8px 16px;
  border-radius: 8px;
  transition: background 0.2s, opacity 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 35px;
}

.btn-profile-gray:hover {
  background-color: #dbdbdb;
}

.btn-profile-gray:active {
  opacity: 0.7;
}

.flex-grow-1 {
  flex: 1;
}

/* ======== TABS NAVIGATION ======== */
.tabs-nav {
  border-top: 1px solid #dbdbdb;
  display: flex;
  justify-content: center;
  gap: 60px;
  margin-top: 20px;
}

.tab-item {
  border: none;
  background: none;
  padding: 15px 0;
  font-size: 12px;
  font-weight: 600;
  color: #8e8e8e;
  letter-spacing: 1px;
  border-top: 1px solid transparent;
  margin-top: -1px;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: color 0.2s;
}

.tab-item.active {
  color: #000;
  border-top-color: #262626;
}

.tab-item i {
  font-size: 14px;
}

/* ======== GALLERY GRID (3 Cột ảnh to) ======== */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* 4 cột giúp ảnh to và rõ */
  gap: 4px;
}

.gallery-item {
  position: relative;
  aspect-ratio: 1/1;
  cursor: pointer;
  background: #fafafa;
  overflow: hidden;
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: filter 0.3s ease;
}

.gallery-item-overlay {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.3);
  opacity: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 25px;
  color: #fff;
  font-weight: 700;
  font-size: 18px;
  transition: opacity 0.2s ease;
}

.gallery-item:hover .gallery-item-overlay {
  opacity: 1;
}

/* ======== POST DETAIL MODAL ======== */
.post-detail-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.8);
  z-index: 10000;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px;
}

.post-detail-card {
  background: #fff;
  display: flex;
  width: 100%;
  max-width: 1100px;
  height: 90vh;
  border-radius: 4px;
  overflow: hidden;
}

.media-container { 
  background: #000; 
  flex: 1.5; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
}

.media-container img, .media-container video { 
  max-width: 100%; 
  max-height: 100%; 
  object-fit: contain; 
}

.details-container { 
  flex: 1; 
  display: flex; 
  flex-direction: column; 
  background: #fff; 
  border-left: 1px solid #efefef; 
}

.details-header { 
  padding: 14px; 
  border-bottom: 1px solid #efefef; 
  display: flex; 
  align-items: center; 
  gap: 12px; 
}

.avatar-xs { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }

.comments-scroll { 
  flex: 1; 
  overflow-y: auto; 
  padding: 16px; 
}

.comment-row { display: flex; gap: 12px; margin-bottom: 16px; font-size: 14px; }

.details-footer { 
  padding: 14px; 
  border-top: 1px solid #efefef; 
}

.actions-row { display: flex; gap: 16px; font-size: 24px; margin-bottom: 8px; }

.comment-input-box { 
  border-top: 1px solid #efefef; 
  padding: 12px 0 0; 
  display: flex; 
  align-items: center;
}

.comment-input-box input { 
  flex: 1; 
  border: none; 
  outline: none; 
  font-size: 14px; 
  padding: 5px;
}

/* ======== UTILITIES ======== */
.pointer { cursor: pointer; }

.loading-area { 
  text-align: center; 
  padding: 40px 0; 
}

.empty-state { 
  text-align: center; 
  padding: 80px 0; 
  color: #8e8e8e; 
}

.empty-state i {
  font-size: 60px;
  margin-bottom: 15px;
}

/* ======== FOLLOW MODAL ======== */
.follow-modal-card {
  background: #fff;
  width: 400px;
  border-radius: 12px;
  max-height: 400px;
  display: flex;
  flex-direction: column;
}

.follow-modal-header {
  padding: 12px;
  border-bottom: 1px solid #dbdbdb;
  text-align: center;
  position: relative;
}

.follow-user-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
}

.avatar-sm { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }

.btn-follow-blue {
  background: #0095f6;
  color: #fff;
  border: none;
  padding: 6px 16px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
}

.follow-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.65); /* Làm tối nền */
  z-index: 99999; /* Đảm bảo hiện trên cùng */
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Tinh chỉnh lại card cho đẹp giống mẫu bạn gửi */
.follow-modal-card {
  background: #fff;
  width: 400px;
  border-radius: 12px;
  max-height: 500px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.follow-modal-body {
  overflow-y: auto;
  flex: 1;
}

.text-danger { color: #ed4956 !important; }
.text-warning { color: #ffc107 !important; }

/* Modal Content fixes */
.media-container {
    background-color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
}

.details-header .bi-x-lg {
    cursor: pointer;
    padding: 5px;
    transition: 0.2s;
}

.details-header .bi-x-lg:hover {
    background-color: #f1f1f1;
    border-radius: 50%;
}

.actions-row i {
    font-size: 24px;
    cursor: pointer;
}

.ms-3 { margin-left: 1rem !important; }
</style>