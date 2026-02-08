<template>
  <div class="col-md-12">
    <div class="row g-0">
      <SidebarComponent />

      <div class="col-md-10 pt-5 explore-container">
        <div class="explore-wrapper">
          
          <div v-if="loading" class="text-center mt-5">
            <div class="spinner-border text-secondary" role="status"></div>
          </div>

          <div v-else class="explore-grid">
            <div 
              class="explore-item" 
              v-for="item in exploreList" 
              :key="item.id"
              @click="openComment(item)"
              data-bs-toggle="offcanvas" 
              data-bs-target="#commentPanel"
            >
              <video 
                v-if="item.thumbnail_url && !isImage(item.thumbnail_url)" 
                :src="item.thumbnail_url" 
                class="explore-img"
              ></video>
              
              <img 
                v-else 
                :src="item.thumbnail_url || '../assets/logo.png'" 
                class="explore-img" 
                alt="explore" 
              />

              <div v-if="!isImage(item.thumbnail_url)" class="video-icon-badge">
                <i class="bi bi-play-btn-fill"></i> 
              </div>

              <div class="explore-overlay">
                <span>
                  <i class="bi bi-heart-fill"></i> {{ item.total_like }}
                </span>
                <span>
                  <i class="bi bi-chat-fill"></i> {{ item.total_comment }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="offcanvas offcanvas-bottom offcanvas-comment" tabindex="-1" id="commentPanel">
      
      <div class="header-comment position-relative text-center py-2 d-md-none border-bottom">
        <h6 class="m-0 fw-bold">Bình luận</h6>
        <button 
          class="btn-close position-absolute top-50 end-0 translate-middle-y me-3" 
          data-bs-dismiss="offcanvas"
        ></button>
      </div>
      
      <div class="comment-wrapper" v-if="currentPost">
        <div class="comment-left d-none d-md-flex">
          <video 
            v-if="currentPost.thumbnail_url && !isImage(currentPost.thumbnail_url)" 
            :src="currentPost.thumbnail_url" 
            controls 
            class="media-full"
          ></video>
          <img 
            v-else 
            :src="currentPost.thumbnail_url || '../assets/logo.png'" 
            alt="Media" 
            class="media-full"
          />
        </div>

        <div class="comment-right d-flex flex-column h-100 position-relative">
          
          <div class="d-none d-md-flex align-items-center justify-content-center p-3 border-bottom position-relative">
            <h6 class="m-0 fw-bold">Bình luận</h6>
            <button class="btn-close position-absolute end-0 me-3" data-bs-dismiss="offcanvas"></button>
          </div>

          <div class="comment-list flex-grow-1 overflow-auto p-3">
            <div v-if="loadingComment" class="text-center py-3 text-muted">
              Đang tải bình luận...
            </div>
            
            <div v-for="c in comments" :key="c.id" class="d-flex gap-2 mb-3">
              <div class="avatar-box-small">
                <img :src="c.user?.avatar_url || '../assets/logo.png'" />
              </div>
              <div class="comment-content">
                <p class="m-0 text-sm">
                  <strong class="me-1">{{ c.user?.full_name }}</strong> 
                  {{ c.comment }}
                </p>
                <small class="text-muted time-label">{{ formatTime(c.created_at) }}</small>
              </div>
            </div>
          </div>

          <div class="comment-footer border-top p-3 bg-white">
            <div class="post-actions d-flex align-items-center mb-2">
              <button class="action-btn-modal me-3" @click="likePost(currentPost)">
                <i :class="currentPost.isLiked ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'"></i>
              </button>
              
              <button class="action-btn-modal ms-auto" @click="savePost(currentPost)">
                <i :class="currentPost.isSaved ? 'bi bi-bookmark-fill text-warning' : 'bi bi-bookmark'"></i>
              </button>
            </div>
            <div class="fw-bold mb-2">{{ currentPost.total_like }} lượt thích</div>
            <div class="mb-2">
              <span class="fw-bold me-1">{{ currentPost.author_fullname }}</span>
              <span class="caption-text">{{ currentPost.caption }}</span>
            </div>
            <small class="text-muted d-block mb-3 time-label">
              {{ formatTime(currentPost.created_at) }}
            </small>

            <div class="input-group">
              <input 
                v-model="commentText" 
                @keyup.enter="sendComment" 
                type="text" 
                class="form-control border-0 bg-light-soft" 
                placeholder="Thêm bình luận..."
              >
              <button class="btn text-primary fw-bold" @click="sendComment">Gửi</button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <ChatWidget />
  </div>
</template>

<script>
import SidebarComponent from "@/components/SidebarComponent.vue";
import ChatWidget from "@/components/ChatWidget.vue";
import api from "../api/client";

export default {
  components: { SidebarComponent, ChatWidget },
  data() {
    return {
      exploreList: [],
      loading: false,
      currentUser: null,
      currentPost: null,
      comments: [],
      commentText: "",
      loadingComment: false,
    };
  },
  mounted() {
    const user = JSON.parse(sessionStorage.getItem('user_info'));
    if (user) { this.currentUser = user; }
    this.fetchExploreData();
  },
  methods: {
    async fetchExploreData() {
      this.loading = true;
      try {
        const res = await api.get('/api/post/explore', {
          params: { user_id: this.currentUser?.id }
        });
        if (res.status === 200) {
          this.exploreList = res.data.data.listPostEplore || res.data.data;
        }
      } catch (error) {
        console.error('Lỗi khi tải khám phá:', error);
      } finally {
        this.loading = false;
      }
    },

    async openComment(post) {
      this.currentPost = post;
      this.comments = [];
      this.loadingComment = true;
      try {
        const res = await api.get("/api/post/list-comment", { 
          params: { post_id: post.id } 
        });
        if (res.status === 200) {
          this.comments = res.data.data.comments;
        }
      } catch (err) {
        console.error(err);
      } finally {
        this.loadingComment = false;
      }
    },

    async likePost(post) {
      if (!post) return;
      try {
        const res = await api.post('/api/post/like-post', { 
          post_id: post.id, 
          user_id: this.currentUser.id 
        });
        if (res.status === 200) {
          // Cập nhật dữ liệu trực tiếp vào object post (currentPost)
          post.total_like = res.data.data.total_like;
          post.isLiked = !post.isLiked;
          
          // Cập nhật lại trạng thái trong danh sách Explore ngoài màn hình
          const found = this.exploreList.find(p => p.id === post.id);
          if (found) {
            found.isLiked = post.isLiked;
            found.total_like = post.total_like;
          }
        }
      } catch (err) {
        console.error("Lỗi like bài viết:", err);
      }
    },

    async savePost(post) {
      if (!post) return;
      try {
        const res = await api.post('/api/post/save-post', { 
          post_id: post.id, 
          user_id: this.currentUser.id 
        });
        if (res.status === 200) {
          post.isSaved = res.data.data.saved;
          
          // Cập nhật lại danh sách Explore ngoài màn hình
          const found = this.exploreList.find(p => p.id === post.id);
          if (found) found.isSaved = post.isSaved;
        }
      } catch (err) {
        console.error("Lỗi save bài viết:", err);
      }
    },

    async sendComment() {
      if (!this.commentText.trim()) return;
      try {
        const res = await api.post("/api/post/comment", {
          post_id: this.currentPost.id, 
          user_id: this.currentUser.id, 
          comment: this.commentText
        });
        if (res.status === 200) {
          this.comments.unshift(res.data.data.comment);
          this.currentPost.total_comment++;
          this.commentText = "";
        }
      } catch (error) {
        console.error(error);
      }
    },

    isImage(url) {
      if (!url) return true;
      return /\.(jpg|jpeg|png|webp|gif|bmp)$/i.test(url);
    },

    formatTime(t) {
      const d = (new Date() - new Date(t)) / 1000;
      if (d < 60) return "Vừa xong";
      if (d < 3600) return Math.floor(d / 60) + " phút trước";
      if (d < 86400) return Math.floor(d / 3600) + " giờ trước";
      return Math.floor(d / 86400) + " ngày trước";
    }
  },
};
</script>

<style scoped>
/* Explore Grid Layout */
.explore-container { 
  background: #fafafa; 
  min-height: 100vh; 
  display: flex; 
  justify-content: center; 
}

.explore-wrapper { 
  max-width: 935px; 
  width: 100%; 
  margin: 0 auto; 
  padding: 0 10px; 
}

.explore-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
  gap: 4px; 
}

