<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h3 class="fw-bold">Quản lý bài đăng</h3>
    </div>
    <p class="text-muted mb-4">Kiểm duyệt và quản lý nội dung bài đăng</p>

    <div class="row g-3 mb-4 mt-1 border rounded p-2">
      <input
        v-model="search"
        type="text"
        class="form-control"
        placeholder="🔍 Tìm người đăng, bài đăng"
      />
    </div>

    <div class="table-responsive row g-3 mb-4 border rounded p-3 table-container">
      <table class="table table-bordered table-hover align-middle bg-white rounded shadow-sm">
        <thead class="text-center table-light sticky-top">
          <tr>
            <th>ID</th>
            <th>Người đăng</th>
            <th>Nội dung</th>
            <th>Lượt thích</th>
            <th>Lượt bình luận</th>
            <th>Ngày đăng</th>
            <th width="120">Hành động</th>
          </tr>
        </thead>

        <tbody class="text-center">
          <tr v-if="loading">
            <td colspan="7">Đang tải dữ liệu...</td>
          </tr>

          <tr v-if="!loading && filteredPosts.length === 0">
            <td colspan="7">Không có dữ liệu phù hợp</td>
          </tr>

          <tr v-for="post in filteredPosts" :key="post.id">
            <td>#{{ post.id }}</td>
            <td>{{ post.user_name }}</td>
            <td class="text-start">{{ post.caption }}</td>
            <td>{{ post.total_like }}</td>
            <td>{{ post.total_comment }}</td>
            <td>{{ formatDate(post.created_at) }}</td>
            <td>
              <i
                class="bi bi-trash text-danger cursor-pointer"
                @click="deletePost(post.id)"
              ></i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/api/client";

const posts = ref([]);
const loading = ref(false);
const search = ref("");

/**
 * Lấy danh sách posts 1 lần khi mount
 */
const fetchPosts = async () => {
  loading.value = true;
  try {
    const res = await api.get("/api/admin/posts");
    posts.value = res.data.data || [];
  } catch (err) {
    console.error("Lỗi lấy posts:", err);
    posts.value = [];
  } finally {
    loading.value = false;
  }
};

/**
 * Filter posts theo search
 */
const filteredPosts = computed(() => {
  if (!search.value) return posts.value;
  return posts.value.filter(p =>
    p.caption.toLowerCase().includes(search.value.toLowerCase()) ||
    p.user_name.toLowerCase().includes(search.value.toLowerCase())
  );
});

/**
 * Xoá post
 */
const deletePost = async (id) => {
  if (!confirm("Bạn chắc chắn muốn xóa bài đăng này?")) return;
  try {
    await api.delete(`/api/admin/posts/${id}`);
    posts.value = posts.value.filter(p => p.id !== id);
  } catch (err) {
    console.error("Lỗi xóa post:", err);
  }
};

/**
 * Format Date
 */
const formatDate = (date) => new Date(date).toLocaleString("vi-VN");

onMounted(fetchPosts);
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
.table-container {
  max-height: 57vh;
  overflow-y: auto; 
}
.sticky-top {
  z-index: 1;
  background-color: white;
}
</style>
