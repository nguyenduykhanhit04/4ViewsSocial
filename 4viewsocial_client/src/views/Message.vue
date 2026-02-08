<template>
  <div class="col-md-12">
    <div class="row">
      <SidebarComponent />

      <div class="col-md-3 border-end p-4 d-flex flex-column" style="height: 100vh;">
        
        <div v-if="!isSearching" class="d-flex flex-column h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                 <h5 class="fw-bold mb-0">Tin nhắn</h5>
                 <i class="bi bi-pencil-square fs-4 cursor-pointer" 
                    title="Tin nhắn mới / Tìm kiếm" 
                    @click="enableSearchMode"></i>
            </div>

            <div v-if="isLoadingList" class="text-center py-3">
              <div class="spinner-border spinner-border-sm text-secondary"></div>
            </div>

            <div v-else-if="chatList.length > 0" class="chat-list-container flex-grow-1 overflow-auto custom-scrollbar">
                <div v-for="item in chatList" :key="item.id"
                    class="d-flex align-items-center p-2 chat-list-item border rounded mb-2"
                    :class="{ 'bg-light': activeChat && activeChat.id === item.id }"
                    @click="openChat(item)">
                
                    <img :src="item.avatar || 'https://via.placeholder.com/50'" 
                         class="rounded-circle me-3" width="50" height="50" 
                         style="object-fit: cover; border: 1px solid #eee;"/>
                    
                    <div class="w-100 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center">
                            <b class="text-truncate" style="max-width: 120px;">{{ item.name }}</b>
                            <small class="text-muted" style="font-size: 10px">{{ formatTimeList(item.updated_at) }}</small>
                        </div>
                        <div class="text-muted small text-truncate" :class="{'fw-bold': !item.isRead}">
                            {{ item.me ? 'Bạn: ' : '' }}{{ item.lastMessage }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="suggestion-container flex-grow-1 overflow-auto">
                <p class="text-muted small mb-3 fw-bold ps-1">Gợi ý chat với bạn bè:</p>
                <div v-if="suggestedUsers.length === 0" class="text-center mt-5 text-muted">
                    <small>Bạn chưa theo dõi ai cả.<br>Hãy tìm kiếm bạn bè nhé!</small>
                </div>
                <div v-else v-for="user in suggestedUsers" :key="user.id" 
                     class="d-flex align-items-center mb-2 cursor-pointer suggestion-item p-2 rounded"
                     @click="startNewChat(user)">
                    <img :src="user.avatar_url || 'https://via.placeholder.com/40'" 
                         class="rounded-circle me-3" width="40" height="40" style="object-fit: cover"/>
                    <div class="flex-grow-1">
                        <div class="fw-bold small">{{ user.user_name }}</div>
                        <div class="text-muted small" style="font-size: 11px">{{ user.full_name }}</div>
                    </div>
                    <i class="bi bi-chat-dots text-primary fs-5"></i>
                </div>
            </div>
        </div>

        <div v-else class="d-flex flex-column h-100 animate-slide-in">
            <div class="mb-3 border-bottom pb-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-arrow-left fs-4 me-3 cursor-pointer" @click="disableSearchMode"></i>
                    <h6 class="fw-bold mb-0">Tin nhắn mới</h6>
                </div>
                <div class="d-flex align-items-center bg-light rounded-pill px-3 py-2 border">
                     <span class="text-muted me-2 small">Đến:</span>
                     <input v-model="searchQuery" @input="handleSearchInput" ref="searchInputRef"
                            type="text" class="form-control form-control-sm border-0 bg-transparent p-0 shadow-none" 
                            placeholder="Tìm kiếm..." style="outline: none;">
                </div>
            </div>
            <div class="flex-grow-1 overflow-auto chat-list-container">
                <div class="small fw-bold text-muted mb-2">{{ searchQuery ? 'Kết quả tìm kiếm' : 'Gợi ý' }}</div>
                <div v-if="isSearchingAPI" class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>
                <div v-else-if="userResults.length === 0" class="text-center text-muted mt-3"><small>Không tìm thấy.</small></div>
                <div v-else v-for="user in userResults" :key="user.id" 
                     class="d-flex align-items-center mb-2 cursor-pointer suggestion-item p-2 rounded"
                     @click="onSelectContact(user)">
                    <img :src="user.avatar_url || 'https://via.placeholder.com/50'" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover"/>
                    <div class="flex-grow-1">
                        <div class="fw-bold small">{{ user.user_name || user.full_name }}</div>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="col-md-7 p-4">
        <div v-if="activeChat" class="d-flex align-items-center border-bottom pb-3 mb-3">
          <img :src="activeChat.avatar || 'https://via.placeholder.com/50'" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;"/>
          <h5 class="fw-bold mb-0">{{ activeChat.name }}</h5>
        </div>

        <div v-if="activeChat" class="chat-container mb-3 custom-scrollbar" ref="chatContainerRef">
            <div v-if="isLoadingMessages" class="text-center mt-3"><div class="spinner-border spinner-border-sm"></div></div>
            
            <div v-else class="d-flex flex-column justify-content-end min-h-100">
                <div v-for="(msg, index) in activeChat.messages" :key="index">
                    
                    <div v-if="shouldShowTime(index)" class="text-center my-3">
                        <small class="text-muted" style="font-size: 11px;">{{ formatMessageTime(msg.created_at) }}</small>
                    </div>

                    <div class="message-row fade-in-up" 
                         :class="msg.me ? 'justify-content-end' : 'justify-content-start'"
                         :style="{ marginBottom: shouldMergeMessage(index) ? '2px' : '10px' }">
                        
                        <img v-if="!msg.me" 
                             :src="msg.senderAvatar || activeChat.avatar" 
                             class="avatar-xs-chat me-2" 
                             :style="{ visibility: shouldShowAvatar(index) ? 'visible' : 'hidden' }" />
                        
                        <div :class="msg.me ? 'chat-bubble-me' : 'chat-bubble-other'" :title="formatTooltipTime(msg.created_at)">
                            {{ msg.text }}
                            <div v-if="msg.attachments && msg.attachments.length > 0">
                                 <img v-for="(img, idx) in msg.attachments" :key="idx" 
                                      :src="img.url" class="img-fluid rounded mt-2" style="max-width: 200px"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeChat" class="d-flex mt-3">
          <input v-model="messageText" @keyup.enter="sendMessage" type="text" class="form-control me-2" placeholder="Nhắn tin..." />
          <button class="btn btn-primary" @click="sendMessage">Gửi</button>
        </div>

        <div v-else class="text-center h-100 d-flex flex-column justify-content-center align-items-center text-muted">
            <div class="mb-3 p-4 rounded-circle bg-light border"><i class="bi bi-send fs-1"></i></div>
            <h4>Tin nhắn của bạn</h4>
            <p>Chọn một người bạn từ danh sách để bắt đầu trò chuyện.</p>
            <button class="btn btn-primary btn-sm" @click="enableSearchMode">Tìm bạn bè</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router'; // 🔥 Import useRouter
import SidebarComponent from '@/components/SidebarComponent.vue';
import api from '@/api/client'; 
import { io } from "socket.io-client"; 

const route = useRoute();
const router = useRouter(); // 🔥 Sử dụng router
const userInfo = JSON.parse(sessionStorage.getItem("user_info")) || { id: "User_ID_1" };

// --- STATE ---
const messageText = ref("");
const chatList = ref([]);
const suggestedUsers = ref([]);
const activeChat = ref(null);
const isLoadingList = ref(false);
const isLoadingMessages = ref(false);
const chatContainerRef = ref(null);

// Search State
const isSearching = ref(false);       
const searchQuery = ref("");
const searchResults = ref([]);        
const isSearchingAPI = ref(false);
const searchInputRef = ref(null);
let searchTimeout = null;
let socket = null;

// --- COMPUTED ---
const userResults = computed(() => {
    if (!searchQuery.value || searchQuery.value.trim() === "") return suggestedUsers.value;
    return searchResults.value;
});

// 🔥 WATCH URL: Nếu người dùng bấm back/forward hoặc đổi link, tự động chuyển chat
watch(
  () => route.query.partnerId,
  (newPartnerId) => {
    if (newPartnerId) {
        // Tìm và mở chat
        const targetChat = chatList.value.find(c => String(c.partnerId) === String(newPartnerId));
        if (targetChat) openChat(targetChat);
    } else {
        // Nếu không có partnerId thì về trạng thái list
        activeChat.value = null;
    }
  }
);

// 🔥 WATCH MSG: Scroll to bottom
watch(
  () => activeChat.value?.messages, 
  () => { scrollToBottom(); }, 
  { deep: true }
);

// --- LIFECYCLE ---
onMounted(async () => {
    initSocket();
    await fetchChatList();
    await fetchSuggestions();

    // Check URL lần đầu
    if (route.query.partnerId) {
        const partnerIdFromUrl = String(route.query.partnerId);
        const targetChat = chatList.value.find(c => String(c.partnerId) === partnerIdFromUrl);
        if (targetChat) openChat(targetChat);
        else await handleOpenChatFromUrl(partnerIdFromUrl);
    }
});

onBeforeUnmount(() => { if (socket) socket.disconnect(); });

// --- UTILS HELPER ---
const shouldShowTime = (index) => {
    if (index === 0) return true;
    const currentMsg = activeChat.value.messages[index];
    const prevMsg = activeChat.value.messages[index - 1];
    if(!currentMsg.created_at || !prevMsg.created_at) return false;
    const diff = new Date(currentMsg.created_at) - new Date(prevMsg.created_at);
    return diff > 15 * 60 * 1000;
};

const shouldMergeMessage = (index) => {
    if (index >= activeChat.value.messages.length - 1) return false;
    const currentMsg = activeChat.value.messages[index];
    const nextMsg = activeChat.value.messages[index + 1];
    return currentMsg.me === nextMsg.me;
};

const shouldShowAvatar = (index) => {
    const messages = activeChat.value.messages;
    if (index === messages.length - 1) return true;
    const currentMsg = messages[index];
    const nextMsg = messages[index + 1];
    return currentMsg.me !== nextMsg.me;
};

// --- METHODS CHAT ---
const openChat = async (chat) => {
    // 🔥 Cập nhật URL khi mở chat
    router.push({ path: '/message', query: { partnerId: chat.partnerId } });

    if (activeChat.value && activeChat.value.id === chat.id) return;
    
    activeChat.value = { ...chat, messages: [] };
    if (socket) socket.emit("join_room", chat.id);
    
    isLoadingMessages.value = true;
    try {
        const res = await api.post('/api/chat/get-messages', { conversation_id: chat.id, limit: 50 });
        activeChat.value.messages = (res.data.data || []).map(msg => ({
            text: msg.content,
            me: String(msg.sender_id) === String(userInfo.id), 
            attachments: msg.attachments || [],
            senderAvatar: msg.sender_info ? msg.sender_info.avatar : null,
            created_at: msg.created_at
        }));
        scrollToBottom();
    } catch (e) { console.error("Lỗi get-messages:", e); } 
    finally { isLoadingMessages.value = false; }
};

const backToList = () => {
    activeChat.value = null;
    // 🔥 Xóa query param khi back
    router.push({ path: '/message' });
    fetchChatList();
};

const handleOpenChatFromUrl = async (partnerId) => {
    try {
        const res = await api.post('/api/post/get-users-info', { user_ids: [partnerId] });
        let user = null;
        if (res.data.data && res.data.data.users) user = res.data.data.users[0];
        else if (Array.isArray(res.data.data)) user = res.data.data[0];
        if (user) await startNewChat(user);
    } catch (e) { console.error(e); }
};

const sendMessage = async () => {
    if (!messageText.value.trim()) return;
    const content = messageText.value;
    const currentChatId = activeChat.value.id;
    
    activeChat.value.messages.push({
        text: content, me: true, attachments: [], created_at: new Date().toISOString()
    });
    scrollToBottom();
    messageText.value = ""; 

    try {
        await api.post('/api/chat/send-message', {
            conversation_id: currentChatId,
            sender_id: userInfo.id,
            content: content,
            sender_info: { name: userInfo.user_name, avatar: userInfo.avatar_url }
        });
        
        const chatItem = chatList.value.find(c => c.id === currentChatId);
        if(chatItem) {
            chatItem.lastMessage = content;
            chatItem.me = true;
            chatItem.updated_at = new Date().toISOString();
            chatList.value.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        }
    } catch (e) { console.error("Gửi lỗi:", e); }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainerRef.value) {
            chatContainerRef.value.scrollTop = chatContainerRef.value.scrollHeight;
        }
    });
};

