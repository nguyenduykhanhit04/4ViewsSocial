import { createRouter, createWebHistory } from "vue-router";
import Login from "@/components/auth/Login.vue";
import Signup from "@/components/auth/Signup.vue";
import HomePage from "@/views/HomePage.vue";
import SidebarComponent from "@/components/SidebarComponent.vue";
import Explore from "@/views/Explore.vue";
import Message from "@/views/Message.vue";
import Profile from "@/views/Profile.vue";
import EditProfile from "@/views/EditProfile.vue";
import AdminLayout from "@/components/layouts/AdminLayout.vue";
import Dashboard from "@/views/admin/Dashboard.vue";
import ListUsers from "@/views/admin/ListUsers.vue";
import ListPosts from "@/views/admin/ListPosts.vue";
import ListStories from "@/views/admin/ListStories.vue";

/**
 * Cấu hình định tuyến Router cho toàn bộ ứng dụng Client Web.
 */
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      redirect: "/homepage",
    },
    {
      path: "/homepage",
      name: "homepage",
      component: HomePage,
    },
    {
      path: "/login",
      name: "login",
      component: Login,
    },
    {
      path: "/signup",
      name: "signup",
      component: Signup,
    },
    {
      path: "/sidebar",
      name: "sidebar",
      component: SidebarComponent,
    },
    {
      path: "/explore",
      name: "explore",
      component: Explore,
    },
    {
      path: "/message",
      name: "message",
      component: Message,
    },
    {
      path: "/profile/:id?",
      name: "profile",
      component: Profile,
    },
    {
      path: "/edit-profile",
      name: "edit-profile",
      component: EditProfile,
    },
    // Khu vực trang quản trị Admin
    {
      path: "/admin",
      component: AdminLayout,
      children: [
        {
          path: "",
          redirect: "/admin/dashboard",
        },
        {
          path: "dashboard",
          name: "admin-dashboard",
          component: Dashboard,
        },
        {
          path: "list-users",
          name: "admin-users",
          component: ListUsers,
        },
        {
          path: "list-posts",
          name: "admin-posts",
          component: ListPosts,
        },
        {
          path: "list-stories",
          name: "admin-stories",
          component: ListStories,
        },
      ],
    },
  ],
});

export default router;