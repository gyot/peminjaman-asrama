import { defineStore } from "pinia";
import axios from "axios";
import { useRouter } from "vue-router"; // Tambahkan import useRouter

export const useProfileStore = defineStore("profile", {
  state: () => ({
    profileData: null,
  }),
  actions: {
    async fetchProfile(userId) {
      const router = useRouter(); // Inisialisasi router
      try {
        const response = await axios.get(`/api/profile/${userId}`);
        this.profileData = response.data;
      } catch (error) {
        router.push('/user/profile'); // Gunakan router.push
        console.error("Gagal memuat profil:", error);
      }
    },
  },
});