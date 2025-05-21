<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <Sidebar :isOpen="isMenuOpen" @close="isMenuOpen = false" />

    <!-- Content -->
    <div class="flex-1 flex flex-col">
      <Header @toggleMenu="toggleMenu" />

      <!-- Main Content -->
      <main class="p-6 overflow-auto flex-1">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Sidebar from "../components/Sidebar.vue";
import Header from "../components/Header.vue";
import { useAuthStore } from "../stores/auth";
import axios from "axios";
import { useRouter } from "vue-router";

// const router = useRouter();
const authStore = useAuthStore();
const isMenuOpen = ref(false);
// const profileData = ref(null); // Deklarasikan profileData sebagai ref

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

// const getDataProfile = async () => {
//   try {
//     const response = await axios.get(`/api/profile/${authStore.user?.id}`);
//     profileData.value = response.data; // Simpan data profil ke profileData
//   } catch (error) {
//     router.push("/user/profile");
//     console.error("Gagal memuat profil:", error);
//   }
// };

onMounted(async () => {
  authStore.fetchUser(); // Ambil data user saat komponen dimuat
  // if (!profileData.value) {
  //   await getDataProfile(); // Panggil fungsi jika profileData masih kosong
  // }
});
</script>
