import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("token") || null,
  }),

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post("/auth/login", { email, password });
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem("token", this.token);
        localStorage.setItem("user", JSON.stringify(this.user)); // simpan user
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
        await this.fetchUser(); // opsional, kalau ingin ambil info user lagi
      } catch (error) {
        throw error.response?.data?.message || "Login gagal";
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get("/auth/me", {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        });
        this.user = response.data.user;
        localStorage.setItem("user", JSON.stringify(this.user)); // update user
      } catch (error) {
        // console.error("Gagal mengambil data user:", error);
      }
    },

    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      this.token = null;
      this.user = null;
      delete axios.defaults.headers.common["Authorization"];
    },
  },
});

// Set token ke header saat app dimuat
if (localStorage.getItem("token")) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.getItem("token")}`;
}
