<template>
    <div class="app-layout">
        <!-- 1. LEFT SIDEBAR -->
        <SidebarComponent />

        <!-- 2. MAIN FEED CONTAINER -->
        <main class="feed-main">
            <div class="feed-container">

                <!-- STORIES CAROUSEL -->
                <section class="stories-section">
                    <div class="stories-scroll">
                        <!-- Add Story Item -->
                        <div class="story-item" @click="openStoryModal">
                            <div class="story-ring add-story-ring">
                                <img :src="currentUser?.avatar_url || getAvatarUrl(currentUser?.user_name)" class="story-img" />
                                <div class="add-badge">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </div>
                            </div>
                            <span class="story-name">Tin của bạn</span>
                        </div>

                        <!-- User Stories -->
                        <div 
                            class="story-item" 
                            v-for="(group, index) in groupedStories" 
                            :key="group.user_id" 
                            @click="openStoryViewer(index)"
                        >
                            <div class="story-ring unread-story-ring">
                                <div class="story-ring-inner">
                                    <img :src="group.avatar_url || getAvatarUrl(group.user_name)" class="story-img" />
                                </div>
                            </div>
                            <span class="story-name">{{ group.user_name }}</span>
                        </div>
                    </div>
                </section>

                <!-- CREATE POST QUICK CARD -->
                <section class="create-post-card">
                    <div class="d-flex align-items-center gap-3">
                        <img :src="currentUser?.avatar_url || getAvatarUrl(currentUser?.user_name)" class="user-avatar-sm" />
                        <div class="quick-input-trigger flex-grow-1" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <span>{{ currentUser?.full_name || currentUser?.user_name }} ơi, bạn đang nghĩ gì thế?</span>
                        </div>
                    </div>
                    <div class="create-post-actions mt-3 pt-2 border-top d-flex justify-content-around">
                        <button class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span>Ảnh/Video</span>
                        </button>
                        <button class="quick-action-btn" @click="openStoryModal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="10 8 16 12 10 16 10 8" fill="#ec4899"></polygon>
                            </svg>
                            <span>Tạo Story</span>
                        </button>
                        <button class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                                <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                <line x1="15" y1="9" x2="15.01" y2="9"></line>
                            </svg>
                            <span>Cảm xúc</span>
                        </button>
                    </div>
                </section>

                <!-- EMPTY POSTS STATE -->
                <div v-if="list_posts.length === 0" class="empty-feed-card text-center py-5">
                    <div class="empty-icon-wrap mb-3">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="4" ry="4"></rect>
                            <circle cx="12" cy="12" r="3"></circle>
                            <line x1="16.5" y1="7.5" x2="16.51" y2="7.5"></line>
                        </svg>
                    </div>
                    <h5 class="fw-bold text-dark">Chưa có bài viết nào</h5>
                    <p class="text-muted small">Hãy chia sẻ khoảnh khắc đầu tiên của bạn hoặc theo dõi bạn bè nhé!</p>
                    <button class="btn btn-pink btn-sm rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        Tạo bài viết
                    </button>
                </div>

                <!-- POSTS FEED LIST -->
                <section class="posts-feed" v-else>
                    <article class="post-card" v-for="post in list_posts" :key="post.id">
                        <!-- Post Header -->
                        <header class="post-card-header">
                            <div class="d-flex align-items-center gap-3">
                                <a :href="`/profile/${post.user_id}`" class="avatar-link">
                                    <div class="post-avatar-wrap">
                                        <img :src="post.author_avatar || getAvatarUrl(post.author_fullname || 'User')" class="post-avatar" />
                                    </div>
                                </a>
                                <div class="post-user-meta">
                                    <div class="d-flex align-items-center gap-1">
                                        <a :href="`/profile/${post.user_id}`" class="post-author-name">
                                            {{ post.author_fullname || 'Người dùng' }}
                                        </a>
                                        <svg class="verified-badge-svg" width="14" height="14" viewBox="0 0 24 24" fill="#ec4899">
                                            <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7l-4.2-4.2 1.4-1.4 2.8 2.8 6.8-6.8 1.4 1.4-8.2 8.2z"/>
                                        </svg>
                                    </div>
                                    <span class="post-time">• {{ formatTime(post.created_at) }}</span>
                                </div>
                            </div>

                            <button class="btn-post-options" title="Tùy chọn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>
                        </header>

                        <!-- Post Caption (Top) -->
                        <div class="post-caption-body px-3 pt-2 pb-2" v-if="post.caption">
                            <p class="caption-text m-0">{{ post.caption }}</p>
                        </div>

                        <!-- Post Media Container -->
                        <div class="post-media-box position-relative" @dblclick="onDoubleTapLike(post)">
                            <!-- Video Player -->
                            <video 
                                v-if="post.thumbnail_url && !isImage(post.thumbnail_url)"
                                :src="post.thumbnail_url" 
                                class="post-media" 
                                controls 
                                playsinline
                            ></video>

                            <!-- Image Display -->
                            <img 
                                v-else-if="post.thumbnail_url" 
                                :src="post.thumbnail_url" 
                                class="post-media" 
                                alt="Post media" 
                                loading="lazy"
                            />

                            <!-- Floating Heart on Double Tap -->
                            <transition name="heart-pop">
                                <div v-if="post.showHeartAnim" class="double-tap-heart">
                                    <svg width="85" height="85" viewBox="0 0 24 24" fill="#ec4899" style="filter: drop-shadow(0 4px 16px rgba(236,72,153,0.6));">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </div>
                            </transition>
                        </div>

                        <!-- Post Actions Bar -->
                        <div class="post-actions-bar">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Like Button -->
                                <button class="action-btn like-btn" :class="{ 'liked': post.isLiked }" @click="likePost(post)" title="Thích">
                                    <svg v-if="post.isLiked" width="24" height="24" viewBox="0 0 24 24" fill="#ec4899" class="anim-pop">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Comment Button -->
                                <button class="action-btn comment-btn" data-bs-toggle="offcanvas" data-bs-target="#commentPanel" @click="openComment(post)" title="Bình luận">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                    </svg>
                                </button>

                                <!-- Share Button -->
                                <button class="action-btn share-btn" @click="sharePost(post)" title="Chia sẻ">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                </button>
                            </div>

                            <!-- Bookmark Button -->
                            <button class="action-btn bookmark-btn ms-auto" :class="{ 'saved': post.isSaved }" @click="savePost(post)" title="Lưu bài">
                                <svg v-if="post.isSaved" width="24" height="24" viewBox="0 0 24 24" fill="#ec4899">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Post Footer & Stats -->
                        <footer class="post-card-footer px-3 pb-3">
                            <div class="likes-count mb-1" v-if="post.total_like > 0">
                                <span class="fw-bold">{{ post.total_like.toLocaleString() }} lượt thích</span>
                            </div>

                            <!-- Comment count & Open drawer -->
                            <div 
                                v-if="post.total_comment > 0" 
                                class="view-comments-link" 
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#commentPanel" 
                                @click="openComment(post)"
                            >
                                Xem tất cả {{ post.total_comment }} bình luận
                            </div>

                            <!-- Quick Inline Comment -->
                            <div class="quick-comment-bar mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                <input 
                                    type="text" 
                                    class="form-control form-control-sm quick-comment-input" 
                                    placeholder="Thêm bình luận..." 
                                    v-model="post.quickCommentText"
                                    @keyup.enter="sendQuickComment(post)"
                                />
                                <button 
                                    class="btn btn-sm btn-link text-pink fw-bold text-decoration-none p-0 px-2"
                                    :disabled="!post.quickCommentText || !post.quickCommentText.trim()"
                                    @click="sendQuickComment(post)"
                                >
                                    Đăng
                                </button>
                            </div>
                        </footer>
                    </article>
                </section>
            </div>
        </main>

        <!-- 3. RIGHT WIDGETS SIDEBAR -->
        <aside class="feed-aside d-none d-xl-block">
            <div class="aside-sticky-container">
                <!-- User Profile Card -->
                <div class="user-switch-card p-3 mb-4 d-flex align-items-center justify-content-between" v-if="currentUser">
                    <a :href="`/profile/${currentUser.id}`" class="d-flex align-items-center gap-3 text-decoration-none">
                        <img :src="currentUser.avatar_url || getAvatarUrl(currentUser.user_name)" class="user-avatar-md shadow-sm" />
                        <div class="user-names">
                            <span class="fw-bold text-dark d-block" style="font-size: 14px;">{{ currentUser.full_name || currentUser.user_name }}</span>
                            <small class="text-pink">@{{ currentUser.user_name }}</small>
                        </div>
                    </a>
                    <button class="btn-switch-account" @click="logout" title="Đăng xuất">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Thoát
                    </button>
                </div>

                <!-- Suggestions Card -->
                <div class="suggestions-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-pink-dark text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">Gợi ý cho bạn</span>
                        <a href="/explore" class="text-decoration-none small text-pink fw-semibold">Xem tất cả</a>
                    </div>

                    <div v-if="loadingSuggest" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-pink"></div>
                    </div>

                    <div v-else-if="suggest_friends.length === 0" class="text-center text-muted py-3 small">
                        Không còn gợi ý bạn bè mới.
                    </div>

                    <div class="suggest-list" v-else>
                        <div v-for="user in suggest_friends.slice(0, 5)" :key="user.id" class="suggest-row d-flex align-items-center justify-content-between py-2">
                            <a :href="`/profile/${user.id}`" class="d-flex align-items-center gap-2 text-decoration-none overflow-hidden me-2">
                                <img :src="user?.avatar_url || getAvatarUrl(user?.user_name)" class="rounded-circle" width="38" height="38" style="object-fit: cover;" />
                                <div class="overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 13px;">{{ user.full_name || user.user_name }}</div>
                                    <small class="text-muted text-truncate d-block" style="font-size: 11px;">@{{ user.user_name }}</small>
                                </div>
                            </a>
                            <button 
                                class="btn-follow-sm" 
                                :class="{ 'btn-followed': user.is_followed }" 
                                @click="followUser(user)"
                            >
                                {{ user.is_followed ? 'Đang theo dõi' : 'Theo dõi' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <footer class="aside-footer mt-4 px-2">
                    <div class="footer-links d-flex flex-wrap gap-2 text-muted small" style="font-size: 11px;">
                        <a href="#">Giới thiệu</a> • 
                        <a href="#">Trợ giúp</a> • 
                        <a href="#">Bảo mật</a> • 
                        <a href="#">Điều khoản</a> • 
                        <a href="#">Ngôn ngữ</a>
                    </div>
                    <p class="copyright-text mt-3 text-muted" style="font-size: 11px;">
                        © 2026 4VIEWSSOCIAL FROM VIETNAM
                    </p>
                </footer>
            </div>
        </aside>

        <!-- 4. STORY VIEWER OVERLAY -->
        <div v-if="isViewingStory" class="story-viewer-overlay">
            <div class="viewer-brand">4Views</div>
            <button class="close-viewer-btn" @click="closeStoryViewer">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div class="viewer-carousel-wrap">
                <!-- Left Story Preview -->
                <div class="story-preview-tile left-tile" v-if="prevUserGroup" @click="prevUserForce">
                    <img :src="prevUserGroup.avatar_url || getAvatarUrl(prevUserGroup.user_name)" class="tile-avatar" />
                    <span class="tile-name">{{ prevUserGroup.user_name }}</span>
                    <div class="tile-arrow">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="story-preview-tile placeholder-tile" v-else></div>

                <!-- Active Story Card -->
                <div class="active-story-card" v-if="activeGroup">
                    <!-- Progress Bars -->
                    <div class="story-progress-bar">
                        <div v-for="(item, idx) in activeGroup.items" :key="idx" class="progress-segment">
                            <div class="progress-fill" :style="{ width: getProgressWidth(idx) }"></div>
                        </div>
                    </div>

                    <!-- Story Author Header -->
                    <div class="story-author-header">
                        <img :src="activeGroup.avatar_url || getAvatarUrl(activeGroup.user_name)" class="story-author-avatar" />
                        <div class="story-author-info">
                            <span class="story-author-name">{{ activeGroup.user_name }}</span>
                            <span class="story-time-stamp">{{ formatTime(activeStoryItem?.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Story Media Display -->
                    <div class="story-display-box">
                        <div class="tap-zone tap-left" @click="prevStory"></div>
                        <div class="tap-zone tap-right" @click="nextStory"></div>

                        <img 
                            v-if="isImage(activeStoryItem?.video_url)" 
                            :src="activeStoryItem.video_url" 
                            class="story-media-fit" 
                        />
                        <video 
                            v-else 
                            ref="storyVideoRef" 
                            :src="activeStoryItem?.video_url" 
                            class="story-media-fit"
                            autoplay 
                            playsinline 
                            @timeupdate="onVideoTimeUpdate" 
                            @ended="onVideoEnded"
                        ></video>
                    </div>

                    <!-- Story Footer / Reply -->
                    <div class="story-interactive-footer">
                        <div class="story-input-box">
                            <input 
                                type="text" 
                                v-model="storyReplyText" 
                                :placeholder="storyReplyPlaceholder"
                                @keyup.enter="sendStoryReply" 
                                @focus="pauseStoryForInput"
                                @blur="resumeStoryFromInput" 
                            />
                        </div>
                        <button v-if="storyReplyText.trim()" class="btn-send-reply" @click="sendStoryReply">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#ec4899">
                                <line x1="22" y1="2" x2="11" y2="13" stroke="#ec4899" stroke-width="2"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2" fill="#ec4899"></polygon>
                            </svg>
                        </button>
                        <button v-else class="btn-story-like" @click="likeCurrentStory">
                            <svg v-if="activeStoryItem?.isLiked" width="24" height="24" viewBox="0 0 24 24" fill="#ec4899">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Right Story Preview -->
                <div class="story-preview-tile right-tile" v-if="nextUserGroup" @click="nextUserForce">
                    <img :src="nextUserGroup.avatar_url || getAvatarUrl(nextUserGroup.user_name)" class="tile-avatar" />
                    <span class="tile-name">{{ nextUserGroup.user_name }}</span>
                    <div class="tile-arrow">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="story-preview-tile placeholder-tile" v-else></div>
            </div>
        </div>

        <!-- 5. CREATE STORY MODAL -->
        <div class="modal fade" id="storyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modern-modal">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold text-pink">Tạo Story Mới</h6>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <div v-if="!storyPreview" class="upload-dropzone p-4">
                            <svg class="mb-2" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="1.5" stroke-linecap="round">
                                <rect x="2" y="2" width="20" height="20" rx="6"></rect>
                                <circle cx="12" cy="12" r="4"></circle>
                            </svg>
                            <p class="text-muted small mb-3">Chọn ảnh hoặc video ngắn (dưới 30 giây)</p>
                            <label class="btn btn-pink rounded-pill px-4">
                                Chọn tệp
                                <input type="file" id="storyFileInput" hidden @change="handleStoryUpload" accept="image/*,video/*" />
                            </label>
                        </div>
                        <div v-else class="story-preview-wrap position-relative">
                            <img v-if="isStoryImg" :src="storyPreview" class="rounded shadow-sm" style="max-height: 380px; width: 100%; object-fit: cover;" />
                            <video v-if="isStoryVideo" :src="storyPreview" controls autoplay class="rounded shadow-sm" style="max-height: 380px; width: 100%;"></video>
                            <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 rounded-circle" @click="storyPreview = null; currentStoryFile = null;">
                                ✕
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-pink rounded-pill px-4" :disabled="!currentStoryFile || isSubmittingStory" @click="submitStory">
                            <span v-if="isSubmittingStory" class="spinner-border spinner-border-sm me-1"></span> Đăng Story
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. CREATE POST MODAL -->
        <div class="modal fade" id="uploadModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content modern-modal">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold w-100 text-center text-pink">Tạo Bài Viết Mới</h6>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div v-if="!previewUrl" class="upload-dropzone p-5 text-center">
                            <div class="dropzone-icon mb-3">
                                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark">Kéo thả ảnh và video vào đây</h5>
                            <p class="text-muted small mb-4">Hỗ trợ các định dạng JPG, PNG, MP4</p>
                            <label class="btn btn-pink rounded-pill px-4 py-2">
                                Chọn từ máy tính
                                <input type="file" hidden @change="handleFileUpload" accept="image/*,video/*" />
                            </label>
                        </div>

                        <div v-else class="post-create-layout d-flex flex-column flex-md-row">
                            <div class="post-create-media col-md-7 p-2 bg-black d-flex align-items-center justify-content-center position-relative">
                                <img v-if="isImagePost" :src="previewUrl" class="img-fluid" style="max-height: 450px; object-fit: contain;" />
                                <video v-if="isVideoPost" :src="previewUrl" class="img-fluid" style="max-height: 450px;" controls></video>
                                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 rounded-circle" @click="previewUrl = null; uploadFile = null;">
                                    ✕
                                </button>
                            </div>
                            <div class="post-create-form col-md-5 p-3 d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <img :src="currentUser?.avatar_url || getAvatarUrl(currentUser?.user_name)" class="rounded-circle" width="36" height="36" />
                                    <span class="fw-bold small">{{ currentUser?.full_name || currentUser?.user_name }}</span>
                                </div>
                                <textarea 
                                    class="form-control border-0 flex-grow-1 p-0 shadow-none mb-3" 
                                    rows="6" 
                                    placeholder="Viết chú thích cho bài viết của bạn..." 
                                    v-model="caption"
                                    style="resize: none;"
                                ></textarea>
                                
                                <!-- Quick Moods / Hashtags -->
                                <div class="quick-moods d-flex flex-wrap gap-1 mb-2">
                                    <span class="badge bg-pink-light text-pink cursor-pointer" @click="caption += ' #happy'">#happy</span>
                                    <span class="badge bg-pink-light text-pink cursor-pointer" @click="caption += ' #lifestyle'">#lifestyle</span>
                                    <span class="badge bg-pink-light text-pink cursor-pointer" @click="caption += ' #vibes'">#vibes</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top text-muted small">
                                    <span>{{ caption.length }}/2,200</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-pink rounded-pill px-4" @click="addPost" :disabled="(!caption.trim() && !uploadFile) || isSubmittingPost">
                            <span v-if="isSubmittingPost" class="spinner-border spinner-border-sm me-1"></span> Đăng bài
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. COMMENTS OFFCANVAS / DRAWER -->
        <div class="offcanvas offcanvas-end modern-comment-drawer" tabindex="-1" id="commentPanel">
            <div class="offcanvas-header border-bottom py-3 px-4">
                <h6 class="offcanvas-title fw-bold d-flex align-items-center gap-2 m-0 fs-5 text-pink">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                    Bình luận
                </h6>
                <button class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body p-0 d-flex flex-column" v-if="currentPost">
                <!-- Post Summary Header in Comments -->
                <div class="p-3 border-bottom bg-pink-light d-flex align-items-center gap-3">
                    <img :src="currentPost.author_avatar || getAvatarUrl(currentPost.author_fullname || 'Author')" class="rounded-circle" width="40" height="40" style="object-fit: cover;" />
                    <div class="overflow-hidden">
                        <strong class="d-block text-truncate small text-dark">{{ currentPost.author_fullname }}</strong>
                        <p class="text-muted small m-0 text-truncate">{{ currentPost.caption }}</p>
                    </div>
                </div>

                <!-- Comments List -->
                <div class="comments-scroll-area flex-grow-1 overflow-auto p-3">
                    <div v-if="loadingComment" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-pink me-2"></div> Đang tải bình luận...
                    </div>

                    <div v-else-if="comments.length === 0" class="text-center py-5 text-muted">
                        <svg class="mb-2 opacity-50" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f472b6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span class="d-block small">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</span>
                    </div>

                    <div v-else class="comment-bubbles d-flex flex-column gap-3">
                        <div v-for="c in comments" :key="c.id" class="comment-row d-flex gap-2">
                            <img :src="c.user?.avatar_url || getAvatarUrl(c.user?.full_name || 'User')" class="rounded-circle mt-1" width="34" height="34" style="object-fit: cover;" />
                            <div class="comment-content-box bg-light rounded-4 p-2 px-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="small text-dark">{{ c.user?.full_name || c.user?.user_name }}</strong>
                                    <small class="text-muted" style="font-size: 10px;">{{ formatTime(c.created_at) }}</small>
                                </div>
                                <p class="small text-secondary m-0">{{ c.comment }}</p>

                                <!-- Replies if any -->
                                <div class="nested-replies mt-2" v-if="c.children && c.children.length > 0">
                                    <div v-for="r in c.children" :key="r.id" class="d-flex gap-2 mt-2 pt-2 border-top">
                                        <img :src="r.user?.avatar_url || getAvatarUrl(r.user?.full_name || 'User')" class="rounded-circle" width="24" height="24" />
                                        <div>
                                            <strong class="small text-dark">{{ r.user?.full_name }}</strong>
                                            <p class="small text-secondary m-0">{{ r.comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Comment Input Footer -->
                <div class="comment-input-footer p-3 border-top bg-white">
                    <div class="d-flex align-items-center gap-2">
                        <input 
                            v-model="commentText" 
                            @keyup.enter="sendComment" 
                            type="text"
                            class="form-control rounded-pill border bg-light px-3 py-2 small" 
                            placeholder="Thêm bình luận cho bài viết..."
                        />
                        <button class="btn btn-pink rounded-circle p-2 px-3" :disabled="!commentText.trim()" @click="sendComment">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#fff">
                                <line x1="22" y1="2" x2="11" y2="13" stroke="#fff" stroke-width="2"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. CHAT WIDGET -->
        <ChatWidget />
    </div>
</template>

<script>
import SidebarComponent from '@/components/SidebarComponent.vue';
import api from "../api/client";
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

            // STORY UPLOAD
            currentStoryFile: null,
            storyPreview: null,
            isStoryImg: false,
            isStoryVideo: false,
            isSubmittingStory: false,

            // STORY VIEWER
            rawStories: [],
            groupedStories: [],
            isViewingStory: false,
            activeUserIndex: 0,
            activeStoryIndex: 0,
            progressPercent: 0,
            storyTimer: null,
            storyReplyText: "",
            isPausedForInput: false,

            // POST UPLOAD
            uploadFile: null,
            previewUrl: null,
            caption: "",
            isImagePost: false,
            isVideoPost: false,
            isSubmittingPost: false,

            // COMMENT / POST DETAIL
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

        window.addEventListener('keydown', this.handleKeyDown);
    },

    beforeUnmount() {
        this.stopImageTimer();
        window.removeEventListener('keydown', this.handleKeyDown);
    },

    methods: {
        getAvatarUrl(name) {
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'User')}&background=db2777&color=fff&bold=true&rounded=true`;
        },

        handleKeyDown(e) {
            if (!this.isViewingStory) return;
            if (e.key === 'ArrowRight') this.nextStory();
            if (e.key === 'ArrowLeft') this.prevStory();
            if (e.key === 'Escape') this.closeStoryViewer();
        },

        // --- DOUBLE TAP TO LIKE ---
        onDoubleTapLike(post) {
            post.showHeartAnim = true;
            if (!post.isLiked) {
                this.likePost(post);
            }
            setTimeout(() => {
                post.showHeartAnim = false;
            }, 800);
        },

        // --- SHARE POST (COPY LINK) ---
        sharePost(post) {
            const shareUrl = `${window.location.origin}/profile/${post.user_id}`;
            navigator.clipboard.writeText(shareUrl).then(() => {
                alert("Đã sao chép liên kết bài viết vào bộ nhớ tạm!");
            }).catch(() => {
                alert("Chia sẻ: " + shareUrl);
            });
        },

        // --- INLINE QUICK COMMENT ---
        async sendQuickComment(post) {
            if (!post.quickCommentText || !post.quickCommentText.trim()) return;
            const text = post.quickCommentText.trim();
            post.quickCommentText = "";
            try {
                const res = await api.post("/api/post/comment", {
                    post_id: post.id,
                    user_id: this.currentUser.id,
                    comment: text
                });
                if (res.status === 200) {
                    post.total_comment = (post.total_comment || 0) + 1;
                }
            } catch (error) {
                console.error("Lỗi gửi bình luận nhanh:", error);
            }
        },

        // --- 1. STORY LOGIC ---
        async listStory() {
            try {
                const res = await api.get('/api/post/list-story', { params: { user_id: this.currentUser.id } });
                if (res.status === 200 && res.data.data) {
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
            document.querySelector('.story-input-box input')?.blur();
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

        // --- 2. UPLOAD STORY ---
        openStoryModal() {
            this.closeStoryViewer(); this.currentStoryFile = null; this.storyPreview = null;
            const input = document.getElementById('storyFileInput');
            if (input) input.value = '';
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

        // --- 3. UPLOAD POST ---
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

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
            if (!this.uploadFile && !this.caption.trim()) {
                alert("Vui lòng chọn file hoặc nhập chú thích");
                return;
            }

            this.isSubmittingPost = true;
            const formData = new FormData();
            if (this.uploadFile) {
                formData.append("thumbnail", this.uploadFile);
            }
            formData.append("caption", this.caption);
            formData.append("user_id", this.currentUser.id);

            try {
                const res = await api.post("/api/post/add-post", formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (res.status === 200 || res.status === 201) {
                    alert("Đăng bài viết thành công!");
                    this.caption = "";
                    this.uploadFile = null;
                    this.previewUrl = null;

                    const modalEl = document.getElementById('uploadModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    setTimeout(() => {
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = 'auto';
                    }, 350);

                    this.listPost();
                }
            } catch (err) {
                console.error(err);
                alert("Lỗi upload bài viết");
            } finally {
                this.isSubmittingPost = false;
            }
        },

        listPost() {
            api.get('/api/post/list-post', { params: { user_id: this.currentUser.id } }).then(res => {
                if (res.data && res.data.data) {
                    this.list_posts = res.data.data.map(p => ({
                        ...p,
                        isLiked: !!p.isLiked,
                        showHeartAnim: false,
                        quickCommentText: ""
                    }));
                }
            }).catch(e => console.error(e));
        },

        async likePost(post) {
            const oldState = post.isLiked;
            const oldCount = post.total_like || 0;
            post.isLiked = !oldState;
            post.total_like = post.isLiked ? oldCount + 1 : Math.max(0, oldCount - 1);

            try {
                const res = await api.post('/api/post/like-post', {
                    post_id: post.id,
                    user_id: this.currentUser.id
                });
                if (res.status === 200 && res.data && res.data.data) {
                    post.total_like = res.data.data.total_like;
                }
            } catch (err) {
                console.error("Lỗi like post:", err);
                post.isLiked = oldState;
                post.total_like = oldCount;
            }
        },

        async savePost(post) {
            const oldState = post.isSaved;
            post.isSaved = !oldState;
            try {
                const res = await api.post('/api/post/save-post', { post_id: post.id, user_id: this.currentUser.id });
                if (res.status === 200 && res.data && res.data.data) {
                    post.isSaved = res.data.data.saved;
                }
            } catch (err) { 
                console.error("Lỗi save post:", err);
                post.isSaved = oldState;
            }
        },

        async openComment(post) {
            this.currentPost = post;
            this.comments = [];
            this.loadingComment = true;
            try {
                const res = await api.get("/api/post/list-comment", { params: { post_id: post.id } });
                if (res.status === 200 && res.data && res.data.data) {
                    this.comments = res.data.data.comments || [];
                }
            } catch (err) { console.error(err); }
            finally { this.loadingComment = false; }
        },

        async sendComment() {
            if (!this.commentText.trim()) return;
            const text = this.commentText.trim();
            this.commentText = "";
            try {
                const res = await api.post("/api/post/comment", {
                    post_id: this.currentPost.id, 
                    user_id: this.currentUser.id, 
                    comment: text
                });
                if (res.status === 200 && res.data && res.data.data) {
                    this.comments.unshift(res.data.data.comment);
                    this.currentPost.total_comment = (this.currentPost.total_comment || 0) + 1;
                }
            } catch (error) { console.error(error); }
        },

        async suggestFriend() {
            this.loadingSuggest = true;
            try {
                const res = await api.get('/api/post/suggest-friend', {
                    params: { user_id: this.currentUser.id }
                });
                if (res.status === 200 && res.data.data) {
                    this.suggest_friends = (res.data.data.users || []).map(u => ({ ...u, is_followed: false }));
                }
            } catch (err) {
                console.error("Lỗi tải gợi ý:", err);
            } finally {
                this.loadingSuggest = false;
            }
        },

        async followUser(targetUser) {
            if (!targetUser || !targetUser.id) return;
            targetUser.is_followed = !targetUser.is_followed;
            try {
                const res = await api.post('/api/post/follow', {
                    following_id: targetUser.id,
                    user_id: this.currentUser.id
                });
            } catch (err) {
                console.error("Lỗi follow:", err);
                targetUser.is_followed = !targetUser.is_followed;
            }
        },

        async logout() {
            this.$router.push("/login");
            try {
                await signOut(auth);
                await api.post('/api/auth/logout', { user_id: this.currentUser.id });
            } catch (e) { console.warn(e); }
            sessionStorage.removeItem("user_info");
            sessionStorage.removeItem("access_token");
            sessionStorage.removeItem("token");
            localStorage.removeItem("access_token");
            localStorage.removeItem("user_info");
        },

        formatTime(t) {
            if (!t) return "";
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

/* ==========================================================================
   GLOBAL APP LAYOUT - PINK ROSE THEME
   ========================================================================== */
.app-layout {
    display: flex;
    min-height: 100vh;
    background-color: #fdf8fa;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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

.btn-pink {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: #ffffff;
    border: none;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(236, 72, 153, 0.35);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-pink:hover {
    background: linear-gradient(135deg, #f43f5e, #ec4899);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(236, 72, 153, 0.45);
}

.btn-pink:active {
    transform: translateY(0);
}

.btn-pink:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* ==========================================================================
   MAIN FEED AREA
   ========================================================================== */
.feed-main {
    flex: 1;
    display: flex;
    justify-content: center;
    padding: 24px 16px;
}

.feed-container {
    width: 100%;
    max-width: 630px;
}

/* ==========================================================================
   STORIES SECTION
   ========================================================================== */
.stories-section {
    background: #ffffff;
    border: 1px solid #fce7f3;
    border-radius: 18px;
    padding: 16px 14px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(219, 39, 119, 0.04);
}

.stories-scroll {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    scrollbar-width: none;
    padding: 2px;
}

.stories-scroll::-webkit-scrollbar {
    display: none;
}

.story-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 68px;
    cursor: pointer;
    flex-shrink: 0;
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.story-item:hover {
    transform: scale(1.06);
}

.story-ring {
    width: 62px;
    height: 62px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 6px;
}

.unread-story-ring {
    background: linear-gradient(45deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);
    padding: 2.5px;
    box-shadow: 0 4px 12px rgba(236, 72, 153, 0.25);
}

.story-ring-inner {
    background: #ffffff;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.story-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.add-story-ring {
    border: 2px dashed #f472b6;
    padding: 2px;
}

.add-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 22px;
    height: 22px;
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(219, 39, 119, 0.3);
}

.story-name {
    font-size: 11px;
    font-weight: 500;
    color: #831843;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}

/* ==========================================================================
   CREATE POST QUICK CARD
   ========================================================================== */
.create-post-card {
    background: #ffffff;
    border: 1px solid #fce7f3;
    border-radius: 18px;
    padding: 16px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(219, 39, 119, 0.04);
}

.user-avatar-sm {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid #ec4899;
}

.quick-input-trigger {
    background: #fdf2f8;
    padding: 10px 18px;
    border-radius: 999px;
    color: #9d174d;
    font-size: 14px;
    cursor: pointer;
    border: 1px solid #fce7f3;
    transition: all 0.2s;
}

.quick-input-trigger:hover {
    background: #fce7f3;
    color: #831843;
    border-color: #fbcfe8;
}

.quick-action-btn {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    padding: 6px 14px;
    border-radius: 10px;
    transition: background 0.2s;
}

.quick-action-btn:hover {
    background: #fdf2f8;
    color: #db2777;
}

/* ==========================================================================
   POST CARDS
   ========================================================================== */
.post-card {
    background: #ffffff;
    border: 1px solid #fce7f3;
    border-radius: 18px;
    margin-bottom: 24px;
    box-shadow: 0 4px 24px rgba(219, 39, 119, 0.04);
    overflow: hidden;
}

.post-card-header {
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.post-avatar-wrap {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    padding: 2px;
    background: linear-gradient(135deg, #fbcfe8, #f472b6);
}

.post-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid #fff;
}

.post-author-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    text-decoration: none;
}

.post-author-name:hover {
    color: #db2777;
    text-decoration: underline;
}

.post-time {
    font-size: 12px;
    color: #94a3b8;
}

.btn-post-options {
    background: none;
    border: none;
    color: #64748b;
    font-size: 18px;
    padding: 4px 8px;
    border-radius: 8px;
}

.btn-post-options:hover {
    background: #fdf2f8;
    color: #db2777;
}

.caption-text {
    font-size: 14px;
    line-height: 1.55;
    color: #1e293b;
    word-break: break-word;
}

.post-media-box {
    width: 100%;
    max-height: 580px;
    background: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.post-media {
    width: 100%;
    max-height: 580px;
    object-fit: contain;
    display: block;
}

/* Double tap heart anim */
.double-tap-heart {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.heart-pop-enter-active {
    animation: heartPopIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes heartPopIn {
    0% { opacity: 0; transform: translate(-50%, -50%) scale(0.3); }
    50% { opacity: 1; transform: translate(-50%, -50%) scale(1.2); }
    100% { opacity: 0; transform: translate(-50%, -50%) scale(1); }
}

/* Actions Bar */
.post-actions-bar {
    padding: 10px 14px 4px 14px;
    display: flex;
    align-items: center;
}

.action-btn {
    background: none;
    border: none;
    padding: 8px;
    font-size: 22px;
    color: #475569;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.action-btn:hover {
    transform: scale(1.15);
    background: #fdf2f8;
    color: #db2777;
}

.anim-pop {
    animation: heartBeat 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    50% { transform: scale(1.35); }
    100% { transform: scale(1); }
}

.likes-count {
    font-size: 14px;
    color: #0f172a;
}

.view-comments-link {
    font-size: 13px;
    color: #9d174d;
    cursor: pointer;
    margin-top: 4px;
    display: inline-block;
    font-weight: 500;
}

.view-comments-link:hover {
    color: #db2777;
    text-decoration: underline;
}

.quick-comment-input {
    border-radius: 999px;
    border: 1px solid #fbcfe8;
    padding: 8px 16px;
    font-size: 13px;
    background: #fdf8fa;
    color: #831843;
}

.quick-comment-input:focus {
    background: #ffffff;
    border-color: #ec4899;
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
}

/* Empty feed */
.empty-icon-wrap {
    width: 68px;
    height: 68px;
    background: #fdf2f8;
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ==========================================================================
   RIGHT WIDGETS SIDEBAR
   ========================================================================= */
.feed-aside {
    width: 340px;
    padding: 24px 20px 24px 0;
    flex-shrink: 0;
}

.aside-sticky-container {
    position: sticky;
    top: 24px;
}

.user-switch-card, .suggestions-card {
    background: #ffffff;
    border: 1px solid #fce7f3;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(219, 39, 119, 0.04);
}

.user-avatar-md {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid #ec4899;
}

.btn-switch-account {
    background: none;
    border: none;
    color: #f43f5e;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    transition: background 0.2s;
}

.btn-switch-account:hover {
    background: #ffe4e6;
}

.btn-follow-sm {
    background: #fdf2f8;
    color: #db2777;
    border: 1px solid #fbcfe8;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 999px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-follow-sm:hover {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 2px 8px rgba(219, 39, 119, 0.3);
}

.btn-followed {
    background: #f1f5f9 !important;
    color: #64748b !important;
    border-color: #e2e8f0 !important;
    box-shadow: none !important;
}

.footer-links a {
    color: #94a3b8;
    text-decoration: none;
}

.footer-links a:hover {
    color: #db2777;
    text-decoration: underline;
}

/* ==========================================================================
   MODERN OFFCANVAS & MODALS
   ========================================================================== */
.modern-comment-drawer {
    width: 420px;
    box-shadow: -6px 0 30px rgba(219, 39, 119, 0.08);
    border-left: 1px solid #fce7f3;
}

.modern-modal {
    border-radius: 20px;
    overflow: hidden;
    border: none;
    box-shadow: 0 20px 40px rgba(219, 39, 119, 0.15);
}

.dropzone-icon {
    font-size: 54px;
    color: #ec4899;
}

.cursor-pointer {
    cursor: pointer;
}

/* ==========================================================================
   STORY VIEWER OVERLAY
   ========================================================================== */
.story-viewer-overlay {
    position: fixed;
    inset: 0;
    background: #0a0f1d;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.viewer-brand {
    position: absolute;
    top: 20px;
    left: 24px;
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.5px;
    background: linear-gradient(135deg, #ec4899, #db2777);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.close-viewer-btn {
    position: absolute;
    top: 20px;
    right: 24px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: #ffffff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.close-viewer-btn:hover {
    background: rgba(236, 72, 153, 0.4);
    transform: rotate(90deg);
}

.viewer-carousel-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 36px;
    width: 100%;
    height: 100%;
    padding: 40px 0;
}

.active-story-card {
    width: 100%;
    max-width: 400px;
    height: 90vh;
    background: #000000;
    border-radius: 18px;
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8);
}

.story-progress-bar {
    position: absolute;
    top: 12px;
    left: 12px;
    right: 12px;
    display: flex;
    gap: 4px;
    z-index: 10;
}

.progress-segment {
    flex: 1;
    height: 3px;
    background: rgba(255, 255, 255, 0.35);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #ec4899, #f43f5e);
    transition: width 0.05s linear;
}

.story-author-header {
    position: absolute;
    top: 24px;
    left: 14px;
    right: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10;
}

.story-author-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ec4899;
}

.story-author-info {
    display: flex;
    flex-direction: column;
}

.story-author-name {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
}

.story-time-stamp {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.8);
}

.story-display-box {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.story-media-fit {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.tap-zone {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 40%;
    z-index: 5;
    cursor: pointer;
}

.tap-left { left: 0; }
.tap-right { right: 0; }

.story-interactive-footer {
    position: absolute;
    bottom: 14px;
    left: 14px;
    right: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10;
}

.story-input-box {
    flex: 1;
}

.story-input-box input {
    width: 100%;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 999px;
    padding: 8px 16px;
    color: #ffffff;
    font-size: 13px;
    outline: none;
}

.story-input-box input:focus {
    border-color: #ec4899;
}

.story-input-box input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.btn-story-like, .btn-send-reply {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.15s;
}

.btn-story-like:hover, .btn-send-reply:hover {
    background: rgba(236, 72, 153, 0.3);
    border-color: #ec4899;
}

.story-preview-tile {
    width: 180px;
    height: 320px;
    background: #1e293b;
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    opacity: 0.45;
    cursor: pointer;
    transition: all 0.25s;
    position: relative;
}

.story-preview-tile:hover {
    opacity: 0.85;
    transform: scale(1.05);
}

.tile-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ec4899;
}

.tile-name {
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
}

.tile-arrow {
    position: absolute;
    width: 32px;
    height: 32px;
    background: rgba(236, 72, 153, 0.4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
}

.left-tile .tile-arrow { left: -16px; }
.right-tile .tile-arrow { right: -16px; }
.placeholder-tile { visibility: hidden; }
</style>