<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="bg-white shadow-md rounded-2xl p-6">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Detail Pengajuan {{ authStore.user ?
        authStore.user.name : 'Guest' }}</h2>
      <div v-if="application" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Detail Data -->
        <div class="flex flex-col" v-for="(value, label) in details" :key="label">
          <span class="text-sm text-gray-500">{{ label }}:</span>
          <span class="text-base text-gray-800">{{ value }}</span>
        </div>
        <div class="flex flex-col">
          <span class="text-sm text-gray-500">Status Approval:</span>
          <span :class="[
            'text-base font-semibold',
            application.approval?.status === 'approved' ? 'text-green-600' :
              application.approval?.status === 'rejected' ? 'text-red-600' :
                'text-yellow-600'
          ]">
            {{ application.approval?.status || 'Belum di-approval' }}
          </span>
        </div>
      </div>
      <div v-else class="text-center text-gray-500 py-8">Memuat data...</div>

      <!-- Tombol Aksi -->
      <div class="mt-6 text-center space-x-4">
        <button @click="openModal('approved')"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
          Setujui
        </button>
        <button @click="openModal('rejected')"
          class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
          Tolak
        </button>
        <router-link to="/applications"
          class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
          Kembali
        </router-link>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Kirim Pesan Konfirmasi</h3>
        <p class="mb-2"><strong>Nomor HP:</strong> <input v-model="number" class="w-full border rounded p-2"
            placeholder="Nomor WhatsApp (628xxx)" readonly /> </p>

        <textarea v-model="message" class="w-full border rounded p-2 mb-4" rows="4"
          placeholder="Tulis pesan di sini..."></textarea>
        <div class="flex justify-end space-x-2">
          <button @click="showModal = false"
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Batal</button>
          <button @click="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Kirim</button>
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
    const authStore = useAuthStore(); // <--- dipanggil di dalam data()
    return {
      id: this.$route.params.id,
      application: null,
      showModal: false,
      approvalStatus: null,
      message: '',
      authStore, // <--- simpan ke state data
      id_user: authStore.user ? authStore.user.id : 0, // pastikan optional chaining untuk cegah undefined
      number: '',
      serverHost: null,
    };
  },
  computed: {
    details() {
      return {
        'Nama Pemohon': this.application?.name,
        'Alamat': this.application?.address,
        'Nama Kegiatan': this.application?.event_name,
        'Tanggal Mulai': this.application?.event_start_date,
        'Tanggal Selesai': this.application?.event_end_date,
        'WhatsApp': this.application?.phone_number,
        'Keterangan': this.application?.notes,
        'Nama Fasilitas': this.application?.facility?.name || '-',

      };
    },
  },
  created() {
    this.authStore.fetchUser(); // <--- fetch user di created
    this.fetchData();
  },
  methods: {
    async fetchData() {
      try {
        const response = await axios.get(`/api/applications/${this.id}`);
        this.application = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    openModal(status) {
      this.approvalStatus = status;
      this.showModal = true;
      this.number = this.application?.phone_number;
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
      console.log(this.authStore.user);

      console.log(this.approvalStatus,
        this.message,
        this.id_user,
        this.id);

      try {
        await axios.post(`/api/applications/${this.id}/approval`, {
          status: this.approvalStatus,
          message: this.message,
          id_user: this.authStore.user.id,
          id_applications: this.id,
        });
        // alert('Pesan terkirim!');
        this.sendMessage();
        this.showModal = false;
        this.fetchData(); // refresh data setelah kirim
      } catch (error) {
        console.error(error);
        alert('Gagal mengirim pesan.');
      }
    },
    async sendMessage() {
      if (!this.number || !this.message) {
        Swal.fire('Error', 'Nomor WhatsApp dan pesan tidak boleh kosong!', 'error');
        return;
      }
      try {
        await axios.post(this.serverHost + "api/whatsapp/send-message", {
          number: this.number,
          message: this.message,
        });
        Swal.fire('Sukses', 'Pesan berhasil dikirim!', 'success');
        this.isModalOpen = false;
        this.$router.push('/applications');
      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Gagal mengirim pesan!",
          text: "Coba scan ulang QRCode whatsapp!",
        });
      }
    },
    getServerHost() {
      axios.get("/api/host")
        .then(response => {
          this.serverHost = response.data[0]?.host || null;
        })
        .catch(() => Swal.fire('Error', 'Gagal mendapatkan server host!', 'error'));
    },
  },
  mounted() {
        this.getServerHost();
    }
};
</script>


<style scoped>
/* Tambahan opsional untuk modal */
</style>
