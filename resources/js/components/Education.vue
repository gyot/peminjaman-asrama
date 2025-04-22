<template>
    <div class="max-w-4xl mx-auto p-4">
      <div class="bg-white shadow-md rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Riwayat Pendidikan</h2>
  
        <div v-if="data" v-for="data in data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Detail Data -->
           <h4>{{ data.tingkat_pendidikan }}</h4>
          <div class="flex flex-col" >
            <span class="text-sm text-gray-500">Institusi:</span>
            <span class="text-base text-gray-800">{{ data.institusi }}</span>
            <span class="text-sm text-gray-500">Juruan:</span>
            <span class="text-base text-gray-800">{{ data.jurusan }}</span>
            <span class="text-sm text-gray-500">Tahun Masuk:</span>
            <span class="text-base text-gray-800">{{ data.tahun_masuk }}</span>
            <span class="text-sm text-gray-500">Tahun Lulus:</span>
            <span class="text-base text-gray-800">{{ data.tahun_lulus }}</span>
          </div>
        </div>
  
        <div v-else class="text-center text-gray-500 py-8">Memuat data...</div>
  
        <!-- Tombol Aksi -->
        <div class="mt-6 text-center space-x-4">
          <button @click="openModal"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Tambah Pendidikan</button>
        </div>
      </div>
  
      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Tambah Pendidikan</h3>
            <p class="mb-2">
                <strong>Tingkat Pendidikan:</strong>
                <input v-model="tingkat_pendidikan" class="w-full border rounded p-2" placeholder=""  />
            </p>
            <p class="mb-2">
                <strong>Institusi:</strong>
                <input v-model="institusi" class="w-full border rounded p-2"></input>
            </p>
            <p class="mb-2">
                <strong>Jurusan:</strong>
                <input v-model="jurusan" class="w-full border rounded p-2"></input>
            </p>
            <p class="mb-2">
                <strong>Tahun Masuk:</strong>
                <input v-model="tahun_masuk" class="w-full border rounded p-2"></input>
            </p>
            <p class="mb-2">
                <strong>Tahun Lulus:</strong>
                <input v-model="tahun_lulus" class="w-full border rounded p-2"></input>
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
        data: [],
        form: {
            user_id: authStore.user?.id || 0, 
            tingkat_pendidikan:'', 
            institusi:'', 
            jurusan:'', 
            tahun_masuk:'', 
            tahun_lulus:'',
        },
      };
    },
    computed: {
      details() {
        return {
        };
      },
    },
    created() {
      this.authStore.fetchUser();
        this.fetchData();
    },
    mounted() {
    },
    methods: {
      async fetchData() {
        try {
          const response = await axios.get(`/api/educations/${this.id}`);
          this.data = response.data;
          console.log('Fetched Education:', this.data);
  
        } catch (error) {
          console.error(error);
        }
      },
      openModal() {
        this.showModal = true;
       
      },
      closeModal() {
        this.isModalOpen = false;
        
      },
      async submit() {
        const formData = {
            ...this.form,
            phone_number
        };

        try {
            Swal.fire({
            title: 'Mengirim...',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await axios.post('/api/setEducations', formData, {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
            });

            
            Swal.fire('Berhasil', 'Data Pendidikan sudah ditambah.', 'success');
            this.$router.push('/selesai');
            this.resetForm();

        } catch (error) {
            console.error(error);
            Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
        }
      },
  
    }
  };
  </script>
  
  <style scoped>
  /* Styling opsional */
  </style>
  