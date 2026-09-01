import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import DashboardPage from '@/views/DashboardPage.vue';
import AboutPage from '@/views/AboutPage.vue';
import HomePage from '@/views/HomePage.vue';
import ProfilePage from '@/views/ProfilePage.vue';
import EditProfilePage from '@/views/EditProfilePage.vue';
import LoginPage from '@/views/LoginPage.vue';
import RegisterPage from '@/views/RegisterPage.vue';
import EventsPage from '@/views/EventsPage.vue';
import { isAuthenticated } from '@/services/auth';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'About',
    component: AboutPage,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardPage,
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage,
    meta: { guestOnly: true },
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
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { guestOnly: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterPage,
    meta: { guestOnly: true },
  },
  {
    path: '/about',
    redirect: '/',
  },
  {
    path: '/events',
    name: 'Events',
    component: EventsPage,
    meta: { guestOnly: true },
  },
   {
     path: '/ai',
     name: 'AI',
     component: () => import('@/views/AiPage.vue'),
     meta: { guestOnly: true },
   },
   {
     path: '/contact',
     name: 'Contact',
     component: () => import('@/views/ContactPage.vue'),
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
