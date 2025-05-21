import { defineStore } from "pinia";
import axios from "axios";
import { useRouter } from "vue-router";

export const useProfileStore = defineStore("profile", {
  state: () => ({
    profileData: null,
  }),
  actions: {
    async fetchProfile(userId) {
      const router = useRouter();
      try {
        const response = await axios.get(`/api/profile/${userId}`);

        // Deteksi struktur respons Laravel
        let profile = null;
        if (Array.isArray(response.data)) {
          profile = response.data[0];
        } else if (Array.isArray(response.data.data)) {
          profile = response.data.data[0];
        } else {
          profile = response.data;
        }

        // console.log("Profil yang dimuat:", profile);

        if (!profile || Object.keys(profile).length === 0) {
          router.push("/user/profile");
        } else {
          this.profileData = profile;
        }
      } catch (error) {
        router.push("/user/profile");
        // console.error("Gagal memuat profil:", error);
      }
    },
  },
});
