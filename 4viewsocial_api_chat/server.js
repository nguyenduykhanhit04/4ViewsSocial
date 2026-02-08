require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const multer = require('multer');
const path = require('path');
const cors = require('cors');
const http = require('http');           
const { Server } = require('socket.io');

// --- 1. CONFIG DATABASE & MODELS ---
const connectDB = async () => {
    try {
        // Thay chuỗi kết nối MongoDB của bạn vào đây
        await mongoose.connect(process.env.MONGO_URI || 'mongodb://127.0.0.1:27017/instagram_chat_clone');
        console.log("✅ MongoDB Connected");
    } catch (err) {
        console.error("❌ MongoDB Connection Error:", err);
        process.exit(1);
    }
};

// Import Models (Đảm bảo file model tồn tại đúng đường dẫn)
const Conversation = require('./src/models/Conversation');
const Message = require('./src/models/Message');

// --- 2. INIT APP ---
const app = express();
const server = http.createServer(app); 

// Cấu hình CORS (Cho phép Vue & Gateway truy cập)
const corsOptions = {
    origin: ["http://localhost:5173", "http://localhost:3000", "http://localhost:4000"], 
    credentials: true,
    methods: ["GET", "POST", "PUT", "DELETE"]
};

app.use(express.json());
app.use(cors(corsOptions));
app.use('/uploads', express.static('uploads')); // Public folder ảnh

// Kết nối DB
connectDB();

// --- 3. SOCKET.IO CONFIG ---
const io = new Server(server, {         
    cors: {
        origin: "*", // Chấp nhận kết nối từ mọi nguồn (hoặc cụ thể domain Vue)
        methods: ["GET", "POST"]
    }
});

io.on('connection', (socket) => {
    // console.log('⚡ User connected:', socket.id);

    // User join vào phòng chat cụ thể
    socket.on('join_room', (conversation_id) => {
        socket.join(conversation_id);
        // console.log(`User ${socket.id} joined room: ${conversation_id}`);
    });

    socket.on('disconnect', () => {
        // console.log('User disconnected');
    });
});

// --- 4. MULTER UPLOAD CONFIG ---
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'uploads/'); // Đảm bảo tạo thư mục uploads ở root project
    },
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, uniqueSuffix + path.extname(file.originalname));
    }
});
const upload = multer({ storage: storage });


// ============================================================
// CÁC API BACKEND (CHUẨN HÓA POST REQUEST)
// ============================================================

// 1. API Upload File (Ảnh/Video)
app.post('/api/upload', upload.array('files', 5), (req, res) => {
    try {
        if (!req.files || req.files.length === 0) {
            return res.status(400).json({ success: false, message: "No files uploaded" });
        }
        const attachments = req.files.map(file => ({
            url: `${req.protocol}://${req.get('host')}/uploads/${file.filename}`,
            type: file.mimetype.startsWith('image/') ? 'image' : 'video'
        }));
        res.status(200).json({ success: true, data: attachments });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});
/**
 * 2. API Lấy danh sách hội thoại
 * Sắp xếp: updated_at GIẢM DẦN (-1) => Cái nào mới cập nhật thì lên đầu list
 */
app.post('/api/get-conversations', async (req, res) => {
    try {
        const { user_id } = req.body;

        if (!user_id) return res.status(400).json({ error: "Thiếu user_id" });

        const conversations = await Conversation.find({
            members: { $in: [String(user_id)] }
        })
        .sort({ updated_at: -1 }) // 🔥 QUAN TRỌNG: Mới nhất lên đầu
        .lean(); // Dùng lean() để query nhanh hơn nếu chỉ đọc

        res.status(200).json({ success: true, data: conversations });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: err.message });
    }
});

/**
 * 3. API Lấy lịch sử tin nhắn
 * Logic chuẩn cho Chat App: 
 * - Lấy 50 tin nhắn MỚI NHẤT của cuộc trò chuyện đó.
 * - Sau đó sắp xếp lại theo thứ tự thời gian TĂNG DẦN để hiển thị từ trên xuống dưới.
 */
