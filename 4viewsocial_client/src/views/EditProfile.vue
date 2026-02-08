<template>
  <div class="edit-profile-page">
    <SidebarComponent />

    <main class="edit-profile-content">
      <div class="edit-container shadow-sm">
        
        <aside class="inner-sidebar d-none d-md-block">
          <div 
            class="menu-item" 
            :class="{ active: currentTab === 'edit' }" 
            @click="currentTab = 'edit'"
          >
            Chỉnh sửa trang cá nhân
          </div>
          <div 
            class="menu-item" 
            :class="{ active: currentTab === 'password' }" 
            @click="currentTab = 'password'"
          >
            Đổi mật khẩu
          </div>
        </aside>

        <section class="edit-main">
          
          <header class="edit-header-row mb-5">
            <div class="header-label">
              <div class="avatar-circle">
                <img :src="previewAvatar || user_info.avatar_url || defaultAvatar" @error="handleAvatarError" />
              </div>
            </div>
            <div class="header-info">
              <h2 class="display-username">{{ user_info.user_name }}</h2>
              <label v-if="currentTab === 'edit'" for="avatar-upload" class="btn-change-photo">
                Thay đổi ảnh đại diện
              </label>
              <input type="file" id="avatar-upload" hidden @change="handleImageChange" accept="image/*" />
            </div>
          </header>

          <Transition name="fade" mode="out-in">
            <div v-if="currentTab === 'edit'" key="edit-form">
              <form @submit.prevent="updateProfile" class="inst-form">
                
                <div class="form-group-row">
                  <aside class="label-aside"><label>Tên</label></aside>
                  <div class="input-aside">
                    <input type="text" v-model="formData.full_name" class="inst-input" />
                    <p class="help-text">Giúp mọi người khám phá tài khoản của bạn bằng cách sử dụng tên mà mọi người thường dùng để gọi bạn.</p>
                  </div>
                </div>

                <div class="form-group-row">
                  <aside class="label-aside"><label>Tên người dùng</label></aside>
                  <div class="input-aside">
                    <input type="text" v-model="formData.user_name" class="inst-input" />
                    <p class="help-text">Bạn có thể đổi tên người dùng trở lại sau 14 ngày.</p>
                  </div>
                </div>

                <div class="form-group-row">
                  <aside class="label-aside"><label>Tiểu sử</label></aside>
                  <div class="input-aside">
                    <textarea v-model="formData.bio" class="inst-input bio-area" maxlength="150"></textarea>
                    <div class="char-count">{{ formData.bio?.length || 0 }} / 150</div>
                  </div>
                </div>

                <div class="form-group-row mt-4">
                  <aside class="label-aside"></aside>
                  <div class="input-aside">
                    <button type="submit" class="btn-inst-primary" :disabled="loading">
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>Lưu thông tin
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <div v-else-if="currentTab === 'password'" key="pw-form">
              <form @submit.prevent="changePassword" class="inst-form">
                
                <div class="form-group-row">
                  <aside class="label-aside"><label>Mật khẩu cũ</label></aside>
                  <div class="input-aside">
                    <input type="password" v-model="passwordData.old_password" class="inst-input" />
                  </div>
                </div>

                <div class="form-group-row">
                  <aside class="label-aside"><label>Mật khẩu mới</label></aside>
                  <div class="input-aside">
                    <input type="password" v-model="passwordData.new_password" class="inst-input" />
                  </div>
                </div>

                <div class="form-group-row">
                  <aside class="label-aside"><label>Xác nhận lại</label></aside>
                  <div class="input-aside">
                    <input type="password" v-model="passwordData.confirm_password" class="inst-input" />
                  </div>
                </div>

                <div class="form-group-row mt-4">
                  <aside class="label-aside"></aside>
                  <div class="input-aside">
                    <button type="submit" class="btn-inst-primary" :disabled="loading">
                      Đổi mật khẩu
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </Transition>
        </section>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import SidebarComponent from "@/components/SidebarComponent.vue";
import api from "@/api/client";

const user_info = JSON.parse(sessionStorage.getItem("user_info")) || {};
const loading = ref(false);
const currentTab = ref('edit');
const previewAvatar = ref(null);
const avatarFile = ref(null);
const defaultAvatar = new URL('@/assets/avtgd2.jpg', import.meta.url).href;

const formData = reactive({
  full_name: user_info.full_name || "",
  user_name: user_info.user_name || "",
  bio: user_info.bio || "",
});

const passwordData = reactive({ old_password: "", new_password: "", confirm_password: "" });

