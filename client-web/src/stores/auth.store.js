import { defineStore } from "pinia";
import { ref, computed } from "vue";
import authApi from "@/api/auth.api";

/**
 * Pinia Store quản lý trạng thái phiên đăng nhập và thông tin người dùng hiện tại.
 */
export const useAuthStore = defineStore("auth", () => {
  /** @type {import('vue').Ref<Object|null>} */
  const user = ref(JSON.parse(localStorage.getItem("user") || "null"));

  /** @type {import('vue').Ref<string|null>} */
  const token = ref(localStorage.getItem("token") || null);

  /** @type {import('vue').ComputedRef<boolean>} */
  const isAuthenticated = computed(() => !!token.value);

  /**
   * Thiết lập thông tin người dùng và token sau khi đăng nhập thành công.
   *
   * @param {Object} userData Thông tin người dùng
   * @param {string} accessToken Token JWT
   */
  function setAuth(userData, accessToken) {
    user.value = userData;
    token.value = accessToken;
    localStorage.setItem("user", JSON.stringify(userData));
    localStorage.setItem("token", accessToken);
  }

  /**
   * Đăng xuất và xóa sạch thông tin phiên đăng nhập khỏi LocalStorage.
   *
   * @return {Promise<void>}
   */
  async function logout() {
    try {
      if (user.value?.id) {
        await authApi.logout({ user_id: user.value.id });
      }
    } catch (e) {
      console.error("Lỗi khi gọi API đăng xuất:", e);
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem("user");
      localStorage.removeItem("token");
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    setAuth,
    logout,
  };
});

export default useAuthStore;