.explore-item { 
  position: relative; 
  width: 100%; 
  aspect-ratio: 4/5; /* Instagram style chuẩn thường là vuông */
  overflow: hidden; 
  background-color: #efefef; 
  cursor: pointer; 
}

.video-icon-badge {
  position: absolute; 
  top: 12px; 
  right: 12px; 
  color: white;
  font-size: 1.8rem; 
  z-index: 2; 
  text-shadow: 0px 0px 8px rgba(0, 0, 0, 0.6); 
  pointer-events: none;
}

.explore-img { 
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
}

.explore-overlay {
  position: absolute; 
  inset: 0; 
  background: rgba(0, 0, 0, 0.3);
  display: flex; 
  justify-content: center; 
  align-items: center; 
  gap: 20px;
  color: white; 
  font-weight: bold; 
  opacity: 0; 
  transition: opacity 0.2s ease; 
  z-index: 3;
}

.explore-item:hover .explore-overlay { 
  opacity: 1; 
}

/* Offcanvas / Modal Popup Style */
.offcanvas-comment { 
  max-width: 100%; 
}

.comment-wrapper { 
  display: flex; 
  height: 100%; 
}

.comment-left { 
  flex: 1.2; 
  background: #000; 
  display: flex;
  align-items: center; 
  justify-content: center; 
  overflow: hidden; 
}

