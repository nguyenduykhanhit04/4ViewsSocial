<template>
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="row align-items-center">

      <!-- LEFT SIDE IMAGES -->
      <div class="col-lg-6 d-none d-lg-flex justify-content-end">
        <div class="phone-mockup">
          <img src="/images/logo.png" alt="Logo">
        </div>
      </div>

      <!-- RIGHT SIDE LOGIN FORM -->
      <div class="col-lg-6 d-flex justify-content-center">
        <div>

          <div class="login-box text-center">
            <div class="instagram-logo mb-4">4ViewsSocial</div>

            <input
              v-model="user.user_name"
              type="text"
              class="form-control input-instagram mb-2"
              placeholder="Số điện thoại, tên người dùng hoặc email"
            >

            <input
              v-model="user.password"
              type="password"
              class="form-control input-instagram mb-3"
              placeholder="Mật khẩu"
            >

            <button @click="login" class="btn btn-login w-100 py-1">
              Đăng nhập
            </button>

            <div class="divider">
              <span>HOẶC</span>
            </div>

            <button
              @click="loginWithGoogle"
              class="login-gg text-primary fw-bold d-block mb-3 mx-auto"
            >
              <i class="bi bi-google"></i> Đăng nhập với Google
            </button>

            <a href="#" class="text-decoration-none" style="font-size: 12px;">
              Quên mật khẩu?
            </a>
          </div>

          <div class="login-fb signup-box text-center">
            Bạn chưa có tài khoản?
            <a href="/signup" class="text-primary fw-bold">Đăng ký</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { googleTokenLogin } from "vue3-google-login";
import api from "@/api/client";

/* ================= FIREBASE ================= */
import { auth } from "@/firebase";
import { signInWithCustomToken, onAuthStateChanged } from "firebase/auth";
import {
  getDatabase,
  ref as dbRef,
  set,
  onDisconnect,
  serverTimestamp
} from "firebase/database";

/* ================= STATE ================= */
const loading = ref(false);
const error = ref(null);

const user = reactive({
  user_name: "",
  password: ""
});

const db = getDatabase();

/* ================= ONLINE / OFFLINE ================= */
function setUserOnline(uid) {
  const statusRef = dbRef(db, `status/${uid}`);

  // Khi mất kết nối → offline
  onDisconnect(statusRef).set({
    state: "offline",
    last_changed: serverTimestamp()
  });

  // Khi online
  set(statusRef, {
    state: "online",
    last_changed: serverTimestamp()
  });
}

/* ================= SAVE FCM TOKEN ================= */
import { requestPermission } from "@/firebase";

async function saveDeviceToken() {
  try {
    const fcmToken = await requestPermission();
    if (!fcmToken) return;

    const userInfo = JSON.parse(sessionStorage.getItem("user_info"));

    await api.post("/api/post/set-device-token", {
      fcmToken: fcmToken,
      user_id: userInfo.id
    });

    console.log("Saved FCM Token for user:", userInfo.id);
  } catch (err) {
    console.warn("Cannot save FCM token:", err);
  }
}

/* ================= LOGIN NORMAL ================= */
async function login() {
  loading.value = true;
  try {
    const res = await api.post("/api/auth/login", {
      user_name: user.user_name,
      password: user.password
    });

    if (res.data.code === 200) {
      const { access_token, firebase_token, user_info } = res.data.data;

      // Firebase login
      await signInWithCustomToken(auth, firebase_token);

      sessionStorage.setItem("access_token", access_token);
      sessionStorage.setItem("user_info", JSON.stringify(user_info));

      api.defaults.headers.common["Authorization"] = `Bearer ${access_token}`;

      await saveDeviceToken();

      handleRedirect(user_info);
    }
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
}

/* ================= LOGIN GOOGLE ================= */
async function loginWithGoogle() {
  try {
    const googleUser = await googleTokenLogin();

    const res = await api.post("/api/auth/loginwithgoogle", {
      access_token: googleUser.access_token
    });

    if (res.data.code === 200) {
      const { access_token, firebase_token, user_info } = res.data.data;

      await signInWithCustomToken(auth, firebase_token);

      sessionStorage.setItem("access_token", access_token);
      sessionStorage.setItem("user_info", JSON.stringify(user_info));

      api.defaults.headers.common["Authorization"] = `Bearer ${access_token}`;

      await saveDeviceToken();

      handleRedirect(user_info);
    }
  } catch (err) {
    console.error(err);
  }
}

/* ================= REDIRECT ================= */
function handleRedirect(user_info) {
  if (user_info.role == 0) {
    window.location.href = "/admin/dashboard";
  } else {
    window.location.href = "/homepage";
  }
}

/* ================= FIREBASE AUTH LISTENER ================= */
onMounted(() => {
  onAuthStateChanged(auth, (firebaseUser) => {
    if (firebaseUser) {
      console.log("User online:", firebaseUser.uid);
      setUserOnline(firebaseUser.uid);
    }
  });
});
</script>

<style scoped>
body {
  background-color: #fafafa;
}

.phone-mockup img {
  width: 100%;
  max-width: 420px;
}

.login-box {
  border: 1px solid #ddd;
  padding: 40px;
  background: white;
  width: 100%;
  max-width: 360px;
}

.instagram-logo {
  font-family: "Billabong", cursive;
  font-size: 48px;
}

.input-instagram {
  background: #fafafa;
  font-size: 14px;
  height: 36px;
}

.btn-login {
  background-color: #fb6f92;
  color: white;
  font-weight: 600;
  font-size: 13px;
}

.login-gg {
  font-size: 13px;
  font-weight: 500;
}

.divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 15px 0;
}

.divider::before,
.divider::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid #ccc;
}

.divider span {
  padding: 0 10px;
  font-size: 14px;
  color: #737373;
}

.signup-box {
  border: 1px solid #ddd;
  background: white;
  padding: 20px;
  margin-top: 10px;
  max-width: 360px;
}
</style>
