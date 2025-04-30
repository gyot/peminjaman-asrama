<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="bg-white shadow-md rounded-2xl p-6">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Detail Pengajuan</h2>

      <div v-if="authStore" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Detail Data -->
        <div class="flex flex-col" v-for="(value, label) in details" :key="label">
          <span class="text-sm text-gray-500">{{ label }}:</span>
          <span class="text-base text-gray-800">{{ value }}</span>
        </div>
      </div>

      <div v-else class="text-center text-gray-500 py-8">Memuat data...</div>

      <!-- Tombol Aksi -->
      <div class="mt-6 text-center space-x-4">
        <button @click="openModal" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
          Ubah
        </button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Kirim Pesan Konfirmasi</h3>
        <p class="mb-2">
          <strong>Nama:</strong>
          <input v-model="name" class="w-full border rounded p-2" placeholder="" />
        </p>
        <p class="mb-2">
          <strong>Email:</strong>
          <input v-model="email" class="w-full border rounded p-2" />
        </p>
        <p class="mb-2">
          <strong>Password:</strong>
          <input v-model="password" type="password" class="w-full border rounded p-2" />
        </p>

        <div class="flex justify-end space-x-2">
          <button @click="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
            Batal
          </button>
          <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Kirim
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../stores/auth";
import Swal from "sweetalert2";
import router from "../router";

const authStore = useAuthStore();
const showModal = ref(false);
const id = ref(authStore.user?.id || 0);
const name = ref("");
const email = ref("");
const password = ref("");

const details = computed(() => ({
  "Nama ": authStore.user?.name || "",
  "Surel ": authStore.user?.email || "",
  "Sandi ": authStore.user?.password || "",
}));

const fetchDataProfil = async () => {
  try {
    const response = await axios.get(`/api/profile/${authStore.user?.id}`);
    console.log(response.data); // Lakukan sesuatu dengan data profil
  } catch (error) {
    router.push("/user/profile");
    console.error("Gagal memuat profil:", error);
  }
};

const openModal = () => {
  showModal.value = true;
  name.value = authStore.user?.name || "";
  email.value = authStore.user?.email || "";
  password.value = ""; // Demi keamanan, kosongkan atau sesuaikan sesuai logika kamu
};

const closeModal = () => {
  showModal.value = false;
  name.value = "";
  email.value = "";
  password.value = ""; // Demi keamanan, kosongkan atau sesuaikan sesuai logika kamu
};

const submit = async () => {
  Swal.fire({
    title: "Mohon tunggu...",
    text: "Sedang mengirim data",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });
  const formData = new FormData();
  formData.append("name", name.value);
  formData.append("email", email.value);
  formData.append("password", password.value);
  formData.append('_method', 'PUT')
  try {
    await axios.post(`api/account/${id.value}`,formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    await authStore.fetchUser(); // Memperbarui data user di store

    Swal.fire("Berhasil", "Data berhasil dirubah!", "success");
    showModal.value = false;
    await fetchDataProfil();
  } catch (error) {
    console.error(error);
    Swal.fire("Error", "Gagal mengirim data!", "error");
  }
};

onMounted(() => {
  authStore.fetchUser();
  fetchDataProfil();
});
</script>

<style scoped>
/* Styling opsional */
</style>
