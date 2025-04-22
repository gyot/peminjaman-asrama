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
        <button @click="openModal"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ubah</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Kirim Pesan Konfirmasi</h3>
        <p class="mb-2">
          <strong>Nama:</strong>
          <input v-model="name" class="w-full border rounded p-2" placeholder=""  />
        </p>
        <p class="mb-2">
          <strong>Email:</strong>
          <input v-model="email" class="w-full border rounded p-2"></input>
        </p>
        <p class="mb-2">
          <strong>Password:</strong>
          <input v-model="password" type="password" class="w-full border rounded p-2"></input>
        </p>

        <div class="flex justify-end space-x-2">
          <button @click="showModal = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Batal</button>
          <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Kirim</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
import Swal from 'sweetalert2';

export default {
  data() {
    const authStore = useAuthStore();
    return {
      authStore,
      showModal: false,
      id: authStore.user?.id || 0,
      name: '',
      email: '',
      password: '',
    };
  },
  computed: {
    details() {
      return {
        'Nama ': this.authStore.user.name,
        'Surel ': this.authStore.user.email,
        'Sandi ': this.authStore.user.password,
      };
    },
  },
  created() {
    this.authStore.fetchUser();
    // this.name = this.authStore.user.name;
    // this.email = this.authStore.user.email;
    // this.password = this.authStore.user.password;
  },
  mounted() {
    // this.getServerHost();
  },
  methods: {
    // async fetchData() {
    //   try {
    //     const response = await axios.get(`/api/applications/${this.id}`);
    //     this.application = response.data;
    //     console.log('Fetched application:', this.application);

    //   } catch (error) {
    //     console.error(error);
    //   }
    // },
    openModal() {
      this.showModal = true;
      this.name = this.authStore.user?.name || '';
      this.email = this.authStore.user?.email || '';
      this.password = ''; // Demi keamanan, kosongkan atau sesuaikan sesuai logika kamu
    },
    closeModal() {
      this.isModalOpen = false;
      this.name = '';
      this.email = '';
      this.password = ''; // Demi keamanan, kosongkan atau sesuaikan sesuai logika kamu
    },
    async submit() {
      Swal.fire({
        title: 'Mohon tunggu...',
        text: 'Sedang mengirim data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        await axios.post(`/user/${this.id}/approval`, {
          status: this.approvalStatus,
          message: this.message,
          id_user: this.authStore.user.id,
          id_applications: this.id,
        });

        await this.sendMessage();
        this.showModal = false;
        await this.fetchData();
      } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal mengirim data!', 'error');
      }
    },

  }
};
</script>

<style scoped>
/* Styling opsional */
</style>
