<template>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">

        <!-- SIGNUP BOX -->
        <div class="signup-box">

            <!-- INSTAGRAM LOGO -->
            <div class="instagram-logo text-center mb-3">4ViewsSocial</div>

            <!-- Subtitle -->
            <p class="text-center text-muted fw-bold mb-3" style="font-size: 14px;">
                Đăng ký để xem ảnh và video từ bạn bè.
            </p>

            <!-- GOOGLE SIGNUP BUTTON -->
            <button @click="loginWithGoogle"
                class="btn btn-light w-100 d-flex justify-content-center align-items-center gap-2 mb-3 signup-google">
                <!-- Google login button -->
                Đăng nhập bằng Google
            </button>

            <!-- DIVIDER -->
            <div class="divider mb-3">
                <span>HOẶC</span>
            </div>

            <!-- FORM INPUTS -->
            <!-- Nhập thông tin đăng ký -->
            <input v-model="user.email" type="text" class="form-control input-instagram mb-2"
                placeholder="Số di động hoặc email">
            <input v-model="user.password" type="password" class="form-control input-instagram mb-2"
                placeholder="Mật khẩu">
            <input v-model="user.confirm_password" type="password" class="form-control input-instagram mb-2"
                placeholder="Nhập lại mật khẩu">
            <input v-model="user.full_name" type="text" class="form-control input-instagram mb-2"
                placeholder="Tên đầy đủ">
            <input v-model="user.user_name" type="text" class="form-control input-instagram mb-2"
                placeholder="Tên người dùng">

            <!-- Thông tin quyền riêng tư -->
            <p class="text-center mt-3 text-muted" style="font-size: 12px;">
                Những người dùng dịch vụ của chúng tôi có thể đã tải thông tin liên hệ của bạn lên 4ViewsSocial.
                <a href="#" class="text-muted">Tìm hiểu thêm</a>
            </p>

            <p class="text-center text-muted" style="font-size: 12px;">
                Bằng cách đăng ký, bạn đồng ý với
                <a href="#" class="text-muted">Điều khoản</a>,
                <a href="#" class="text-muted">Chính sách quyền riêng tư</a> và
                <a href="#" class="text-muted">Chính sách cookie</a> của chúng tôi.
            </p>

            <!-- REGISTER BUTTON -->
            <button @click="register" class="btn btn-primary w-100 mt-2 btn-register">Đăng ký</button>

            <!-- ERROR / LOADING MESSAGES -->
            <p v-if="error" class="text-danger mt-2 text-center">{{ error }}</p>
            <p v-if="loading" class="text-center text-muted mt-2">Đang xử lý...</p>

        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import { googleTokenLogin } from 'vue3-google-login';
import api from "@/api/client";

/**
 * STATE
 */
const loading = ref(false); // trạng thái loading khi gọi API
const error = ref(null); // hiển thị lỗi
const user = reactive({
    full_name: '',
    user_name: '',
    email: '',
    password: '',
    confirm_password: ''
});

/**
 * FUNCTION: register
 * Gửi yêu cầu đăng ký tài khoản mới lên backend
 */
async function register() {
    error.value = '';

    // Validate form
    if (!user.email || !user.password || !user.confirm_password || !user.full_name || !user.user_name) {
        error.value = 'Vui lòng điền đầy đủ thông tin!';
        return;
    }

    if (user.password !== user.confirm_password) {
        error.value = 'Mật khẩu và nhập lại mật khẩu không khớp!';
        return;
    }

    loading.value = true;

    try {
        // Gọi API đăng ký
        const res = await api.post("/api/auth/register", {
            email: user.email,
            password: user.password,
            full_name: user.full_name,
            user_name: user.user_name
        });

        if (res.data.code === 200) {
            alert('Đăng ký thành công!');
            window.location.href = '/login'; // chuyển hướng tới trang login
        } else {
            error.value = res.data.message || 'Lỗi khi đăng ký';
        }
    } catch (err) {
        console.error(err);
        error.value = 'Lỗi khi gửi yêu cầu đăng ký';
    } finally {
        loading.value = false;
    }
}

/**
 * FUNCTION: loginWithGoogle
 * Đăng nhập / đăng ký bằng tài khoản Google
 */
async function loginWithGoogle() {
    try {
        const googleUser = await googleTokenLogin(); // lấy token Google
        const res = await api.post("/api/auth/loginwithgoogle", {
            access_token: googleUser.access_token,
        });

        if (res.data.code === 200) {
            // Lưu thông tin user vào sessionStorage
            sessionStorage.setItem("access_token", res.data.data.accessToken);
            sessionStorage.setItem("user_info", JSON.stringify(res.data.data.user_info));

            alert("Đăng nhập thành công!");
            close(); // Đóng popup Google

            const user_info = JSON.parse(sessionStorage.getItem('user_info'));
            if (user_info.role == 0) { // 0 là admin
                window.location.href = '/admin/index';
            } else {
                window.location.href = '/homepage';
            }
        } else {
            alert(res.data.message || "Đăng nhập thất bại");
        }
    } catch (err) {
        console.error(err);
        alert(err.response?.data?.message || err.message);
    }
}

/**
 * LIFECYCLE
 */
onMounted(() => {
    console.log("Register component mounted");
});
</script>

<style scoped>
.signup-box {
    border: 1px solid #ddd;
    background: white;
    padding: 40px 35px;
    width: 100%;
    max-width: 380px;
    border-radius: 5px;
}

.instagram-logo {
    font-family: 'Billabong', cursive;
    font-size: 48px;
}

.input-instagram {
    background: #fafafa;
    font-size: 14px;
    height: 40px;
}

.signup-google {
    border: 1px solid #ddd;
    font-weight: 600;
    font-size: 14px;
    color: #000;
}

.signup-google:hover {
    background-color: #f1f1f1;
}

.btn-register {
    background-color: #fb6f92;
    border-color: #ff80b3;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.btn-register:hover {
    background-color: #ff80b3 !important;
    border-color: #ff80b3 !important;
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
}

.divider::before,
.divider::after {
    content: "";
    flex: 1;
    border-bottom: 1px solid #ccc;
}

.divider span {
    font-size: 12px;
    padding: 0 10px;
    color: #737373;
}
</style>