app.post('/api/get-messages', async (req, res) => {
    try {
        const { conversation_id, limit = 50, offset = 0 } = req.body;

        if (!conversation_id || !mongoose.Types.ObjectId.isValid(conversation_id)) {
            return res.status(400).json({ error: "conversation_id không hợp lệ" });
        }

        // Bước 1: Lấy N tin nhắn mới nhất (sort giảm dần -1)
        const messagesDesc = await Message.find({ conversation_id: conversation_id })
            .sort({ created_at: -1 }) 
            .skip(offset)
            .limit(limit)
            .lean();

        // Bước 2: Đảo ngược lại mảng để trả về cho Client hiển thị đúng thứ tự (Cũ trên - Mới dưới)
        const messagesAsc = messagesDesc.reverse();

        res.status(200).json({ success: true, data: messagesAsc });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: err.message });
    }
});

/**
 * 4. API Gửi tin nhắn
 * Vue gọi: api.post('/api/chat/send-message', { ...data })
 */
app.post('/api/send-message', async (req, res) => {
    try {
        const { conversation_id, sender_id, content, attachments, sender_info } = req.body;

        // Validation cơ bản
        if (!conversation_id || !sender_id) {
            return res.status(400).json({ error: "Thiếu thông tin bắt buộc" });
        }

        // 1. Tạo Message mới
        const newMessage = new Message({
            conversation_id,
            sender_id, // ID String từ MySQL
            content: content || "",
            attachments: attachments || [],
            sender_info: sender_info || {} // Lưu snapshot tên/avatar để hiện nhanh
        });

        await newMessage.save();

        // 2. Cập nhật Conversation (Last Message để hiện ngoài list)
        let previewText = content;
        if (!content && attachments && attachments.length > 0) {
            previewText = `[Đã gửi ${attachments.length} tệp]`;
        }

        await Conversation.findByIdAndUpdate(conversation_id, {
            last_message: {
                content: previewText,
                sender: sender_id,
                created_at: new Date()
            },
            updated_at: new Date()
        });

        // 3. Bắn Socket Realtime cho user khác trong phòng
        // Client Vue cần lắng nghe sự kiện 'receive_message'
        io.to(conversation_id).emit('receive_message', newMessage);

        res.status(200).json({ success: true, data: newMessage });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: err.message });
    }
});

/**
 * 5. API Tạo cuộc trò chuyện mới (Check trùng Private chat)
 * Dùng khi user click vào nút "Nhắn tin" trên profile người khác
 */
app.post('/api/create-conversation', async (req, res) => {
    try {
        // members: Mảng chứa ["MyID", "PartnerID"]
        const { members, type = 'private', name } = req.body;

        if (!members || members.length < 2) {
            return res.status(400).json({ error: "Cần ít nhất 2 thành viên" });
        }

        // Nếu là chat riêng (private), kiểm tra xem đã tồn tại chưa
        if (type === 'private') {
            const existingConv = await Conversation.findOne({
                type: 'private',
                members: { $all: members } // Chứa đủ cả 2 ID
            });

            if (existingConv) {
                return res.status(200).json({ success: true, data: existingConv, isNew: false });
            }
        }

        // Tạo mới
        const newConv = new Conversation({
            members,
            type,
            name: name || "Cuộc trò chuyện",
            last_message: { content: "Bắt đầu cuộc trò chuyện", created_at: new Date() }
        });

        await newConv.save();
        res.status(200).json({ success: true, data: newConv, isNew: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});


// --- 5. START SERVER ---
const PORT = process.env.PORT || 8002;
server.listen(PORT, () => {
    console.log(`🚀 Chat Service running on port ${PORT}`);
    console.log(`📡 Socket.io ready`);
});