.media-full {
  max-width: 100%; 
  max-height: 100%; 
  object-fit: contain;
}

.comment-right { 
  flex: 1; 
  min-width: 350px; 
  background: #fff; 
}

.avatar-box-small {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.avatar-box-small img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.time-label {
  font-size: 0.75rem;
}

.bg-light-soft {
  background: transparent;
}

.action-btn-modal {
  background: none;
  border: none;
  padding: 0;
  font-size: 1.5rem; /* Kích thước icon tim trong modal */
  cursor: pointer;
  color: #262626;
  transition: transform 0.1s ease;
}

.action-btn-modal:active {
  transform: scale(1.2); /* Hiệu ứng nhấn nhẹ */
}

/* Đảm bảo icon tim có màu đỏ khi đã like */
.text-danger {
  color: #ed4956 !important;
}

/* Màu vàng cho Bookmark */
.text-warning {
  color: #ffc107 !important;
}

/* Desktop Popup Logic */
@media (min-width: 768px) {
  .offcanvas-comment {
    position: fixed; 
    top: 50% !important; 
    left: 50% !important; 
    bottom: auto !important; 
    right: auto !important;
    width: 70vw !important; 
    max-width: 1100px; 
    height: 85vh !important;
    border-radius: 8px; 
    border: none; 
    box-shadow: 0 0 50px rgba(0,0,0,0.5);
    transform: translate(-50%, -50%) scale(0.9); 
    opacity: 0; 
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  
  .offcanvas-comment.show { 
    transform: translate(-50%, -50%) scale(1) !important; 
    opacity: 1; 
    visibility: visible; 
  }
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .comment-wrapper { 
    flex-direction: column; 
  }
  .comment-left { 
    display: none !important; 
  }
  .explore-grid { 
    grid-template-columns: repeat(3, 1fr); 
    gap: 1px; 
  }
}
</style>