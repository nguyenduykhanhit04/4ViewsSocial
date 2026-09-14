<template>
  <div class="col-3 col-md-2 bg-white border-end min-vh-100 p-3 d-flex flex-column">
    <h4 class="fw-bold mb-4 text-center">4ViewsSocial</h4>

    <div class="nav flex-column gap-1">
      <RouterLink
        to="/admin/dashboard"
        class="nav-link text-dark"
        active-class="active bg-dark text-white rounded"
      >
        <i class="bi bi-house-door me-2"></i>
        Dashboard
      </RouterLink>

      <RouterLink
        to="/admin/list-users"
        class="nav-link text-dark"
        active-class="active bg-dark text-white rounded"
      >
        <i class="bi bi-people me-2"></i>
        Người dùng
      </RouterLink>

      <RouterLink
        to="/admin/list-posts"
        class="nav-link text-dark"
        active-class="active bg-dark text-white rounded"
      >
        <i class="bi bi-file-earmark-text me-2"></i>
        Bài đăng
      </RouterLink>

      <RouterLink
        to="/admin/list-stories"
        class="nav-link text-dark"
        active-class="active bg-dark text-white rounded"
      >
        <i class="bi bi-camera me-2"></i>
        Stories
      </RouterLink>

      <!-- <RouterLink
        to="/admin/list-reports"
        class="nav-link text-dark"
        active-class="active bg-dark text-white rounded"
      >
        <i class="bi bi-exclamation-triangle me-2"></i>
        Vi phạm
      </RouterLink> -->
    </div>

    <button
      class="btn btn-danger mt-auto w-100"
      @click="logout"
    >
      <i class="bi bi-box-arrow-right me-2"></i>
      Đăng xuất
    </button>
  </div>
</template>

<script setup>
import { RouterLink, useRouter } from "vue-router";

const router = useRouter();

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");

  router.push("/login");
};
</script>
