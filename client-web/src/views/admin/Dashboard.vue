<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h3 class="fw-bold">Tổng quan Dashboard</h3>
    </div>

    <p class="text-muted">Thống kê và giám sát hệ thống</p>

    <!-- STAT BOXES -->
    <div class="row g-3 mt-1">
      <div class="col-md-3" v-for="item in statBoxes" :key="item.label">
        <div class="p-3 bg-white shadow-sm rounded">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1">{{ item.label }}</p>
              <h4 class="fw-bold">{{ item.value }}</h4>
            </div>
            <div :class="`px-3 py-2 rounded d-flex align-items-center justify-content-center ${item.bg}`">
              <i :class="`${item.icon} text-white fs-3`"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="row g-3 mt-4">
      <div class="col-md-6">
        <div class="p-4 bg-white shadow-sm rounded">
          <h6 class="fw-bold mb-3">Tăng trưởng người dùng</h6>
          <canvas ref="userChart"></canvas>
        </div>
      </div>

      <div class="col-md-6">
        <div class="p-4 bg-white shadow-sm rounded">
          <h6 class="fw-bold mb-3">Thống kê bài đăng</h6>
          <canvas ref="postChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/* ================= VUE ================= */
import { ref, computed, onMounted, onUnmounted } from "vue";

/* ================= API ================= */
import api from "@/api/client";

/* ================= CHART ================= */
import Chart from "chart.js/auto";

/* ================= FIREBASE ================= */
import { getDatabase, ref as dbRef, onValue, off } from "firebase/database";

/* ================= STATE ================= */
const stats = ref({
  total_users: 0,
  online_users: 0,
  total_posts: 0,
  totalViolenceWarnings: 0,
});

/* ================= STAT BOXES ================= */
const statBoxes = computed(() => [
  {
    label: "Tổng người dùng",
    value: stats.value.total_users,
    icon: "bi bi-people-fill",
    bg: "bg-primary",
  },
  {
    label: "Người dùng online",
    value: stats.value.online_users,
    icon: "bi bi-activity",
    bg: "bg-success",
  },
  {
    label: "Tổng bài đăng",
    value: stats.value.total_posts,
    icon: "bi bi-file-post",
    bg: "bg-secondary",
  },
  {
    label: "Vi phạm chưa xử lý",
    value: stats.value.totalViolenceWarnings,
    icon: "bi bi-exclamation-triangle",
    bg: "bg-danger",
  },
]);

/* ================= FIREBASE ONLINE USERS ================= */
const db = getDatabase();
const statusRef = dbRef(db, "status");

function initOnlineListener() {
  console.log("📡 Init Firebase online listener");

  onValue(statusRef, (snapshot) => {
    const data = snapshot.val();

    if (!data) {
      stats.value.online_users = 0;
      return;
    }

    // user online nếu có >= 1 connection
    stats.value.online_users = Object.keys(data).filter(
      (uid) => data[uid] && Object.keys(data[uid]).length > 0
    ).length;

    console.log("🟢 Online users:", stats.value.online_users);
  });
}

/* ================= FETCH STATS ================= */
async function fetchDashboardStats() {
  try {
    console.log("📦 Fetch dashboard stats");
    const res = await api.get("/api/admin/dashboard");
    console.log("📊 API dashboard:", res.data);

    stats.value = {
      ...stats.value,
      ...res.data.data,
    };
  } catch (err) {
    console.error("❌ Fetch stats error:", err);
  }
}

/* ================= CHARTS ================= */
const userChart = ref(null);
const postChart = ref(null);

let userChartInstance = null;
let postChartInstance = null;

async function fetchDashboardCharts() {
  try {
    console.log("📈 Fetch dashboard charts");
    const res = await api.get("/api/admin/dashboard/chart");
    console.log("📉 Chart data:", res.data);

    const data = res.data.data ?? res.data;

    if (userChartInstance) userChartInstance.destroy();
    userChartInstance = new Chart(userChart.value, {
      type: "line",
      data: {
        labels: data.user_growth.labels,
        datasets: [
          {
            label: "Người dùng mới",
            data: data.user_growth.data,
            borderWidth: 2,
            tension: 0.4,
          },
        ],
      },
      options: { responsive: true },
    });

    if (postChartInstance) postChartInstance.destroy();
    postChartInstance = new Chart(postChart.value, {
      type: "bar",
      data: {
        labels: data.post_stats.labels,
        datasets: [
          { label: "Bài đăng", data: data.post_stats.posts },
          { label: "Vi phạm", data: data.post_stats.violations },
        ],
      },
      options: { responsive: true },
    });
  } catch (err) {
    console.error("❌ Chart error:", err);
  }
}

/* ================= LIFECYCLE ================= */
onMounted(() => {
  console.log("🚀 Dashboard mounted");
  fetchDashboardStats();
  fetchDashboardCharts();
  initOnlineListener();
});

onUnmounted(() => {
  console.log("🧹 Dashboard unmounted → remove Firebase listener");
  off(statusRef);
});
</script>
