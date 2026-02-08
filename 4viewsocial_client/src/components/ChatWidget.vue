<template>
  <div class="chat-toggle" @click="toggleChat">
    <i class="bi bi-chat-dots-fill"></i>
    <span v-if="unread && !isOpen" class="badge"></span>
  </div>

  <div v-if="isOpen" class="chat-box animate-fade-up">
    
    <div class="chat-header">
      <div class="d-flex align-items-center">
        <i v-if="activeChat" class="bi bi-arrow-left me-2 cursor-pointer" @click="backToList"></i>
        <span class="text-truncate fw-bold" style="max-width: 180px;">
            {{ activeChat ? activeChat.name : (isSearching ? 'Tìm kiếm' : 'Tin nhắn') }}
        </span>
      </div>

      <div class="chat-actions">
        <i class="bi bi-arrows-angle-expand" title="Mở toàn màn hình" @click="goToMessagePage"></i>
        <i class="bi bi-x-lg" @click="toggleChat"></i>
      </div>
    </div>

    <div v-if="isLoading" class="d-flex justify-content-center align-items-center flex-grow-1">
        <div class="spinner-border spinner-border-sm text-secondary"></div>
    </div>

    <div v-else-if="activeChat" 
         class="chat-body custom-scrollbar p-2" 
         ref="msgContainer"
         @scroll="handleScroll">
        
        <div v-if="isLoadingMore" class="text-center py-2 w-100">
            <div class="spinner-border spinner-border-sm text-secondary" style="width: 1rem; height: 1rem;"></div>
        </div>

        <div class="d-flex flex-column justify-content-end" style="min-height: 100%;">
            
            <div v-for="(msg, index) in activeChat.messages" :key="index">
                
                <div v-if="shouldShowTime(index)" class="text-center my-3">
                    <small class="text-muted" style="font-size: 10px;">{{ formatMessageTime(msg.created_at) }}</small>
                </div>

                <div class="message-row fade-in-up" 
                     :class="msg.me ? 'justify-content-end' : 'justify-content-start'"
                     :style="{ marginBottom: shouldMergeMessage(index) ? '2px' : '8px' }">
                    
                    <img v-if="!msg.me" 
                         :src="msg.senderAvatar || activeChat.avatar" 
                         class="rounded-circle me-2 align-self-end" 
                         width="24" height="24" 
                         style="object-fit: cover; border: 1px solid #eee;"
                         :style="{ visibility: shouldShowAvatar(index) ? 'visible' : 'hidden' }">
                    
                    <div :class="msg.me ? 'chat-bubble-me' : 'chat-bubble-other'"
                         :title="formatTooltipTime(msg.created_at)">
                        {{ msg.text }}
                        <div v-if="msg.attachments && msg.attachments.length > 0">
                            <img v-for="(img, i) in msg.attachments" :key="i" 
                                 :src="img.url" class="img-fluid rounded mt-2 d-block" style="max-width: 150px"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="chat-body custom-scrollbar">
        <div v-if="isSearching">
            <div v-if="searchResults.length === 0 && searchQuery" class="text-center text-muted mt-4 small">
                Không tìm thấy kết quả.
            </div>
            <div v-else class="p-2">
                <small v-if="searchResults.length > 0" class="text-muted fw-bold px-2">Kết quả:</small>
                <div v-for="user in searchResults" :key="user.id" 
                     class="chat-item cursor-pointer" @click="onSelectContact(user)">
                    <img :src="user.avatar_url || 'https://via.placeholder.com/50'" />
                    <div class="chat-info overflow-hidden">
                        <strong class="text-truncate d-block">{{ user.user_name || user.full_name }}</strong>
                        <small class="text-muted">{{ user.full_name }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="chatList.length > 0">
            <div class="chat-item cursor-pointer" 
                 v-for="item in chatList" :key="item.id" @click="selectChat(item)">
                <img :src="item.avatar || 'https://via.placeholder.com/50'" />
                <div class="chat-info overflow-hidden">
                    <strong class="text-truncate d-block">{{ item.name }}</strong>
                    <small class="text-truncate" :class="{'fw-bold text-dark': !item.isRead}">
                        {{ item.me ? 'Bạn: ' : '' }}{{ item.lastMessage }}
                    </small>
                </div>
                <span class="small text-muted ms-1" style="font-size: 10px;">{{ formatTime(item.updated_at) }}</span>
            </div>
        </div>

        <div v-else class="p-2">
            <div class="text-center text-muted mt-2 mb-3 small">Bạn chưa có tin nhắn nào.</div>
            <small class="text-muted fw-bold px-2">Gợi ý cho bạn:</small>
            <div v-for="user in suggestedUsers" :key="user.id" 
                 class="chat-item cursor-pointer mt-1" @click="startNewChat(user)">
                <img :src="user.avatar_url || 'https://via.placeholder.com/50'" />
                <div class="chat-info overflow-hidden">
                    <strong class="text-truncate d-block">{{ user.user_name }}</strong>
                    <small class="text-muted">Gợi ý</small>
                </div>
                <i class="bi bi-chat-dots text-primary"></i>
            </div>
        </div>
    </div>

    <div class="chat-footer">
        <div v-if="activeChat" class="d-flex align-items-center">
            <input v-model="messageText" @keyup.enter="sendMessage" type="text" 
                   class="form-control form-control-sm rounded-pill border-0 bg-light" 
                   placeholder="Nhắn tin..." autofocus />
            <i class="bi bi-send-fill ms-2 text-primary cursor-pointer" @click="sendMessage"></i>
        </div>
        <div v-else>
            <input v-model="searchQuery" @input="handleSearchInput" type="text" 
                   placeholder="Tìm kiếm người dùng..." class="small-input"/>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed, watch } from "vue";
