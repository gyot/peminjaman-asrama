import { ref, createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { useProfileStore } from "../stores/profile";
import axios from "axios";

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
import NotFound from "../views/NotFound.vue";
import Konfirmasi from "../views/Konfirmasi.vue";
import Account from "../components/Account.vue";
import Profile from "../components/Profile.vue";
import Education from "../components/Education.vue";
import Position from "../components/Position.vue";
import User from "../views/User.vue";

const routes = [
  { path: "/dashboard", component: Dashboard, meta: { requiresAuth: false } },
  { path: "/selesai", component: Konfirmasi, meta: { requiresAuth: false } },
  {
    path: "/",
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "/whatsapp", name: "whatsapp", component: WhatsApp, meta: { requiresAuth: true } },
      { path: "/applications", name: "applications", component: Applications, meta: { requiresAuth: true } },
      { path: "/facilities", name: "facilities", component: Facilities, meta: { requiresAuth: true } },
      { path: "/users", name: "users", component: Users, meta: { requiresAuth: true } },
      { path: "/approvals", name: "approvals", component: Approvals, meta: { requiresAuth: true } },
      { path: "/applications/:id/detail/:approvalStatus", name: "detailapproval", component: ApplicationsDetail, meta: { requiresAuth: true } },
      { path: "/applications/:id", name: "detail", component: ApplicationsDetail, meta: { requiresAuth: true } },
      {
        path: "/user",
        name: "user",
        component: User,
        meta: { requiresAuth: true },
        children: [
          { path: "account", name: "Account", component: Account, meta: { requiresAuth: true } },
          { path: "profile", name: "Profile", component: Profile, meta: { requiresAuth: true } },
          { path: "education", name: "Education", component: Education, meta: { requiresAuth: true } },
          { path: "position", name: "Position", component: Position, meta: { requiresAuth: true } },
        ],
      },
    ],
  },
  {
    path: "/",
    component: AuthLayout,
    children: [
      { path: "login", component: Login, meta: { requiresAuth: false } },
      { path: "register", component: Register, meta: { requiresAuth: false } },
      { path: "reset-password", component: ResetPassword, meta: { requiresAuth: false } },
    ],
  },
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: NotFound,
    meta: { requiresAuth: false },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Middleware untuk proteksi halaman
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const profileStore = useProfileStore();

  if (to.meta.requiresAuth && !authStore.token) {
    localStorage.setItem("intendedRoute", to.fullPath);
    return next("/login");
  }
  // alert("masuk ke halaman " + authStore.user?.id);
  // if (!profileStore.profileData) {
  //   await profileStore.fetchProfile(authStore.user?.id); // Tunggu hingga data profil selesai dimuat
  // }

  next(); // Lanjutkan navigasi
});

export default router;
