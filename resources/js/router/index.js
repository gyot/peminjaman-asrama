import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Import components
import MainLayout from "../components/MainLayout.vue";
import AuthLayout from "../components/AuthLayout.vue";

import WhatsApp from "../views/WhatsApp.vue";
import Dashboard from "../views/Dashboard.vue";
import Applications from "../views/Applications.vue";
import Facilities from "../views/Facilities.vue";
import Users from "../views/Users.vue";
import Approvals from "../views/Approvals.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import ResetPassword from "../views/ResetPassword.vue";
import ApplicationsDetail from "../views/ApplicationsDetail.vue";
import NotFound from '../views/NotFound.vue';

const routes = [
  { path: "/dashboard", component: Dashboard, meta: { requiresAuth: false } },
  { 
      path: "/", 
      component: MainLayout,meta: { requiresAuth: true },
      children: [
        // { path: "", component: Dashboard, meta: { requiresAuth: true } },
        { path: "/whatsapp", name: "whatsapp", component: WhatsApp, meta: { requiresAuth: true } },
        { path: "/applications", name: "applications", component: Applications, meta: { requiresAuth: true } },
        { path: "/facilities", name: "facilities", component: Facilities, meta: { requiresAuth: true } },
        { path: "/users", name: "users", component: Users, meta: { requiresAuth: true } },
        { path: "/approvals", name: "approvals", component: Approvals, meta: { requiresAuth: true } },
        // { path: "/dashboard", name: "dashboard", component: Dashboard, meta: { requiresAuth: true } },
        { path: "/applications/:id", name: "detail", component: ApplicationsDetail, meta: { requiresAuth: true } },
      ]
    },
    {
      path: "/",
      component: AuthLayout, // Menggunakan AuthLayout
      children: [
        { path: "login", component: Login },
        { path: "register", component: Register },
        { path: "reset-password", component: ResetPassword }
      ]
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: NotFound,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Middleware untuk proteksi halaman
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if (to.meta.requiresAuth && !authStore.token) {
    next("/login");
  } else {
    next();
  }
});

export default router;
