import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { useProfileStore } from "../stores/profile";
import axios from "axios";

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
import Konfirmasi from "../views/Konfirmasi.vue";
import NotFound from "../views/NotFound.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import ResetPassword from "../views/ResetPassword.vue";
import User from "../views/User.vue";

// User components
import Account from "../components/Account.vue";
import Profile from "../components/Profile.vue";
import Education from "../components/Education.vue";
import Position from "../components/Position.vue";

const routes = [
  {
    path: "/",
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "dashboard", component: Dashboard },
      { path: "whatsapp", name: "whatsapp", component: WhatsApp },
      { path: "applications", name: "applications", component: Applications },
      { path: "facilities", name: "facilities", component: Facilities },
      { path: "users", name: "users", component: Users },
      { path: "approvals", name: "approvals", component: Approvals },
      { path: "applications/:id/detail/:approvalStatus", name: "detailapproval", component: ApplicationsDetail },
      { path: "applications/:id", name: "detail", component: ApplicationsDetail },
      {
        path: "user",
        name: "user",
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
    path: "/selesai",
    component: Konfirmasi,
    meta: { requiresAuth: false },
  },
  {
    path: "/auth",
    component: AuthLayout,
    children: [
      { path: "login", component: Login },
      { path: "register", component: Register },
      { path: "reset-password", component: ResetPassword },
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

// Middleware untuk proteksi & cek profil
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const profileStore = useProfileStore();

  if (to.meta.requiresAuth && !authStore.token) {
    localStorage.setItem("intendedRoute", to.fullPath);
    return next("/auth/login");
  }

  if (to.meta.requiresAuth && authStore.token) {
    try {
      if (!profileStore.profileData) {
        const res = await axios.get(`/api/profile/${authStore.user?.id}`);
        const profile = Array.isArray(res.data) ? res.data[0] : res.data;
        console.log("Profile:", profile);

        if (!profile || Object.keys(profile).length === 0) {
          if (to.fullPath !== "/user/profile") {
            return next("/user/profile");
          }
        } else {
          profileStore.profileData = profile;
        }
      }
    } catch (err) {
      console.error("Gagal memuat data profil:", err);
      return next("/user/profile");
    }
  }

  next();
});


export default router;