const initSocket = () => {
    socket = io("http://localhost:8002"); 
    socket.on("receive_message", (newMsg) => {
        
        // KIỂM TRA: Nếu tin nhắn đến từ chính mình (người đang đăng nhập) thì KHÔNG push vào mảng messages nữa
        // Vì ở hàm sendMessage bạn đã push rồi.
        const isMyMessage = String(newMsg.sender_id) === String(userInfo.id);

        if (activeChat.value && activeChat.value.id === newMsg.conversation_id) {
            // Chỉ push vào nếu KHÔNG PHẢI là tin nhắn của mình
            if (!isMyMessage) {
                activeChat.value.messages.push({
                    text: newMsg.content,
                    me: false, // Vì !isMyMessage nên chắc chắn là false
                    attachments: newMsg.attachments || [],
                    created_at: newMsg.created_at
                });
                scrollToBottom();
            }
        }

        // Phần cập nhật Sidebar (Tin nhắn cuối, giờ gửi) thì VẪN PHẢI CHẠY 
        // để cập nhật lại thứ tự chat list dù là tin mình vừa gửi
        const chatItem = chatList.value.find(c => c.id === newMsg.conversation_id);
        if (chatItem) {
            chatItem.lastMessage = newMsg.content;
            chatItem.me = isMyMessage; // Cập nhật trạng thái người gửi cuối
            chatItem.updated_at = new Date().toISOString();
            chatList.value.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        } else {
            // Nếu là cuộc trò chuyện mới chưa có trong list thì fetch lại
            fetchChatList();
        }
    });
};

