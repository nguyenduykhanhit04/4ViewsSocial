<template>

</template>

<script setup>
import {
    ref,
    reactive,
    watch,
    watchEffect,
    onMounted,
    onUnmounted,
    onUpdated
} from "vue";

import api from "@/api/client";

// nhận props
const props = defineProps({
    title: {
        type: String,
        default: "Demo component"
    }
});

// state cơ bản
const count = ref(0);
const loading = ref(false);
const error = ref(null);
const user = ref(null);

// state dạng object (reactive)
const state = reactive({
    message: "Hello",
    version: 1
});

// gọi API
async function fetchUser() {
    loading.value = true;
    error.value = null;

    try {
        const res = await api.get("/api/ten_service_goi_den/hehe");
        user.value = res.data;
    } catch (err) {
        error.value = "Lỗi khi tải dữ liệu";
    } finally {
        loading.value = false;
    }
}

// hàm demo
const increase = () => {
    count.value++;
};

// --- WATCH ---
watch(count, (newVal, oldVal) => {
    console.log("count thay đổi:", oldVal, "→", newVal);
});

watch(
    () => state.version,
    (v) => console.log("Version changed:", v)
);

// Tự chạy lại khi phụ thuộc thay đổi
watchEffect(() => {
    console.log("watchEffect chạy lại:", count.value, state.version);
});

// --- LIFE CYCLE ---
onMounted(() => {
    console.log("Component mounted");
    fetchUser();
});

onUpdated(() => {
    console.log("Component updated");
});

onUnmounted(() => {
    console.log("Component destroyed");
});
</script>

<style scoped>
/* Style optional */
</style>
