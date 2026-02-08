<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h3 class="fw-bold">Quản lý Stories</h3>
    </div>
    <p class="text-muted mb-4">Kiểm duyệt và xoá Stories vi phạm</p>

    <div class="row g-3 mb-4 border rounded p-2">
      <div class="col-md-4">
        <p class="fw-semibold mb-1">Tìm kiếm</p>
        <input
          type="text"
          class="form-control"
          placeholder="🔍 Tìm story theo người đăng hoặc URL..."
          v-model="search"
        />
      </div>
    </div>

    <div class="table-responsive row g-3 mb-4 border rounded p-3 table-container">
      <table class="table table-bordered table-hover border align-middle bg-white rounded shadow-sm">
        <thead class="table-light text-center sticky-top">
          <tr>
            <th>ID</th>
            <th>Người đăng</th>
            <th>Nội dung</th>
            <th>Ngày đăng</th>
            <th>Hết hạn lúc</th>
            <th>Hành động</th>
          </tr>
        </thead>

        <tbody class="text-center">
          <tr v-if="loading">
            <td colspan="6">Đang tải dữ liệu...</td>
          </tr>

          <tr v-if="!loading && filteredStories.length === 0">
            <td colspan="6">Không có story phù hợp</td>
          </tr>

          <tr v-for="story in filteredStories" :key="story.id">
            <td>#{{ story.id }}</td>
            <td>{{ story.user_name }}</td>
            <td class="text-truncate" style="max-width: 250px">{{ story.video_url }}</td>
            <td>{{ formatDate(story.created_at) }}</td>
            <td>{{ formatDate(story.expired_time) }}</td>
            <td>
              <i
                class="bi bi-trash text-danger cursor-pointer"
                @click="deleteStory(story.id)"
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

const stories = ref([]);
const loading = ref(false);
const search = ref("");

/**
 * List Story
 */
const fetchStories = async () => {
  loading.value = true;
  try {
    const res = await api.get("/api/admin/stories");
    stories.value = res.data.data || [];
  } catch (err) {
    console.error("Lỗi lấy stories", err);
    stories.value = [];
  } finally {
    loading.value = false;
  }
};

/**
 * Xoá Story
 */
const deleteStory = async (id) => {
  if (!confirm("Bạn chắc chắn muốn xoá story này?")) return;
  try {
    await api.delete(`/api/admin/stories/${id}`);
    stories.value = stories.value.filter((s) => s.id !== id);
  } catch (err) {
    console.error("Lỗi xoá story", err);
  }
};

/**
 * Filter
 */
const filteredStories = computed(() => {
  return stories.value.filter((s) => {
    if (!search.value) return true;
    const term = search.value.toLowerCase();
    return (
      s.user_name.toLowerCase().includes(term) ||
      s.video_url.toLowerCase().includes(term)
    );
  });
});

/**
 * Formate Date
 */
const formatDate = (date) => {
  if (!date) return "";
  return new Date(date).toLocaleString("vi-VN");
};

onMounted(fetchStories);
</script>

<style scoped>
.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cursor-pointer {
  cursor: pointer;
}
.table-container {
  max-height: 50vh;
  overflow-y: auto;
}
.sticky-top {
  z-index: 1;
  background-color: white;
}
</style>
