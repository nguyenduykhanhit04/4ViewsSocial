<template>
  <div class="container-fluid bg-light">
    <div class="row">
      <AdminSidebar />
      
      <div class="col-9 col-md-10 p-4">
        <AdminHeader />
        <hr />

        <RouterView />
      </div>
    </div>
  </div>
</template>

<script setup>
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import AdminHeader from '@/components/admin/AdminHeader.vue'
import { RouterView } from 'vue-router'
</script>
