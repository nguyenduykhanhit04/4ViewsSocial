<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h3 class="fw-bold">Quản lý người dùng</h3>
    </div>
    <p class="text-muted mb-4">Quản lý tài khoản và quyền hạn</p>

    <!-- FILTER -->
    <div class="row g-3 mb-4 mt-1 border rounded p-2">
      <div class="col-md-4">
        <p class="fw-semibold mb-1">Tìm kiếm</p>
        <input
          v-model="filters.name"
          type="text"
          class="form-control"
          placeholder="🔍 Tên, username..."
        />
      </div>

      <div class="col-md-4">
        <p class="fw-semibold mb-1">Vai trò</p>
        <select v-model="filters.role" class="form-select">
          <option value="">Tất cả vai trò</option>
          <option :value="0">Admin</option>
          <option :value="1">User</option>
        </select>
      </div>

      <div class="col-md-4">
        <p class="fw-semibold mb-1">Trạng thái</p>
        <select v-model="filters.status" class="form-select">
          <option value="">Tất cả trạng thái</option>
          <option :value="1">Hoạt động</option>
          <option :value="0">Bị cấm</option>
        </select>
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive row g-3 mb-4 border rounded p-3 table-container">
      <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
        <thead class="table-light text-center sticky-top">
          <tr>
            <th>Người dùng</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Online</th>
            <th>Hành động</th>
          </tr>
        </thead>

        <tbody class="text-center">
          <tr v-if="loading">
            <td colspan="6">Đang tải dữ liệu...</td>
          </tr>

          <tr v-if="!loading && filteredUsers.length === 0">
            <td colspan="6">Không có dữ liệu phù hợp</td>
          </tr>

          <tr v-for="user in filteredUsers" :key="user.id">
            <td>
              {{ user.full_name }}<br />
              <small class="text-muted">@{{ user.user_name }}</small>
            </td>

            <td>{{ user.email }}</td>

            <td>
              <span class="badge" :class="user.role === 0 ? 'bg-secondary' : 'bg-danger'">
                {{ user.role === 0 ? "Admin" : "User" }}
              </span>
            </td>

            <td>
              <span :class="user.status == 1 ? 'text-success' : 'text-danger'">
                {{ user.status == 1 ? "Hoạt động" : "Bị cấm" }}
              </span>
            </td>

            <!-- ONLINE -->
            <td>
              <span :class="user.online_status ? 'text-success' : 'text-muted'">
                {{ user.online_status ? "Online" : "Offline" }}
              </span>
            </td>

            <td>
              <i
                class="bi bi-trash text-danger cursor-pointer"
                @click="deleteUser(user.id)"
              ></i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "@/api/client";

/* ================= FIREBASE ================= */
import { getDatabase, ref as dbRef, onValue, off } from "firebase/database";

const users = ref([]);
const loading = ref(false);
const filters = ref({
  name: "",
  role: "",
  status: "",
});

/* ================= FETCH USERS ================= */
const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await api.get("/api/admin/users");
    users.value = (res.data.data || []).map((u) => ({
      ...u,
      online_status: false, // default OFFLINE
    }));
  } catch (err) {
    console.error("❌ Fetch users error:", err);
  } finally {
    loading.value = false;
  }
};

/* ================= FIREBASE ONLINE ================= */
const db = getDatabase();
const statusRef = dbRef(db, "status");

const statusListener = onValue(statusRef, (snapshot) => {
  const statusData = snapshot.val() || {};

  users.value = users.value.map((u) => ({
    ...u,
    online_status:
      statusData[u.id] && Object.keys(statusData[u.id]).length > 0,
  }));

  console.log("📡 Firebase status:", statusData);
});

/* ================= FILTER ================= */
const filteredUsers = computed(() => {
  return users.value.filter((u) => {
    const matchName = filters.value.name
      ? u.full_name.toLowerCase().includes(filters.value.name.toLowerCase()) ||
        u.user_name.toLowerCase().includes(filters.value.name.toLowerCase())
      : true;

    const matchRole =
      filters.value.role !== "" ? u.role === Number(filters.value.role) : true;

    const matchStatus =
      filters.value.status !== "" ? u.status === filters.value.status : true;

    return matchName && matchRole && matchStatus;
  });
});

/* ================= DELETE ================= */
const deleteUser = async (id) => {
  if (!confirm("Bạn chắc chắn muốn xóa người dùng này?")) return;
  try {
    await api.delete(`/api/admin/users/${id}`);
    users.value = users.value.filter((u) => u.id !== id);
  } catch (err) {
    console.error("❌ Delete user error:", err);
  }
};

/* ================= LIFECYCLE ================= */
onMounted(fetchUsers);
onUnmounted(() => off(statusRef));
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
.table-container {
  max-height: 53vh;
  overflow-y: auto;
}
.sticky-top {
  z-index: 1;
  background-color: white;
}
</style>