// --- DATA FETCH ---
const fetchChatList = async () => {
    isLoadingList.value = true;
    try {
        const resChat = await api.post('/api/chat/get-conversations', { user_id: userInfo.id });
        const rawConversations = resChat.data.data || [];
        const myIdStr = String(userInfo.id);
        const partnerIds = [];
        rawConversations.forEach(conv => {
            if (conv.members) {
                const pId = conv.members.find(m => String(m) !== myIdStr);
                if (pId) partnerIds.push(pId);
            }
        });

        let usersMap = [];
        if (partnerIds.length > 0) {
            const resUser = await api.post('/api/post/get-users-info', { user_ids: partnerIds });
            usersMap = (resUser.data && resUser.data.data && resUser.data.data.users) ? resUser.data.data.users : [];
        }

        const mappedList = rawConversations.map(conv => {
            if(!conv.members) return null;
            const partnerId = conv.members.find(m => String(m) !== myIdStr);
            const partnerInfo = usersMap.find(u => String(u.id) === String(partnerId)) || {};
            return {
                id: conv._id, partnerId: partnerId,
                name: partnerInfo.user_name || "Người dùng", 
                avatar: partnerInfo.avatar_url || 'https://via.placeholder.com/50',
                lastMessage: conv.last_message ? conv.last_message.content : "...",
                me: conv.last_message && String(conv.last_message.sender_id) === myIdStr,
                updated_at: conv.updated_at || conv.created_at,
                isRead: true 
            };
        }).filter(i => i !== null);
        
        chatList.value = mappedList.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
    } catch (e) { console.error(e); } finally { isLoadingList.value = false; }
};

