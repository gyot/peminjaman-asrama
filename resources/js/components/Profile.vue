<template>
    <div class="max-w-4xl mx-auto p-4">
      <div class="bg-white shadow-md rounded-2xl p-6 relative">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Detail User</h2>
  
        <div v-if="isLoading" class="flex justify-center items-center py-10">
          <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v8H4z" />
          </svg>
        </div>
  
        <div v-else-if="profile" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="flex flex-col" v-for="(item, index) in details" :key="index">
            <span class="text-sm text-gray-500">{{ item.label }}:</span>
            <span class="text-base text-gray-800" v-if="!item.isImage">{{ item.value }}</span>
            <img v-else :src="item.value" alt="Foto" class="w-32 h-32 rounded object-cover border" />
          </div>
        </div>
  
        <div v-else class="text-center text-gray-500 py-8">Tidak ada data ditemukan</div>
  
        <div class="mt-6 text-center space-x-4">
          <button @click="openModal" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ubah</button>
        </div>
      </div>
  
      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
          <h3 class="text-xl font-semibold mb-4">Ubah Data</h3>
          <div class="space-y-2">
            <div>
              <label class="font-medium">Tempat Lahir</label>
              <input v-model="form.tempat_lahir" type="text" class="w-full border rounded p-2" />
            </div>
            <div>
              <label class="font-medium">Tanggal Lahir</label>
              <input v-model="form.tanggal_lahir" type="date" class="w-full border rounded p-2" />
            </div>
            <div>
              <label class="font-medium">Alamat</label>
              <input v-model="form.alamat" type="text" class="w-full border rounded p-2" />
            </div>
            <div>
              <label class="font-medium">Nomor HP</label>
              <input v-model="form.nomor_hp" type="text" class="w-full border rounded p-2" />
            </div>
            <div>
              <label class="font-medium">Foto Saat Ini</label><br />
              <img v-if="form.foto" :src="form.foto" alt="Foto profil" class="w-24 h-24 object-cover rounded mb-2 border" />
            </div>
            <div>
              <label class="font-medium">Ganti Foto</label>
              <input type="file" @change="handleFileChange" class="w-full border rounded p-2" />
            </div>
          </div>
          <div class="flex justify-end mt-4 space-x-2">
            <button @click="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
            <button @click="submitForm" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  import { useAuthStore } from '../stores/auth';
  
  export default {
    data() {
      const authStore = useAuthStore();
      return {
        authStore,
        profile: null,
        form: {
          tempat_lahir: '',
          tanggal_lahir: '',
          alamat: '',
          nomor_hp: '',
          foto: null,
          fotoFile: null
        },
        showModal: false,
        isLoading: true
      };
    },
    computed: {
      details() {
        return [
          { label: 'Tempat Lahir', value: this.profile.tempat_lahir },
          { label: 'Tanggal Lahir', value: this.dateFormat(this.profile.tanggal_lahir) },
          { label: 'Alamat', value: this.profile.alamat },
          { label: 'Nomor HP', value: this.profile.nomor_hp },
          { label: 'Foto', value: this.profile.foto, isImage: true }
        ];
      }
    },
    async created() {
      await this.authStore.fetchUser();
      this.fetchData();
    },
    methods: {
      async fetchData() {
        this.isLoading = true;
        try {
          const response = await axios.get(`/api/profile/${this.authStore.user?.id}`);
          this.profile = response.data;
          this.profile.foto = `/storage/images/${this.profile.foto}`;
        } catch (error) {
          console.error("Gagal memuat profil:", error);
        } finally {
          this.isLoading = false;
        }
      },
      openModal() {
        this.form = {
          id: this.authStore.user?.id,
          tempat_lahir: this.profile.tempat_lahir,
          tanggal_lahir: this.profile.tanggal_lahir,
          alamat: this.profile.alamat,
          nomor_hp: this.profile.nomor_hp,
          foto: this.profile.foto,
          fotoFile: null
        };
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
      },
      handleFileChange(event) {
        const file = event.target.files[0];
        this.form.foto = URL.createObjectURL(file);
        this.form.fotoFile = file;
      },
      dateFormat(dateString) {
        return new Date(dateString).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
      },
      async submitForm() {
        if (!this.form.tempat_lahir || !this.form.tanggal_lahir || !this.form.alamat || !this.form.nomor_hp) {
          return Swal.fire('Peringatan', 'Semua field wajib diisi.', 'warning');
        }
  
        let nomor_hp = this.form.nomor_hp.startsWith('0') ? '62' + this.form.nomor_hp.slice(1) : this.form.nomor_hp;
  
        const formData = new FormData();
        formData.append('id', this.form.id);
        formData.append('tempat_lahir', this.form.tempat_lahir);
        formData.append('tanggal_lahir', this.form.tanggal_lahir);
        formData.append('alamat', this.form.alamat);
        formData.append('nomor_hp', nomor_hp);
        if (this.form.fotoFile) formData.append('foto', this.form.fotoFile);
  
        try {
          Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
          await axios.post('/api/update/profiles', formData);
          Swal.fire('Berhasil', 'Profil diperbarui!', 'success');
          this.fetchData();
          this.closeModal();
        } catch (error) {
          Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan.', 'error');
          console.error(error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  /* Spinner animation sudah diatur pakai animate-spin di Tailwind */
  </style>
  