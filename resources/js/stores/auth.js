import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("token") || null,
    }),

    actions: {
        async login(email, password) {
            try {
                const response = await axios.post("/auth/login", { email, password });
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem("token", this.token);
                axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
                await this.fetchUser();
            } catch (error) {
                throw error.response?.data?.message || "Login gagal";
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get("/auth/me");
                this.user = response.data.user;
            } catch (error) {
                this.logout(); // ⬅ Langsung logout jika terjadi error
            }
        },

        logout() {
            localStorage.removeItem("token");
            this.token = null;
            this.user = null;
            delete axios.defaults.headers.common["Authorization"];
        },

        initAuth() {
            const token = localStorage.getItem("token");
            if (token) {
                this.token = token;
                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
                return this.fetchUser(); // HARUS di-return
            }
            return Promise.resolve();
        }
    },
});