import { useRouter } from "vue-router";
import api from '@/api/client'; 
import { io } from "socket.io-client"; 

const router = useRouter();
const userInfo = JSON.parse(sessionStorage.getItem("user_info")) || { id: "User_ID_1" };

// --- STATE ---
const isOpen = ref(false);
const unread = ref(false);
const isLoading = ref(false);
const chatList = ref([]);
const suggestedUsers = ref([]); 
const activeChat = ref(null);
const messageText = ref("");
const msgContainer = ref(null); 

// 🔥 State cho Load More
const isLoadingMore = ref(false);
const hasMoreMessages = ref(true);

// Search State
const searchQuery = ref("");
const searchResults = ref([]);
const isSearching = ref(false);
let searchTimeout = null;
let socket = null;

// Watcher: Cuộn xuống đáy khi có tin nhắn MỚI (không phải load cũ)
watch(
  () => activeChat.value?.messages, 
  (newVal, oldVal) => { 
      if (!isLoadingMore.value) {
          scrollToBottom(); 
      }
  }, 
  { deep: true }
);

// --- LIFECYCLE ---
onMounted(async () => {
    initSocket();
    await fetchChatList();
    if (chatList.value.length === 0) {
        await fetchSuggestions();
    }
});

onBeforeUnmount(() => { if (socket) socket.disconnect(); });

// --- METHODS UI ---
const toggleChat = async () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        unread.value = false;
        if(!activeChat.value) {
            await fetchChatList();
            if (chatList.value.length === 0) await fetchSuggestions();
        } else {
            scrollToBottom();
        }
    }
};

const backToList = () => {
    activeChat.value = null;
    fetchChatList();
};

