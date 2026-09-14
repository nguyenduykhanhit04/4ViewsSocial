<template>
  <RouterView />

  <!-- Tìm kiếm -->
  <!-- <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSearch" style="border-radius: 10px ;">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Tìm kiếm</h5>
      <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
      <input type="text" class="form-control mb-3" placeholder="Nhập tên người dùng..." />
      <div class="d-flex align-items-center mb-2" v-for="i in 5" :key="i">
        <img :src="'https://picsum.photos/40?random=' + i" class="rounded-circle me-2" />
        <span>Người dùng {{ i }}</span>
      </div>
    </div>
  </div> -->
  
  <!-- Thông báo -->
  <!-- <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNotify" style="border-radius: 10px ;">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Thông báo</h5>
      <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="notification-item mb-3 p-2 border rounded" v-for="i in 8" :key="i">
        <b>Lee Min Ho</b> đã thích bài viết của bạn ❤️
        <div class="text-muted small">2 giờ trước</div>
      </div>
    </div>
  </div> -->

  <!-- Modal Upload Ảnh/Video -->
  <!-- Đã xóa để copy sang homepage -->
</template>

<script setup>
import { RouterView } from 'vue-router'
import { ref } from "vue"
import { onMounted } from "vue";
import { initPresence } from "@/presence";

onMounted(() => {
  initPresence();
});


</script>

<style scoped>

</style>
