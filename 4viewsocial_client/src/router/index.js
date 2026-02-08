import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '@/components/auth/Login.vue'
import Signup from '@/components/auth/Signup.vue'
import HomePage from '../views/HomePage.vue'
import SidebarComponent from '@/components/SidebarComponent.vue'
import Explore from '@/views/Explore.vue'
import Message from '@/views/Message.vue'
import Profile from '@/views/Profile.vue'
import EditProfile from '@/views/EditProfile.vue'
import chatapp from '@/views/chatapp.vue'
import AdminLayout from '@/components/layouts/AdminLayout.vue'
import Dashboard from '@/views/admin/Dashboard.vue'
import ListUsers from '@/views/admin/ListUsers.vue'
import ListPosts from '@/views/admin/ListPosts.vue'
// import ListReports from '@/views/admin/ListReports.vue'
import ListStories from '@/views/admin/ListStories.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/homepage',
      name: 'homepage',
      component: HomePage,
    },
    {
      path: '/login',
      name: 'login',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: Login,
    },
    {
      path: '/signup',
      name: 'signup',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: Signup,

    },
    {
      path: '/sidebar',
      name: 'sidebar',
      component: SidebarComponent
    },
    {
      path: '/explore',
      name: 'explore',
      component: Explore
    },
    {
      path: '/message',
      name: 'message',
      component: Message
    },
    {
      path: '/profile/:id?', // nếu không có id thì sẽ hiển thị profile của chính người dùng
      name: 'profile',
      component: Profile
    },
    {
      path: '/edit-profile',
      name: 'edit-profile',
      component: EditProfile
    }, 
    {
      path: '/chatapp',
      name: 'chatapp',
      component: chatapp
    },
    // Router for admin
    {
      path: '/admin',
      component: AdminLayout,
      children: [
        {
          path: 'dashboard',
          component: Dashboard
        },
        {
          path: 'list-users',
          component: ListUsers
        },
        {
          path: 'list-posts',
          component: ListPosts
        },
        // {
        //   path: 'list-reports',
        //   component: ListReports
        // },
        {
          path: 'list-stories',
          component: ListStories
        }
      ]
    },
  ],
})

export default router