const fetchSuggestions = async () => {
    try {
        const res = await api.post('/api/post/suggest-friend-message', { user_id: userInfo.id });
        suggestedUsers.value = (res.data && res.data.data) ? res.data.data : [];
    } catch (e) { console.error(e); }
};

const startNewChat = async (user) => {
    try {
        const res = await api.post('/api/chat/create-conversation', { members: [userInfo.id, user.id], type: 'private' });
        const newConv = res.data.data;
        const newChatItem = {
            id: newConv._id, partnerId: user.id, name: user.user_name, avatar: user.avatar_url,
            lastMessage: "Bắt đầu cuộc trò chuyện", updated_at: new Date().toISOString(), isRead: true, messages: []
        };
        chatList.value.unshift(newChatItem);
        // 🔥 Cập nhật URL luôn
        router.push({ path: '/message', query: { partnerId: user.id } });
        openChat(newChatItem);
    } catch (e) { console.error(e); }
};

// Search Logic
const enableSearchMode = () => { isSearching.value = true; searchQuery.value = ""; nextTick(() => searchInputRef.value?.focus()); };
const disableSearchMode = () => { isSearching.value = false; };
const handleSearchInput = () => {
    if (!searchQuery.value.trim()) { searchResults.value = []; isSearchingAPI.value = false; return; }
    isSearchingAPI.value = true;
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        try {
            const res = await api.post('/api/post/search-user', { keyword: searchQuery.value });
            searchResults.value = (res.data.data && Array.isArray(res.data.data.users)) ? res.data.data.users : [];
        } catch (e) { console.error(e); } finally { isSearchingAPI.value = false; }
    }, 500);
};
const onSelectContact = async (user) => {
    const existingChat = chatList.value.find(chat => String(chat.partnerId) === String(user.id));
    if (existingChat) openChat(existingChat);
    else await startNewChat(user);
    disableSearchMode();
};

