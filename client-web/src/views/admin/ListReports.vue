<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h3 class="fw-bold">Theo dõi vi phạm</h3>
    </div>

    <p class="text-muted mb-4">Giám sát và xử lý các vi phạm của người dùng</p>

    <div class="row g-3 mb-4 border rounded p-2">
      <input
        type="text"
        class="form-control"
        placeholder="🔍 Tìm vi phạm theo username..."
        v-model="search"
      />
    </div>

    <div class="table-responsive row g-3 mb-4 border rounded p-3 table-container">
      <table class="table table-bordered table-hover border align-middle bg-white rounded shadow-sm">
        <thead class="table-light text-center sticky-top">
          <tr>
            <th>ID</th>
            <th>Người vi phạm</th>
            <th>Số lần vi phạm</th>
            <th>Login fail</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>

        <tbody class="text-center">
          <tr v-if="loading">
            <td colspan="6">Đang tải dữ liệu...</td>
          </tr>

          <tr v-if="!loading && filteredReports.length === 0">
            <td colspan="6">Không có dữ liệu phù hợp</td>
          </tr>

          <tr v-for="report in filteredReports" :key="report.id">
            <td>#{{ report.id }}</td>
            <td>{{ report.username }}</td>
            <td>{{ report.violation_count }}</td>
            <td>{{ report.login_fail_count }}</td>
            <td>{{ report.status }}</td>
            <td class="d-flex gap-1 justify-content-center">
              <button class="btn btn-success btn-sm" @click="approve(report.id)">Duyệt</button>
              <button class="btn btn-danger btn-sm" @click="reject(report.id)">Từ chối</button>
              <button class="btn btn-secondary btn-sm" @click="reopen(report.id)">Mở lại</button>
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

const reports = ref([]);
const loading = ref(false);
const search = ref("");

/**
 * List Report
 */
const fetchReports = async () => {
  loading.value = true;
  try {
    const res = await api.get("/api/admin/reports");
    reports.value = res.data.data || [];
  } catch (err) {
    console.error("Lỗi lấy reports", err);
    reports.value = [];
  } finally {
    loading.value = false;
  }
};

/**
 * Filter
 */
const filteredReports = computed(() => {
  if (!search.value) return reports.value;
  const term = search.value.toLowerCase();
  return reports.value.filter(r => r.username.toLowerCase().includes(term));
});

/**
 * Action
 */
const approve = (id) => {
  console.log("Approve", id);
};
const reject = (id) => {
  console.log("Reject", id);
};
const reopen = (id) => {
  console.log("Reopen", id);
};

onMounted(fetchReports);
</script>

<style scoped>
.table-container {
  max-height: 50vh;
  overflow-y: auto; 
}
.sticky-top {
  z-index: 1;
  background-color: white;
}
</style>