function handleImageChange(e) {
  const file = e.target.files[0];
  if (file) {
    avatarFile.value = file;
    previewAvatar.value = URL.createObjectURL(file);
  }
}

function handleAvatarError(e) { e.target.src = defaultAvatar; }

async function updateProfile() {
  loading.value = true;
  const data = new FormData();
  data.append("user_id", user_info.id);
  data.append("full_name", formData.full_name);
  data.append("user_name", formData.user_name);
  data.append("bio", formData.bio);
  if (avatarFile.value) data.append("avatar", avatarFile.value);

  try {
    const res = await api.post("/api/post/update-profile", data);
    if (res.data.code === 200) {
      alert("Cập nhật thành công!");
      sessionStorage.setItem("user_info", JSON.stringify({ ...user_info, ...res.data.data }));
      window.location.reload();
    }
  } finally { loading.value = false; }
}

async function changePassword() {
  if (passwordData.new_password !== passwordData.confirm_password) {
    alert("Mật khẩu xác nhận không khớp!");
    return;
  }
  loading.value = true;
  try {
    const res = await api.post("/api/post/change-password", {
      user_id: user_info.id,
      old_password: passwordData.old_password,
      new_password: passwordData.new_password
    });
    if (res.data.code === 200) {
      alert("Đổi mật khẩu thành công!");
      Object.keys(passwordData).forEach(k => passwordData[k] = "");
    } else { alert(res.data.message); }
  } finally { loading.value = false; }
}
</script>

<style scoped>
/* Layout tổng quát */
.edit-profile-page { display: flex; background: #fafafa; min-height: 100vh; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto; }
.edit-profile-content { margin-left: 250px; flex: 1; display: flex; justify-content: center; align-items: flex-start; padding: 40px 20px; }

/* Container chính (Box trắng) */
.edit-container { background: #fff; border: 1px solid #dbdbdb; display: flex; width: 100%; max-width: 935px; min-height: 80vh; border-radius: 4px; }

/* Sidebar bên trái của box */
.inner-sidebar { width: 236px; border-right: 1px solid #dbdbdb; padding-top: 10px; }
.menu-item { padding: 16px 25px; font-size: 16px; cursor: pointer; border-left: 2px solid transparent; transition: 0.1s; }
.menu-item:hover { background: #fafafa; border-left-color: #dbdbdb; }
.menu-item.active { font-weight: 600; border-left-color: #262626; }

/* Form chính bên phải */
.edit-main { flex: 1; padding: 40px 20px 40px 0; }

/* Header Row (Avatar & Username) */
.edit-header-row { display: flex; align-items: center; }
.header-label { width: 194px; padding-right: 32px; display: flex; justify-content: flex-end; }
.avatar-circle { width: 38px; height: 38px; border-radius: 50%; overflow: hidden; border: 1px solid #efefef; }
.avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
.display-username { font-size: 20px; font-weight: 400; margin: 0; }
.btn-change-photo { color: #0095f6; font-weight: 600; font-size: 14px; cursor: pointer; }

/* Form Group (Label trái - Input phải) */
.form-group-row { display: flex; margin-bottom: 16px; }
.label-aside { width: 194px; padding-right: 32px; text-align: right; margin-top: 6px; font-weight: 600; font-size: 16px; }
.input-aside { flex: 1; max-width: 355px; position: relative; }

/* Input Styles */
.inst-input { width: 100%; border: 1px solid #dbdbdb; border-radius: 3px; padding: 8px 12px; font-size: 16px; outline: none; }
.inst-input:focus { border-color: #a8a8a8; }
.bio-area { height: 80px; resize: vertical; }

/* Văn bản phụ trợ */
.help-text { font-size: 12px; color: #8e8e8e; margin-top: 10px; line-height: 1.4; }
.char-count { font-size: 12px; color: #8e8e8e; text-align: right; margin-top: 4px; }

/* Nút bấm chuẩn IG */
.btn-inst-primary { background-color: #0095f6; color: #fff; border: none; border-radius: 4px; padding: 5px 15px; font-weight: 600; font-size: 14px; transition: 0.2s; }
.btn-inst-primary:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-inst-primary:not(:disabled):hover { background-color: #1877f2; }

/* Hiệu ứng chuyển tab mượt */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@media (max-width: 768px) {
  .inner-sidebar { display: none; }
  .edit-profile-content { margin-left: 0; padding: 0; }
  .edit-container { border: none; flex-direction: column; }
  .edit-main { padding: 20px; }
  .label-aside { width: 100%; text-align: left; padding: 0; margin-bottom: 5px; }
  .header-label { width: auto; padding-right: 15px; }
}
</style>