// Formats
const formatTimeList = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diff = (now - date) / 1000;
    if (diff < 60) return 'Vừa xong';
    if (diff < 3600) return `${Math.floor(diff / 60)} phút`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} giờ`;
    return date.toLocaleDateString('vi-VN');
};
const formatMessageTime = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' });
};
const formatTooltipTime = (dateString) => new Date(dateString).toLocaleString('vi-VN');
</script>

<style scoped>
.cursor-pointer { cursor: pointer; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 4px; }

/* LAYOUT CHAT */
.chat-container { 
    height: 70vh; 
    overflow-y: auto; 
    padding: 10px; 
}
.min-h-100 { 
    min-height: 100%; 
    display: flex;
    flex-direction: column;
    justify-content: flex-end; /* Đẩy tin nhắn xuống đáy */
}

.message-row { display: flex; align-items: flex-end; }
.justify-content-end { justify-content: flex-end; }

.avatar-xs-chat { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-bottom: 4px; }

.chat-bubble-me {
    background: #0095f6; color: #fff;
    padding: 8px 14px; border-radius: 18px;
    max-width: 70%; font-size: 14px; word-wrap: break-word;
}
.chat-bubble-other {
    background: #efefef; color: #000;
    padding: 8px 14px; border-radius: 18px;
    max-width: 70%; font-size: 14px; word-wrap: break-word;
}

.animate-slide-in { animation: slideIn 0.2s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
.fade-in-up { animation: fadeInUp 0.2s ease-out; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>