// 🔥 HÀM XỬ LÝ SCROLL ĐỂ LOAD THÊM
const handleScroll = async () => {
    if (!activeChat.value || isLoadingMore.value || !hasMoreMessages.value || !msgContainer.value) return;

    // Nếu cuộn sát đỉnh (< 20px)
    if (msgContainer.value.scrollTop < 20) {
        isLoadingMore.value = true;
        const oldScrollHeight = msgContainer.value.scrollHeight;
        const currentScrollTop = msgContainer.value.scrollTop;

        try {
            const currentOffset = activeChat.value.messages.length;
            const res = await api.post('/api/chat/get-messages', {
                conversation_id: activeChat.value.id,
                limit: 20,
                offset: currentOffset 
            });

            const newMessages = (res.data.data || []).map(msg => ({
                text: msg.content,
                me: String(msg.sender_id) === String(userInfo.id),
                attachments: msg.attachments || [],
                senderAvatar: msg.sender_info ? msg.sender_info.avatar : null,
                created_at: msg.created_at
            }));

            if (newMessages.length > 0) {
                // Thêm vào đầu danh sách
                activeChat.value.messages = [...newMessages, ...activeChat.value.messages];
                
                // Giữ vị trí cuộn
                nextTick(() => {
                    if (msgContainer.value) {
                        const newScrollHeight = msgContainer.value.scrollHeight;
                        msgContainer.value.scrollTop = newScrollHeight - oldScrollHeight + currentScrollTop;
                    }
                });
            } else {
                hasMoreMessages.value = false;
            }
        } catch (e) {
            console.error("Load more error:", e);
        } finally {
            isLoadingMore.value = false;
        }
    }
};

// --- LOGIC INSTAGRAM TIME ---
const shouldShowTime = (index) => {
    if (index === 0) return true;
    const currentMsg = activeChat.value.messages[index];
    const prevMsg = activeChat.value.messages[index - 1];
    if(!currentMsg.created_at || !prevMsg.created_at) return false;
    const diff = new Date(currentMsg.created_at) - new Date(prevMsg.created_at);
    return diff > 15 * 60 * 1000; // 15 phút
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

// --- LOGIC CHAT ---
const selectChat = async (item) => {
    isLoading.value = true;
    hasMoreMessages.value = true; // Reset trạng thái
    try {
        const res = await api.post('/api/chat/get-messages', {
            conversation_id: item.id,
            limit: 20
        });
        activeChat.value = {
            ...item,
            messages: (res.data.data || []).map(msg => ({
                text: msg.content,
                me: String(msg.sender_id) === String(userInfo.id),
                attachments: msg.attachments || [],
                senderAvatar: msg.sender_info ? msg.sender_info.avatar : null,
                created_at: msg.created_at 
            }))
        };
        scrollToBottom();
    } catch (e) { console.error(e); } finally { isLoading.value = false; }
};

const startNewChat = async (user) => {
    isLoading.value = true;
    try {
        const res = await api.post('/api/chat/create-conversation', {
            members: [userInfo.id, user.id],
            type: 'private'
        });
        const newConv = res.data.data;
        const newChatItem = {
            id: newConv._id,
            partnerId: user.id,
            name: user.user_name || user.full_name,
            avatar: user.avatar_url,
            lastMessage: "Bắt đầu cuộc trò chuyện",
            updated_at: new Date().toISOString(),
            isRead: true,
            messages: []
        };
        chatList.value.unshift(newChatItem);
        activeChat.value = newChatItem;
        scrollToBottom();
    } catch (e) { console.error(e); } finally { isLoading.value = false; }
};

const goToMessagePage = () => {
    isOpen.value = false;
    if (activeChat.value) {
        router.push({ path: '/message', query: { partnerId: activeChat.value.partnerId } });
    } else {
        router.push("/message");
    }
};

const sendMessage = async () => {
    if (!messageText.value.trim() || !activeChat.value) return;
    const content = messageText.value;
    const currentChatId = activeChat.value.id;

    activeChat.value.messages.push({
        text: content, me: true, attachments: [], created_at: new Date().toISOString()
    });
    // ScrollToBottom gọi bởi Watcher
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
    } catch (e) { console.error(e); }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (msgContainer.value) {
            msgContainer.value.scrollTop = msgContainer.value.scrollHeight;
        }
    });
};

// --- DATA ---
const fetchChatList = async () => {
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
    } catch (e) { console.error(e); }
};

const fetchSuggestions = async () => {
    try {
        const res = await api.post('/api/post/suggest-friend-message', { user_id: userInfo.id });
        suggestedUsers.value = (res.data && res.data.data) ? res.data.data : [];
    } catch (e) { console.error(e); }
};

