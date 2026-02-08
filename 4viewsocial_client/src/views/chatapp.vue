<template>
    <div class="chat-container">
        <h2>💬 Vue JS Chat Realtime</h2>

        <div class="setup-box" v-if="!joined">
            <input v-model="userId" placeholder="Tên bạn (VD: UserA)" />
            <input v-model="roomId" placeholder="ID Phòng (Copy từ DB)" />
            <button @click="joinRoom">Vào Phòng Chat</button>
        </div>

        <div class="chat-box" v-else>
            <div class="header">
                Phòng: <strong>{{ roomId }}</strong> | Bạn là: <strong>{{ userId }}</strong>
            </div>

            <div class="messages-area" ref="messagesContainer">
                <div v-for="(msg, index) in messages" :key="index"
                    :class="['message-row', msg.sender_id === userId ? 'my-msg' : 'other-msg']">
                    <div class="bubble">
                        <small class="sender-name">{{ msg.sender_id }}</small>

                        <p v-if="msg.content">{{ msg.content }}</p>

                        <div v-if="msg.attachments && msg.attachments.length > 0">
                            <div v-for="(file, i) in msg.attachments" :key="i">
                                <img v-if="file.type === 'image'" :src="file.url" class="msg-img" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-area">
                <label for="file-upload" class="icon-btn">📎</label>
                <input id="file-upload" type="file" multiple @change="handleFileUpload" style="display: none" />

                <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Nhập tin nhắn..." />
                <button @click="sendMessage">Gửi</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { io } from 'socket.io-client';

// 1. Import instance api của bạn
import api from "@/api/client"; 

// 2. Cấu hình URL cho Socket (Socket không dùng chung axios instance được)
const SOCKET_URL = 'http://localhost:8002'; 
const socket = io(SOCKET_URL);

// --- STATE (Giữ nguyên) ---
const joined = ref(false);
const userId = ref('UserA');
const roomId = ref('');
const messages = ref([]);
const newMessage = ref('');
const messagesContainer = ref(null);

// --- SOCKET LISTENER (Giữ nguyên) ---
onMounted(() => {
    socket.on('receive_message', (message) => {
        console.log("📩 Nhận tin mới:", message);
        messages.value.push(message);
        scrollToBottom();
    });
});

// --- FUNCTIONS ---

// 1. Vào phòng
const joinRoom = async () => {
    if (!roomId.value || !userId.value) return alert("Điền đủ thông tin!");
    
    socket.emit('join_room', roomId.value);

    try {
        // SỬA: Dùng api.get thay vì axios.get
        // Nếu baseURL của bạn đã có /api/chat, hãy xóa chữ /api/chat ở dòng dưới đi
        const res = await api.get(`/api/chat/messages/${roomId.value}`);
        
        messages.value = res.data.data;
        joined.value = true;
        scrollToBottom();
    } catch (err) {
        console.error(err);
        alert("Lỗi lấy lịch sử chat");
    }
};

// 2. Gửi tin nhắn Text
const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    try {
        // SỬA: Đổi đường dẫn thành /api/chat/messages cho đúng với server.js
        await api.post('/api/chat/messages', {
            conversation_id: roomId.value,
            sender_id: userId.value,
            content: newMessage.value
        });

        newMessage.value = ''; 
    } catch (err) {
        console.error(err);
    }
};

// 3. Upload ảnh
const handleFileUpload = async (event) => {
    const files = event.target.files;
    if (!files.length) return;

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files', files[i]);
    }

    try {
        // SỬA: Dùng api.post và config header
        const uploadRes = await api.post('/api/chat/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        const attachments = uploadRes.data.data;

        // Gửi tin nhắn kèm ảnh
        await api.post('/api/chat/messages', {
            conversation_id: roomId.value,
            sender_id: userId.value,
            content: "", 
            attachments: attachments
        });

    } catch (err) {
        console.error(err); // Log lỗi để xem nếu đường dẫn sai
        alert("Lỗi upload ảnh");
    }
};

// Helper (Giữ nguyên)
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};
</script>

<style scoped>
/* CSS đơn giản cho Chat đẹp mắt */
.chat-container {
    max-width: 500px;
    margin: 20px auto;
    font-family: sans-serif;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

h2 {
    text-align: center;
    background: #eee;
    margin: 0;
    padding: 10px;
}

.setup-box {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.setup-box input,
.setup-box button {
    padding: 10px;
}

.chat-box {
    display: flex;
    flex-direction: column;
    height: 500px;
}

.header {
    background: #f1f1f1;
    padding: 10px;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

.messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    background: #fff;
}

.message-row {
    display: flex;
    margin-bottom: 10px;
}

.my-msg {
    justify-content: flex-end;
}

.other-msg {
    justify-content: flex-start;
}

.bubble {
    max-width: 70%;
    padding: 8px 12px;
    border-radius: 15px;
    position: relative;
    word-wrap: break-word;
}

.my-msg .bubble {
    background: #0084ff;
    color: white;
    border-bottom-right-radius: 2px;
}

.other-msg .bubble {
    background: #e4e6eb;
    color: black;
    border-bottom-left-radius: 2px;
}

.sender-name {
    display: block;
    font-size: 10px;
    margin-bottom: 2px;
    opacity: 0.7;
}

.msg-img {
    max-width: 100%;
    border-radius: 8px;
    margin-top: 5px;
}

.input-area {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ddd;
    background: #f9f9f9;
    align-items: center;
}

.input-area input[type=text] {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    margin: 0 5px;
    outline: none;
}

.input-area button {
    padding: 10px 20px;
    background: #0084ff;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}

.icon-btn {
    font-size: 24px;
    cursor: pointer;
    margin-right: 5px;
}
</style>