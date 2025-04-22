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
import Konfirmasi from "../views/Konfirmasi.vue";
import Account from '../components/Account.vue';
import Profile from '../components/Profile.vue';
import Education from '../components/Education.vue';
import Position from '../components/Position.vue';
import User from '../views/User.vue';

const routes = [
  { path: "/dashboard", component: Dashboard, meta: { requiresAuth: false } },
  { path: "/selesai", component: Konfirmasi, meta: { requiresAuth: false } },
  { 
      path: "/", 
      component: MainLayout,meta: { requiresAuth: true },
      children: [
        // { path: "", component: Dashboard, meta: { requiresAuth: true } },
        { path: "/whatsapp", name: "whatsapp", component: WhatsApp },
        { path: "/applications", name: "applications", component: Applications },
        { path: "/facilities", name: "facilities", component: Facilities },
        { path: "/users", name: "users", component: Users },
        { path: "/approvals", name: "approvals", component: Approvals },
        { path: "/applications/:id/detail/:approvalStatus", name: "detailapproval", component: ApplicationsDetail },
        { path: "/applications/:id", name: "detail", component: ApplicationsDetail },
        { 
          path: "/user", 
          name: "user", 
          component: User, 
          children: [
          { path: "account", name: "Account", component: Account},
          { path: "profile", name: "Profile", component: Profile},
          { path: "education", name: "Education", component: Education},
          { path: "position", name: "Position", component: Position},
        ]},
        
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
  // if (to.meta.requiresAuth && !authStore.token) {
  //   next("/login");
  // } else {
  //   next();
  // }
  if (to.meta.requiresAuth && !authStore.token) {
    localStorage.setItem("intendedRoute", to.fullPath);
    next("/login");
  } else {
    next();
  }
  
  
  
});

export default router;