const initSocket = () => {
    socket = io("http://localhost:8002"); 
    socket.on("receive_message", (newMsg) => {
        if (!isOpen.value) unread.value = true;
        if (activeChat.value && activeChat.value.id === newMsg.conversation_id) {
            activeChat.value.messages.push({
                text: newMsg.content,
                me: String(newMsg.sender_id) === String(userInfo.id),
                attachments: newMsg.attachments || [],
                created_at: newMsg.created_at
            });
            // Watcher sẽ cuộn
        }
        const chatItem = chatList.value.find(c => c.id === newMsg.conversation_id);
        if (chatItem) {
            chatItem.lastMessage = newMsg.content;
            chatItem.updated_at = new Date().toISOString();
            chatList.value.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        } else {
            fetchChatList();
        }
    });
};

const handleSearchInput = () => {
    if (!searchQuery.value.trim()) {
        isSearching.value = false;
        searchResults.value = [];
        return;
    }
    isSearching.value = true;
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        try {
            const res = await api.post('/api/post/search-user', { keyword: searchQuery.value });
            searchResults.value = (res.data.data && Array.isArray(res.data.data.users)) ? res.data.data.users : [];
        } catch (e) { console.error(e); }
    }, 500);
};

const onSelectContact = async (user) => {
    const existingChat = chatList.value.find(chat => String(chat.partnerId) === String(user.id));
    if (existingChat) selectChat(existingChat);
    else await startNewChat(user);
    isSearching.value = false;
};

// Format
const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
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
.chat-toggle {
  position: fixed; bottom: 20px; right: 20px;
  background: #0095f6; color: white;
  width: 55px; height: 55px; border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  display: flex; justify-content: center; align-items: center;
  cursor: pointer; z-index: 1050; transition: transform 0.2s;
}
.chat-toggle:hover { transform: scale(1.05); }
.chat-toggle i { font-size: 24px; }
.badge { position: absolute; top: 0; right: 0; width: 14px; height: 14px; background: red; border: 2px solid #fff; border-radius: 50%; }

.chat-box {
  position: fixed; bottom: 90px; right: 20px;
  width: 340px; height: 480px;
  background: #fff; border-radius: 12px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.15);
  display: flex; flex-direction: column;
  z-index: 1050; overflow: hidden; border: 1px solid #eee;
}

.chat-header {
  padding: 12px 16px; border-bottom: 1px solid #f0f0f0;
  display: flex; justify-content: space-between; align-items: center;
  background: #fff; height: 50px;
}
.chat-actions i { margin-left: 10px; color: #666; font-size: 1.1rem; cursor: pointer; }
.chat-actions i:hover { color: #0095f6; }

.chat-body { flex: 1; overflow-y: auto; background: #fff; }

.chat-item {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 14px; border-bottom: 1px solid #f8f8f8;
  transition: background 0.2s;
}
.chat-item:hover { background-color: #f9f9f9; }
.chat-item img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; }
.chat-info { flex: 1; min-width: 0; }

.chat-bubble-me {
    background: #0095f6; color: #fff;
    padding: 8px 12px; border-radius: 18px;
    max-width: 75%; font-size: 13px; word-wrap: break-word;
}
.chat-bubble-other {
    background: #efefef; color: #000;
    padding: 8px 12px; border-radius: 18px;
    max-width: 75%; font-size: 13px; word-wrap: break-word;
}

.chat-footer { padding: 10px; border-top: 1px solid #f0f0f0; background: #fff; }
.small-input { width: 100%; padding: 8px 12px; border-radius: 20px; border: 1px solid #ddd; outline: none; font-size: 13px; background: #f8f8f8; }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 4px; }
.animate-fade-up { animation: fadeUp 0.3s ease-out; }
@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.fade-in-up { animation: fadeInUp 0.2s ease-out; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* FLEX COLUMN REVERSE HELPER */
.d-flex.flex-column { display: flex; flex-direction: column; }
.justify-content-end { justify-content: flex-end; }
.message-row { display: flex; align-items: flex-end; }
</style>