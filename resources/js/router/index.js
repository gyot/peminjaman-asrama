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
// import User from "../views/User.vue";

// Components (User Sub-Routes)
import Account from "../components/Account.vue";
import Profile from "../components/Profile.vue";
import Education from "../components/Education.vue";
import Position from "../components/Position.vue";
import User from "../views/User.vue";

// ROUTES
const routes = [
  { path: "/dashboard", component: Dashboard, meta: { requiresAuth: false } },
  { path: "/selesai", component: Konfirmasi, meta: { requiresAuth: false } },
  { path: "/login", name: "Login", component: Login, meta: { requiresAuth: false } },
  { path: "/register", name: "Register", component: Register, meta: { requiresAuth: false } },
  { path: "/reset-password", name: "ResetPassword", component: ResetPassword, meta: { requiresAuth: false } },
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
          { path: "account", name: "Account", component: Account, meta: { requiresAuth: true } },
          { path: "profile", name: "Profile", component: Profile, meta: { requiresAuth: true } },
          { path: "education", name: "Education", component: Education, meta: { requiresAuth: true } },
          { path: "position", name: "Position", component: Position, meta: { requiresAuth: true } },
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
    const response = await axios.get(window.location.origin+"/profile/"+userId);
    console.log(userId);
    
    return response.data;
  } catch (error) {
    return null;
  }
};

// ✅ Navigation Guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.token) {
    localStorage.setItem("intendedRoute", to.fullPath);
    return next("/login");
  }
  // alert("masuk ke halaman " + authStore.user?.id);
  // if (!profileStore.profileData) {
  //   await profileStore.fetchProfile(authStore.user?.id); // Tunggu hingga data profil selesai dimuat
  // }

  next();
});


export default router;
