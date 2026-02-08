<template>
    <div class="col-md-12">
        <div class="row">
            <SidebarComponent />

            <div class="col-md-6">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <div class="story-wrapper">
                                <div class="story-scroll-container">
                                    <div class="story-item-group">
                                        <div class="story-circle add-story" @click="openStoryModal">
                                            <i class="bi bi-plus-lg"></i>
                                        </div>
                                        <span class="story-username">Tin của bạn</span>
                                    </div>

                                    <div class="story-item-group" v-for="(group, index) in groupedStories"
                                        :key="group.user_id" @click="openStoryViewer(index)">
                                        <div class="story-circle has-story-gradient">
                                            <div class="story-circle-inner">
                                                <img :src="group.avatar_url || '../assets/logo.png'" alt="Avatar" />
                                            </div>
                                        </div>
                                        <span class="story-username">{{ group.user_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Post List -->
                            <div class="post mt-4" v-for="post in list_posts" :key="post.id">
                                <div class="post-header">
                                    <div class="avatar-box">
                                        <img :src="post.author_avatar || '../assets/logo.png'" alt="Avatar" />
                                    </div>
                                    <strong>{{ post.author_fullname }}</strong>
                                    <span class="text-muted ms-1" style="font-weight: lighter;">
                                        - {{ formatTime(post.created_at) }}
                                    </span>
                                </div>

                                <div class="post-media-container" style="cursor: pointer;" data-bs-toggle="offcanvas"
                                    data-bs-target="#commentPanel" @click="openComment(post)">

                                    <video v-if="post.thumbnail_url && !isImage(post.thumbnail_url)"
                                        :src="post.thumbnail_url" class="post-image" controls style="background: #000;">
                                    </video>

                                    <img v-else :src="post.thumbnail_url || '../assets/logo.png'" class="post-image"
                                        alt="Thumbnail" />
                                </div>

                                <div class="post-actions">
                                    <button class="action-btn ms-2" @click="likePost(post)">
                                        <i :class="post.isLiked ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'"></i>
                                    </button>

                                    <button class="action-btn" data-bs-toggle="offcanvas" data-bs-target="#commentPanel"
                                        @click="openComment(post)">
                                        <i class="bi bi-chat-left-text"></i> <span style="font-size: 1.2rem;">{{
                                            post.total_comment }}</span>
                                    </button>

                                    <button class="action-btn ms-auto me-2" @click="savePost(post)">
                                        <i
                                            :class="post.isSaved ? 'bi bi-bookmark-fill text-warning' : 'bi bi-bookmark'"></i>
                                    </button>
                                </div>

                                <div class="box-detail ms-3 mb-3 me-3">
                                    <div class="post-caption fw-bold">
                                        {{ post.total_like }} lượt thích
                                    </div>
                                    <div class="post-caption mt-2">
                                        <label class="fw-bold">{{ post.author_fullname }}</label>
                                        {{ post.caption }}
                                    </div>
                                    <div class="post-caption mt-2 text-muted"
                                        style="font-weight: lighter; cursor: pointer;" data-bs-toggle="offcanvas"
                                        data-bs-target="#commentPanel" @click="openComment(post)">
                                        Xem tất cả {{ post.total_comment }} bình luận
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 right-menu">
                <div class="col-md-8">
                    <div class="user-info d-flex align-items-center gap-3 mb-4" v-if="currentUser">
                        <div class="avatar-box">
                            <img :src="currentUser.avatar_url || '../assets/logo.png'" />
                        </div>
                        <div>
                            <div class="fw-bold">{{ currentUser.user_name }}</div>
                            <small class="text-muted">{{ currentUser.full_name }}</small>
                        </div>
                        <div class="ms-auto">
                            <span @click="logout" class="text-danger fw-bold cursor-pointer">
                                Đăng xuất
                            </span>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">Gợi ý cho bạn</h6>
                    <div v-if="loadingSuggest">Đang tải...</div>
                    <div v-if="errorSuggest" class="text-danger">{{ errorSuggest }}</div>

                    <div v-for="user in suggest_friends" :key="user.id" class="suggest-item">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-box">
                                <img :src="user?.avatar_url || '../assets/logo.png'" />
                            </div>
                            <div>
                                <div class="fw-bold">{{ user.full_name }}</div>
                                <small class="text-muted">{{ user.user_name }}</small>
                            </div>
                        </div>
                        <button class="follow-btn" @click="followUser(user)">Theo dõi</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Xem story -->
        <div v-if="isViewingStory" class="story-viewer-overlay">
            <div class="viewer-logo">4ViewSocial</div>
            <button class="close-story-btn" @click="closeStoryViewer">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="viewer-layout-container">
                <div class="side-preview-box left-preview" v-if="prevUserGroup" @click="prevUserForce">
                    <div class="preview-content">
                        <img :src="prevUserGroup.avatar_url || '../assets/logo.png'" class="preview-avatar" />
                        <span class="preview-name">{{ prevUserGroup.user_name }}</span>
                    </div>
                    <div class="nav-arrow-circle"><i class="bi bi-chevron-left"></i></div>
                </div>
                <div class="side-preview-box placeholder" v-else></div>

                <div class="story-content-box" v-if="activeGroup">
                    <div class="progress-container">
                        <div v-for="(item, idx) in activeGroup.items" :key="idx" class="progress-segment">
                            <div class="progress-fill" :style="{ width: getProgressWidth(idx) }"></div>
                        </div>
                    </div>

                    <div class="story-header-info">
                        <img :src="activeGroup.avatar_url || '../assets/logo.png'" class="story-avatar-small" />
                        <span class="story-username-text">{{ activeGroup.user_name }}</span>
                        <span class="story-time-text">{{ formatTime(activeStoryItem.created_at) }}</span>
                    </div>

                    <div class="story-media-display">
                        <div class="nav-zone left" @click="prevStory"></div>
                        <div class="nav-zone right" @click="nextStory"></div>
                        <img v-if="isImage(activeStoryItem.video_url)" :src="activeStoryItem.video_url"
                            class="media-content" />
                        <video v-else ref="storyVideoRef" :src="activeStoryItem.video_url" class="media-content"
                            autoplay playsinline @timeupdate="onVideoTimeUpdate" @ended="onVideoEnded">
                        </video>
                    </div>

                    <div class="story-footer">
                        <div class="story-reply-box">
                            <input type="text" v-model="storyReplyText" :placeholder="storyReplyPlaceholder"
                                @keyup.enter="sendStoryReply" @focus="pauseStoryForInput"
                                @blur="resumeStoryFromInput" />
                        </div>
                        <button v-if="storyReplyText.trim()" class="story-send-text-btn"
                            @click="sendStoryReply">Gửi</button>
                        <button v-else class="story-like-btn" @click="likeCurrentStory">
                            <i :class="activeStoryItem.isLiked ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'"></i>
                        </button>
                    </div>
                </div>

                <div class="side-preview-box right-preview" v-if="nextUserGroup" @click="nextUserForce">
                    <div class="preview-content">
                        <img :src="nextUserGroup.avatar_url || '../assets/logo.png'" class="preview-avatar" />
                        <span class="preview-name">{{ nextUserGroup.user_name }}</span>
                    </div>
                    <div class="nav-arrow-circle"><i class="bi bi-chevron-right"></i></div>
                </div>
                <div class="side-preview-box placeholder" v-else></div>
            </div>
        </div>

        <!-- Modal tạo story -->
        <div class="modal fade" id="storyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tạo Story</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <label class="btn btn-outline-primary mb-3">
                            Chọn ảnh hoặc video
                            <input type="file" id="storyFileInput" hidden @change="handleStoryUpload"
                                accept="image/*,video/*" />
                        </label>
                        <div v-if="storyPreview" class="mt-2 border rounded p-2 bg-light">
                            <img v-if="isStoryImg" :src="storyPreview" class="img-fluid rounded"
                                style="max-height: 400px; object-fit: contain;" />
                            <video v-if="isStoryVideo" :src="storyPreview" controls autoplay class="img-fluid rounded"
                                style="max-height: 400px;"></video>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-primary" :disabled="!currentStoryFile" @click="submitStory">
                            <span v-if="isSubmittingStory" class="spinner-border spinner-border-sm me-1"></span> Đăng
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal tạo bài viết mới -->
        <div class="modal fade" id="uploadModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg-custom">
                <div class="modal-content upload-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title w-100 text-center">Tạo bài viết mới</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center p-0">

                        <div v-if="!previewUrl" class="upload-box-new">
                            <i class="bi bi-images icon-drag-drop"></i>
                            <p>Kéo ảnh và video vào đây</p>
                            <label class="btn btn-primary mt-2">
                                Chọn từ máy tính
                                <input type="file" hidden @change="handleFileUpload" accept="image/*,video/*" />
                            </label>
                        </div>

                        <div v-else class="preview-and-caption-wrapper">
                            <div class="preview-box">
                                <img v-if="isImagePost" :src="previewUrl" class="preview-media-new" />
                                <video v-if="isVideoPost" :src="previewUrl" class="preview-media-new" controls></video>
                            </div>
                            <div class="caption-box">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-box small me-2">
                                        <img :src="currentUser?.avatar_url || '../assets/logo.png'" alt="Avatar" />
                                    </div>
                                    <strong>{{ currentUser?.full_name || 'Người dùng' }}</strong>
                                </div>
                                <textarea class="form-control caption-textarea" rows="10"
                                    placeholder="Viết chú thích..." v-model="caption"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-primary" @click="addPost"
                            :disabled="!caption.trim() && !uploadFile">Đăng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offcanvas bình luận -->
        <div class="offcanvas offcanvas-bottom offcanvas-comment" tabindex="-1" id="commentPanel">

            <div class="header-comment position-relative text-center py-2 d-md-none border-bottom">
                <h6 class="m-0 fw-bold">Bình luận</h6>
                <button class="btn-close position-absolute top-50 end-0 translate-middle-y me-3"
                    data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="comment-wrapper" v-if="currentPost">
                <div class="comment-left d-none d-md-flex">
                    <video v-if="currentPost.thumbnail_url && !isImage(currentPost.thumbnail_url)"
                        :src="currentPost.thumbnail_url" controls
                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </video>
                    <img v-else :src="currentPost.thumbnail_url || '../assets/logo.png'" alt="Media" />
                </div>

                <div class="comment-right d-flex flex-column h-100 position-relative">

                    <div
                        class="d-none d-md-flex align-items-center justify-content-center p-3 border-bottom position-relative">
                        <h6 class="m-0 fw-bold">Bình luận</h6>
                        <button class="btn-close position-absolute end-0 me-3" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="comment-list flex-grow-1 overflow-auto p-3">
                        <div v-if="loadingComment" class="text-center py-3 text-muted">Đang tải bình luận...</div>

                        <div v-for="c in comments" :key="c.id" class="d-flex gap-2 mb-3">
                            <div class="avatar-box" style="width: 32px; height: 32px; flex-shrink: 0;">
                                <img :src="c.user?.avatar_url || '../assets/logo.png'" />
                            </div>
                            <div>
                                <p class="m-0"><strong>{{ c.user?.full_name }}</strong> {{ c.comment }}</p>
                                <small class="text-muted" style="font-size: 0.8rem;">{{ formatTime(c.created_at)
                                }}</small>

                                <div class="mt-2" v-for="r in c.children" :key="r.id">
                                    <div class="d-flex gap-2 mb-2">
                                        <img :src="r.user?.avatar_url" class="rounded-circle" width="30" height="30" />
                                        <div>
                                            <p class="m-0"><strong>{{ r.user.full_name }}</strong> {{ r.comment }}</p>
                                            <small class="text-muted">{{ formatTime(r.created_at) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="comment-footer border-top p-3 bg-white">
                        <div class="fw-bold mb-2">{{ currentPost.total_like }} lượt thích</div>

                        <div class="mb-2">
                            <span class="fw-bold me-1">{{ currentPost.author_fullname }}</span>
                            <span>{{ currentPost.caption }}</span>
                        </div>
                        <small class="text-muted d-block mb-3" style="font-size: 0.75rem;">{{
                            formatTime(currentPost.created_at) }}</small>

                        <div class="input-group">
                            <input v-model="commentText" @keyup.enter="sendComment" type="text"
                                class="form-control border-0" placeholder="Thêm bình luận..." style="background: none;">
                            <button class="btn text-primary fw-bold" @click="sendComment">Gửi</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Chat Widget -->
        <ChatWidget />
    </div>
</template>

<script>
import SidebarComponent from '@/components/SidebarComponent.vue';
import api from "../api/client";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import * as bootstrap from "bootstrap";
import ChatWidget from '@/components/ChatWidget.vue';
import "bootstrap-icons/font/bootstrap-icons.css";
import { signOut } from "firebase/auth";
import { auth } from "@/firebase";
export default {
    components: { SidebarComponent, ChatWidget },

    data() {
        return {
            currentUser: null,
            list_posts: [],
            suggest_friends: [],
            loadingSuggest: false,
            errorSuggest: null,

            // --- STORY UPLOAD ---
            currentStoryFile: null,
            storyPreview: null,
            isStoryImg: false,
            isStoryVideo: false,
            isSubmittingStory: false,

            // --- STORY VIEWER ---
            rawStories: [],
            groupedStories: [],
            isViewingStory: false,
            activeUserIndex: 0,
            activeStoryIndex: 0,
            progressPercent: 0,
            storyTimer: null,
            storyReplyText: "",
            isPausedForInput: false,

            // --- POST UPLOAD (NEW) ---
            uploadFile: null,
            previewUrl: null,
            caption: "",
            isImagePost: false,
            isVideoPost: false,

            // --- COMMENT / POST DETAIL ---
            currentPost: null,
            comments: [],
            commentText: "",
            loadingComment: false,
        };
    },

    computed: {
        activeGroup() {
            return this.groupedStories[this.activeUserIndex] || null;
        },
        activeStoryItem() {
            if (this.activeGroup && this.activeGroup.items) {
                return this.activeGroup.items[this.activeStoryIndex];
            }
            return null;
        },
        storyReplyPlaceholder() {
            const name = this.activeGroup?.user_name;
            return name ? `Trả lời ${name}...` : `Trả lời...`;
        },
        prevUserGroup() {
            if (this.activeUserIndex > 0) return this.groupedStories[this.activeUserIndex - 1];
            return null;
        },
        nextUserGroup() {
            if (this.activeUserIndex < this.groupedStories.length - 1) return this.groupedStories[this.activeUserIndex + 1];
            return null;
        }
    },

    mounted() {
        const user = JSON.parse(sessionStorage.getItem('user_info'));
        if (!user) { this.$router.push('/login'); return; }
        this.currentUser = user;

        this.listPost();
        this.suggestFriend();
        this.listStory();
    },

    beforeUnmount() {
        this.stopImageTimer();
    },

    methods: {
        // --- 1. STORY LOGIC (GIỮ NGUYÊN) ---
        async listStory() {
            try {
                const res = await api.get('/api/post/list-story', { params: { user_id: this.currentUser.id } });
                if (res.status === 200) {
                    this.rawStories = res.data.data;
                    this.groupStoriesByUser(this.rawStories);
                }
            } catch (err) { console.error(err); }
        },

        groupStoriesByUser(stories) {
            const groups = {};
            stories.forEach(story => {
                const uid = story.user_id;
                story.isLiked = !!story.is_liked;

                if (!groups[uid]) {
                    groups[uid] = {
                        user_id: uid, user_name: story.user_name, full_name: story.full_name, avatar_url: story.avatar_url, items: []
                    };
                }
                groups[uid].items.push(story);
            });
            this.groupedStories = Object.values(groups);
        },

        openStoryViewer(userIndex) {
            this.activeUserIndex = userIndex;
            this.activeStoryIndex = 0;
            this.isViewingStory = true;
            this.isPausedForInput = false;
            this.$nextTick(() => { this.handleCurrentStoryItem(); });
        },

        closeStoryViewer() {
            this.isViewingStory = false;
            this.stopImageTimer();
            if (this.$refs.storyVideoRef) { this.$refs.storyVideoRef.pause(); }
        },

        nextUserForce() {
            if (this.activeUserIndex < this.groupedStories.length - 1) {
                this.activeUserIndex++;
                this.activeStoryIndex = 0;
                this.$nextTick(() => this.handleCurrentStoryItem());
            } else { this.closeStoryViewer(); }
        },

        prevUserForce() {
            if (this.activeUserIndex > 0) {
                this.activeUserIndex--;
                this.activeStoryIndex = 0;
                this.$nextTick(() => this.handleCurrentStoryItem());
            }
        },

        nextStory() {
            if (this.activeStoryIndex < this.activeGroup.items.length - 1) {
                this.activeStoryIndex++;
                this.$nextTick(() => this.handleCurrentStoryItem());
            } else { this.nextUserForce(); }
        },

        prevStory() {
            if (this.activeStoryIndex > 0) {
                this.activeStoryIndex--;
                this.$nextTick(() => this.handleCurrentStoryItem());
            } else { this.prevUserForce(); }
        },

        handleCurrentStoryItem() {
            this.stopImageTimer(); this.progressPercent = 0; this.storyReplyText = "";
            const item = this.activeStoryItem; if (!item) return;

            if (this.isImage(item.video_url)) {
                this.runImageTimer();
            } else {
                const videoEl = this.$refs.storyVideoRef;
                if (videoEl) {
                    videoEl.load(); videoEl.currentTime = 0;
                    videoEl.play().catch(e => console.warn("Autoplay blocked"));
                }
            }
        },

        runImageTimer() {
            const DURATION = 5000; const UPDATE = 50;
            this.storyTimer = setInterval(() => {
                if (this.isPausedForInput) return;
                this.progressPercent += (UPDATE / DURATION) * 100;
                if (this.progressPercent >= 100) this.nextStory();
            }, UPDATE);
        },

        stopImageTimer() {
            if (this.storyTimer) clearInterval(this.storyTimer);
            this.storyTimer = null;
        },

        onVideoTimeUpdate(e) {
            if (this.isPausedForInput) return;
            const video = e.target;
            if (video.duration) this.progressPercent = (video.currentTime / video.duration) * 100;
        },

        onVideoEnded() { this.nextStory(); },

        pauseStoryForInput() {
            this.isPausedForInput = true;
            if (this.$refs.storyVideoRef) this.$refs.storyVideoRef.pause();
        },

        resumeStoryFromInput() {
            if (!this.storyReplyText.trim()) {
                this.isPausedForInput = false;
                if (!this.isImage(this.activeStoryItem.video_url) && this.$refs.storyVideoRef) { this.$refs.storyVideoRef.play(); }
            }
        },

        async likeCurrentStory() {
            const story = this.activeStoryItem; if (!story) return;
            story.isLiked = !story.isLiked;
            try {
                await api.post('/api/post/like-story', { story_id: story.id, user_id: this.currentUser.id, is_liked: story.isLiked });
            } catch (e) { story.isLiked = !story.isLiked; }
        },

        async sendStoryReply() {
            const text = this.storyReplyText.trim(); if (!text) return;
            this.storyReplyText = ""; this.isPausedForInput = false;
            document.querySelector('.story-reply-box input')?.blur();
            if (!this.isImage(this.activeStoryItem.video_url) && this.$refs.storyVideoRef) { this.$refs.storyVideoRef.play(); }
            try {
                await api.post('/api/post/reply-story', { story_id: this.activeStoryItem.id, user_id: this.currentUser.id, content: text });
            } catch (e) { console.error(e); }
        },

        getProgressWidth(index) {
            if (index < this.activeStoryIndex) return '100%';
            if (index === this.activeStoryIndex) return this.progressPercent + '%';
            return '0%';
        },

        isImage(url) {
            if (!url) return true;
            return /\.(jpg|jpeg|png|webp|gif|bmp)$/i.test(url);
        },

        // --- 2. UPLOAD STORY (GIỮ NGUYÊN) ---
        openStoryModal() {
            this.closeStoryViewer(); this.currentStoryFile = null; this.storyPreview = null;
            document.getElementById('storyFileInput').value = '';
            new bootstrap.Modal(document.getElementById('storyModal')).show();
        },

        handleStoryUpload(event) {
            const file = event.target.files[0]; if (!file) return;
            this.currentStoryFile = file; this.storyPreview = URL.createObjectURL(file);
            this.isStoryImg = file.type.startsWith('image/'); this.isStoryVideo = file.type.startsWith('video/');
        },

        async submitStory() {
            if (!this.currentStoryFile) return;
            this.isSubmittingStory = true;
            const fd = new FormData(); fd.append('file', this.currentStoryFile); fd.append('user_id', this.currentUser.id);
            try {
                const res = await api.post('/api/post/add-story', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
                if (res.status === 200 || res.status === 201) {
                    alert("Đăng story thành công!");
                    bootstrap.Modal.getInstance(document.getElementById('storyModal')).hide();
                    this.listStory();
                }
            } catch (e) { alert("Lỗi đăng story"); }
            finally { this.isSubmittingStory = false; }
        },

        // --- 3. UPLOAD POST (TỪ FILE CŨ) ---
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Kiểm tra Ảnh hoặc Video
            const isImg = file.type.startsWith("image/");
            const isVid = file.type.startsWith("video/");

            if (!isImg && !isVid) {
                alert("Chỉ được upload ảnh hoặc video");
                event.target.value = "";
                return;
            }

            this.uploadFile = file;
            this.isImagePost = isImg;
            this.isVideoPost = isVid;
            this.previewUrl = URL.createObjectURL(file);
        },

        async addPost() {
            // 1. Validate
            if (!this.uploadFile && !this.caption.trim()) {
                alert("Vui lòng chọn file hoặc nhập caption");
                return;
            }

            const formData = new FormData();
            if (this.uploadFile) {
                formData.append("thumbnail", this.uploadFile);
            }
            formData.append("caption", this.caption);
            formData.append("user_id", this.currentUser.id);

            try {
                // 2. Gọi API
                await api.post("/api/post/add-post", formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                alert("Đăng bài thành công!");

                // 3. Reset form
                this.caption = "";
                this.uploadFile = null;
                this.previewUrl = null;

                // 4. Đóng Modal
                const modalEl = document.getElementById('uploadModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }

                // 
                setTimeout(() => {
                    // Xóa backdrop (lớp nền đen)
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Xóa class khóa cuộn của body
                    document.body.classList.remove('modal-open');

                    // Ép style trực tiếp để chắc chắn cuộn được
                    document.body.style.overflow = 'auto';
                    document.body.style.paddingRight = '0px';
                }, 350); // Chờ 350ms (lâu hơn thời gian animation của Bootstrap chút xíu)

                // 5. Load lại danh sách
                this.listPost();

            } catch (err) {
                console.error(err);
                alert("Lỗi upload bài viết");
            }
        },

        listPost() {
            api.get('/api/post/list-post', { params: { user_id: this.currentUser.id } }).then(res => {
                this.list_posts = res.data.data.map(p => ({ ...p, isLiked: !!p.isLiked }));
            });
        },

        async likePost(post) {
            try {
                const res = await api.post('/api/post/like-post', {
                    post_id: post.id,
                    user_id: this.currentUser.id
                });

                // Kiểm tra res.data và res.data.data có tồn tại không trước khi đọc total_like
                if (res.status === 200 && res.data && res.data.data) {
                    post.total_like = res.data.data.total_like;
                    post.isLiked = !post.isLiked;
                } else {
                    console.warn("API trả về thành công nhưng thiếu dữ liệu data:", res.data);
                    // Có thể load lại danh sách bài viết nếu dữ liệu bị sai lệch
                    // this.listPost(); 
                }
            } catch (err) {
                console.error("Lỗi khi thực hiện like:", err);
            }
        },

        async savePost(post) {
            try {
                const res = await api.post('/api/post/save-post', { post_id: post.id, user_id: this.currentUser.id });
                if (res.status === 200) post.isSaved = res.data.data.saved;
            } catch (err) { console.error("Lỗi save:", err); }
        },

        async openComment(post) {
            this.currentPost = post;
            this.comments = [];
            this.loadingComment = true;
            try {
                const res = await api.get("/api/post/list-comment", { params: { post_id: post.id } });
                if (res.status === 200) this.comments = res.data.data.comments;
            } catch (err) { console.error(err); }
            finally { this.loadingComment = false; }
        },

        async sendComment() {
            if (!this.commentText.trim()) return;
            try {
                const res = await api.post("/api/post/comment", {
                    post_id: this.currentPost.id, user_id: this.currentUser.id, comment: this.commentText
                });
                if (res.status === 200) {
                    this.comments.unshift(res.data.data.comment);
                    this.currentPost.total_comment++;
                    this.commentText = "";
                }
            } catch (error) { console.error(error); }
        },

        async suggestFriend() {
            this.loadingSuggest = true;
            try {
                // Lấy ID người dùng hiện tại (từ data currentUser hoặc trực tiếp từ session)
                const userLocal = JSON.parse(sessionStorage.getItem('user_info'));
                const currentId = userLocal ? userLocal.id : null;

                // Gọi API và truyền user_id vào params của Axios
                const res = await api.get('/api/post/suggest-friend', {
                    params: {
                        user_id: currentId
                    }
                });

                if (res.status === 200) {
                    this.suggest_friends = res.data.data.users;
                }
            } catch (err) {
                console.error("Lỗi tải gợi ý:", err);
                this.errorSuggest = "Lỗi tải gợi ý";
            } finally {
                this.loadingSuggest = false;
            }
        },

        async followUser(targetUser) {
            if (!targetUser || !targetUser.id) {
                console.error("Dữ liệu người dùng gợi ý không hợp lệ");
                return;
            }

            try {
                const payload = {
                    following_id: targetUser.id, // ID của người được click
                    user_id: this.currentUser.id   // ID của chính mình
                };

                console.log("Gửi Payload:", payload); // Kiểm tra ở tab Console

                const res = await api.post('/api/post/follow', payload);

                if (res.status === 200) {
                    // Xóa người đó khỏi danh sách gợi ý
                    this.suggest_friends = this.suggest_friends.filter(item => item.id !== targetUser.id);
                }
            } catch (err) {
                console.error("Lỗi follow:", err.response?.data || err);
                alert("Không thể theo dõi người dùng này");
            }
        },

        async logout() {
            this.$router.push("/login");

            await signOut(auth);
            await api.post('/api/auth/logout', { user_id: this.currentUser.id });
            sessionStorage.removeItem("user_info");
            sessionStorage.removeItem("access_token");


        },

        formatTime(t) {
            const d = (new Date() - new Date(t)) / 1000;
            if (d < 60) return "Vừa xong";
            if (d < 3600) return Math.floor(d / 60) + " phút trước";
            if (d < 86400) return Math.floor(d / 3600) + " giờ trước";
            return Math.floor(d / 86400) + " ngày trước";
        }
    }
};
</script>

<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css');

body {
    background-color: #fafafa;
}

/* ======== STORY STYLES (GIỮ NGUYÊN) ======== */
.story-wrapper {
    background: #fff;
    border: 1px solid #dbdbdb;
    border-radius: 8px;
    padding: 16px 0;
    margin-bottom: 24px;
}

.story-scroll-container {
    display: flex;
    overflow-x: auto;
    padding: 0 16px;
    gap: 16px;
    scrollbar-width: none;
}

.story-scroll-container::-webkit-scrollbar {
    display: none;
}

.story-item-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 70px;
    cursor: pointer;
    flex-shrink: 0;
}

.story-circle {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 4px;
    position: relative;
}

.has-story-gradient {
    background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
    padding: 2px;
}

.add-story {
    border: 2px dashed #dbdbdb;
}

.add-story i {
    font-size: 24px;
    color: #0095f6;
}

.story-circle-inner {
    background: #fff;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    padding: 2px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.story-circle-inner img,
.story-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.story-username {
    font-size: 12px;
    color: #262626;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    width: 100%;
    text-align: center;
}

/* ======== STORY VIEWER OVERLAY ======== */
.story-viewer-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: #1a1a1a;
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.viewer-logo {
    position: absolute;
    top: 20px;
    left: 20px;
    color: #fff;
    font-family: 'Billabong', cursive;
    font-size: 30px;
}

.close-story-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    color: #fff;
    font-size: 30px;
    cursor: pointer;
    z-index: 10000;
    transition: transform 0.2s ease;
}

.close-story-btn:hover {
    transform: scale(1.2) rotate(90deg);
    /* Phóng to và xoay nhẹ cho đẹp */
}

.viewer-layout-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
    width: 100%;
    height: 100%;
}

.story-content-box {
    width: 100%;
    max-width: 400px;
    aspect-ratio: 9/16;
    height: 90vh;
    background: #000;
    border-radius: 12px;
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}

.side-preview-box {
    width: 200px;
    height: 350px;
    background-color: #333;
    border-radius: 10px;
    opacity: 0.5;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, opacity 0.2s;
    position: relative;
}

.side-preview-box:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

.side-preview-box.placeholder {
    visibility: hidden;
    pointer-events: none;
}

.preview-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #fff;
}

.preview-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 2px solid #fff;
    margin-bottom: 10px;
}

.nav-arrow-circle {
    position: absolute;
    width: 30px;
    height: 30px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-size: 16px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.left-preview .nav-arrow-circle {
    right: -15px;
    top: 50%;
    transform: translateY(-50%);
}

.right-preview .nav-arrow-circle {
    left: -15px;
    top: 50%;
    transform: translateY(-50%);
}

.progress-container {
    position: absolute;
    top: 12px;
    left: 0;
    width: 100%;
    display: flex;
    gap: 4px;
    padding: 0 10px;
    z-index: 20;
}

.progress-segment {
    flex: 1;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #fff;
    transition: width 0.05s linear;
}

.story-header-info {
    position: absolute;
    top: 25px;
    left: 0;
    width: 100%;
    padding: 0 15px;
    display: flex;
    align-items: center;
    z-index: 20;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

.story-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 10px;
}

.story-username-text {
    font-weight: 600;
    font-size: 14px;
    margin-right: 8px;
}

.story-time-text {
    font-size: 12px;
    opacity: 0.8;
}

.story-media-display {
    flex: 1;
    background: #222;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.media-content {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.nav-zone {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 35%;
    z-index: 15;
}

.nav-zone.left {
    left: 0;
}

.nav-zone.right {
    right: 0;
}

.story-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    z-index: 30;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
}

.story-reply-box {
    flex: 1;
}

.story-reply-box input {
    width: 100%;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-radius: 50px;
    padding: 12px 20px;
    color: #fff;
    font-size: 14px;
    outline: none;
}

.story-reply-box input::placeholder {
    color: rgba(255, 255, 255, 0.8);
}

.story-like-btn {
    background: none;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.story-like-btn i {
    font-size: 30px;
    color: #fff;
    transition: transform 0.2s ease;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.story-like-btn:hover i {
    transform: scale(1.2);
}

.story-like-btn i:active {
    transform: scale(1.2);
}

.story-send-text-btn {
    background: none;
    border: none;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    padding: 0 5px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .side-preview-box {
        display: none;
    }

    .story-content-box {
        width: 100%;
        max-width: 100%;
        height: 100%;
        border-radius: 0;
    }

    .viewer-layout-container {
        gap: 0;
    }
}

/* ======== POST STYLES (TỪ FILE CŨ) ======== */
.post {
    background: #fff;
    border: 1px solid #dbdbdb;
    border-radius: 8px;
    margin-bottom: 24px;
}

.post-header {
    padding: 10px;
    display: flex;
    align-items: center;
}

.avatar-box {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 10px;
    flex-shrink: 0;
}

.avatar-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.post-image {
    width: 100%;
    max-height: 600px;
    object-fit: contain;
    background-color: #f0f0f0;
}

.post-actions {
    padding: 8px 0;
    display: flex;
    align-items: center;
}

.action-btn {
    background: none;
    border: none;
    font-size: 1.4rem;
    cursor: pointer;
    color: #262626;
    padding: 0 8px;
    /* Thêm dòng dưới đây để chuyển động mượt mà */
    transition: transform 0.2s ease-in-out;
    display: inline-block;
    /* Đảm bảo transform hoạt động tốt */
}

/* Thêm hiệu ứng hover phóng to */
.action-btn:hover {
    transform: scale(1.2);
    /* Phóng to 20% */
}

/* ======== RIGHT MENU STYLES (TỪ FILE CŨ) ======== */
.right-menu {
    margin-top: 1rem;
}

.suggest-item {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 10px;
}

.follow-btn {
    background-color: #ff7fc8;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    margin-left: auto;
}

.follow-btn:hover {
    background-color: #ff5daf;
}

/* ======== UPLOAD MODAL (TỪ FILE CŨ) ======== */
.modal-dialog.modal-lg-custom {
    max-width: 900px;
    width: 90%;
}

.upload-modal-content {
    min-height: 400px;
    height: 70vh;
    border-radius: 25px;
}

.upload-box-new {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    width: 100%;
    height: 100%;
}

.icon-drag-drop {
    font-size: 5rem;
    color: #888;
    margin-bottom: 1rem;
}

.preview-and-caption-wrapper {
    display: flex;
    width: 100%;
    height: 100%;
}

.preview-box {
    width: 60%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #000;
}

.preview-media-new {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.caption-box {
    width: 40%;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    border-left: 1px solid #eee;
}

.caption-textarea {
    resize: none;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    flex-grow: 1;
}

.avatar-box.small {
    width: 35px;
    height: 35px;
    border: none;
}

/* ======== OFFCANVAS COMMENT (POPUP STYLE) ======== */

/* Cấu hình chung */
.offcanvas-comment {
    max-width: 100%;
    height: 80vh !important;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

/* Layout chia đôi */
.comment-wrapper {
    display: flex;
    height: 100%;
}

/* Cột trái (Ảnh/Video) */
.comment-left {
    width: 55%;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.comment-left img,
.comment-left video {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* Cột phải (Comment) */
.comment-right {
    width: 45%;
    display: flex;
    flex-direction: column;
    background: #fff;
    position: relative;
}

.comment-list {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.comment-footer {
    background-color: #fff;
}

/* Responsive Mobile (Dưới 768px): Xếp chồng dọc */
@media (max-width: 768px) {
    .comment-wrapper {
        flex-direction: column;
    }

    .comment-right {
        width: 100%;
        height: 100%;
    }
}

/* Desktop (Trên 768px): Biến thành Popup ở giữa */
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
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);

        /* Hiệu ứng ẩn/hiện mượt mà */
        transform: translate(-50%, -50%) scale(0.9);
        opacity: 0;
        visibility: hidden;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease, visibility 0.3s;
    }

    /* Khi mở (có class .show) */
    .offcanvas-comment.show {
        transform: translate(-50%, -50%) scale(1) !important;
        opacity: 1;
        visibility: visible;
    }
}
</style>