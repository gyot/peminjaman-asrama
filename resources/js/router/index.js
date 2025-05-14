import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { useAuthStore } from "../stores/auth";

// Layouts
import MainLayout from "../components/MainLayout.vue";
import AuthLayout from "../components/AuthLayout.vue";

// Views
import Dashboard from "../views/Dashboard.vue";
import WhatsApp from "../views/WhatsApp.vue";
import Applications from "../views/Applications.vue";
import Facilities from "../views/Facilities.vue";
import Users from "../views/Users.vue";
import Approvals from "../views/Approvals.vue";
import ApplicationsDetail from "../views/ApplicationsDetail.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import ResetPassword from "../views/ResetPassword.vue";
import Konfirmasi from "../views/Konfirmasi.vue";
import NotFound from "../views/NotFound.vue";
import User from "../views/User.vue";

// Components (User Sub-Routes)
import Account from "../components/Account.vue";
import Profile from "../components/Profile.vue";
import Education from "../components/Education.vue";
import Position from "../components/Position.vue";

// axios.defaults.baseURL = 'http://localhost:8002';
axios.defaults.withCredentials = true;
// ROUTES
const routes = [
  {
    path: "/",
    component: AuthLayout,
    children: [
      { path: "login", name: "Login", component: Login, meta: { requiresAuth: false } },
      { path: "register", name: "Register", component: Register, meta: { requiresAuth: false } },
      { path: "reset-password", name: "ResetPassword", component: ResetPassword, meta: { requiresAuth: false } },
    ],
  },
  {
    path: "/",
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "", redirect: "/dashboard" },
      { path: "dashboard", name: "Dashboard", component: Dashboard },
      { path: "selesai", name: "Konfirmasi", component: Konfirmasi },
      { path: "whatsapp", name: "WhatsApp", component: WhatsApp },
      { path: "applications", name: "Applications", component: Applications },
      { path: "facilities", name: "Facilities", component: Facilities },
      { path: "users", name: "Users", component: Users },
      { path: "approvals", name: "Approvals", component: Approvals },
      { path: "applications/:id/detail/:approvalStatus", name: "DetailApproval", component: ApplicationsDetail },
      { path: "applications/:id", name: "ApplicationDetail", component: ApplicationsDetail },
      {
        path: "user",
        component: User,
        children: [
          { path: "account", name: "Account", component: Account },
          { path: "profile", name: "Profile", component: Profile },
          { path: "education", name: "Education", component: Education },
          { path: "position", name: "Position", component: Position },
        ],
      },
    ],
  },
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: NotFound,
    meta: { requiresAuth: false },
  },
];

// CREATE ROUTER
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// ✅ Function untuk cek data profil user
const fetchDataProfil = async (userId) => {
  try {
    const response = await axios.get(`/api/profile/${userId}`);
    return response.data;
  } catch (error) {
    console.error("Gagal mengambil data profil:", error.response?.status);
    return null;
  }
};

// ✅ Navigation Guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  const publicPages = ["/login", "/register", "/reset-password", "/not-found"];
  const authRequired = to.matched.some(record => record.meta.requiresAuth);

  if (!authRequired) {
    return next();
  }

  if (!authStore.user || !authStore.token) {
    return next("/login");
  }

  const userId = authStore.user.id;
  if (!userId) {
    console.warn("User ID tidak ditemukan.");
    return next("/login");
  }

  const profil = await fetchDataProfil(userId);
  console.log("Data profil:", profil);

  // Jika belum punya profil dan bukan sedang menuju halaman buat profil
  // if (!profil && to.name !== "Profile") {
  //   return next({ name: "Profile" });
  // }

  next();
});

export default router;
