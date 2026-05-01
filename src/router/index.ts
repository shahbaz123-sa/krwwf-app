import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import DashboardPage from '@/views/DashboardPage.vue';
import HomePage from '@/views/HomePage.vue';
import ProfilePage from '@/views/ProfilePage.vue';
import EditProfilePage from '@/views/EditProfilePage.vue';
import { isAuthenticated } from '@/services/auth';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage,
    meta: { guestOnly: true },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: { requiresAuth: true },
  },
  {
    path: '/profile/edit',
    name: 'EditProfile',
    component: EditProfilePage,
    meta: { requiresAuth: true },
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach((to) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    return '/home';
  }

  if (to.meta.guestOnly && isAuthenticated()) {
    return '/dashboard';
  }

  return true;
});

export default router
