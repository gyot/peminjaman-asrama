<template>
  <div class="max-w-xl mx-auto p-4 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4 text-center">Formulir Peminjaman Aula/Asrama</h2>
    <form @submit.prevent="submitForm" class="space-y-4">
      
      <!-- Nama Pemohon -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Pemohon</label>
        <input v-model="form.name" type="text" required
          class="input-field" />
      </div>

      <!-- Alamat -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Alamat</label>
        <textarea v-model="form.address" required class="input-field"></textarea>
      </div>

      <!-- Nama Kegiatan -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
        <input v-model="form.event_name" required type="text" class="input-field" />
      </div>

      <!-- Tanggal Pemakaian -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Tanggal Mulai Pemakaian</label>
        <input v-model="form.event_start_date" type="date" required class="input-field" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Tanggal Selesai Pemakaian</label>
        <input v-model="form.event_end_date" type="date" required class="input-field" />
      </div>

      <!-- Nomor HP -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
        <input v-model="form.phone_number" @input="validatePhoneNumber" type="tel" required class="input-field" />
      </div>

      <!-- Daftar Fasilitas -->
      <div v-if="facilities.length" class="space-y-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Daftar Aula/Asrama</label>
        <div v-for="facility in facilities" :key="facility.id" class="flex items-center space-x-2">
          <input type="checkbox" :value="facility.id" v-model="form.facility_id"
            class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
          <span>{{ facility.name }} / {{ facility.price }} / {{ facility.unit }}</span>
        </div>
      </div>
      <div v-else class="text-red-500 text-sm">Gagal memuat fasilitas. Coba refresh halaman.</div>

      <!-- Catatan Tambahan -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
        <textarea v-model="form.notes" class="input-field"></textarea>
      </div>

      <!-- Submit -->
      <button type="submit" class="submit-btn">
        Submit
      </button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      form: {
        name: '',
        address: '',
        event_name: '',
        event_start_date: '',
        event_end_date: '',
        phone_number: '',
        facility_id: [],
        notes: '',
      },
      facilities: [],
      serverHost: null,
    };
  },
  created() {
    this.fetchFacilities();
    this.getServerHost();
  },
  methods: {
    fetchFacilities() {
      axios.get('/api/facilities')
        .then((res) => {
          this.facilities = res.data;
        })
        .catch(() => {
          Swal.fire('Error', 'Gagal memuat fasilitas.', 'error');
        });
    },
    validatePhoneNumber() {
      this.form.phone_number = this.form.phone_number.replace(/[^0-9+]/g, "");
    },
    async submitForm() {
      if (this.form.facility_id.length === 0) {
        Swal.fire('Oops!', 'Pilih minimal satu fasilitas.', 'warning');
        return;
      }
      if (this.form.event_start_date > this.form.event_end_date) {
        Swal.fire('Oops!', 'Tanggal mulai tidak boleh setelah tanggal selesai.', 'warning');
        return;
      }
      const today = new Date().toISOString().split('T')[0];
      if (this.form.event_start_date < today) {
        Swal.fire('Oops!', 'Tanggal mulai tidak boleh kurang dari hari ini.', 'warning');
        return;
      }

      // Format nomor
      let phone_number = this.form.phone_number;
      if (phone_number.startsWith('+')) {
        phone_number = phone_number.slice(1);
      } else if (phone_number.startsWith('0')) {
        phone_number = '62' + phone_number.slice(1);
      }

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
        const response = await axios.post('/api/setApplications', formData, {
          headers: {
            'X-CSRF-TOKEN': csrfToken
          }
        });

        const id = response.data.id;
        const name = this.form.name;
        const message = `Halo, terdapat permohonan penggunaan sarpras dari ${name}. Silakan cek detailnya di: https://pinjam-sarpras.gdoank.my.id/applications/${id}`;

        await this.sendMessage(message);

        Swal.fire('Berhasil', 'Formulir berhasil dikirim.', 'success');
        this.$router.push('/selesai');
        this.resetForm();

      } catch (error) {
        console.error(error);
        Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
      }
    },
    async sendMessage(message) {
      try {
        await axios.post(this.serverHost + "api/whatsapp/send-message", {
          number: '6287865811603',
          message: message,
        });
      } catch (error) {
        console.error('Error sending WhatsApp message:', error);
      }
    },
    resetForm() {
      this.form = {
        name: '',
        address: '',
        event_name: '',
        event_start_date: '',
        event_end_date: '',
        phone_number: '',
        facility_id: [],
        notes: ''
      };
    },
    getServerHost() {
      axios.get("/api/host")
        .then(response => {
          this.serverHost = response.data[0]?.host || null;
        })
        .catch(() => Swal.fire('Error', 'Gagal mendapatkan server host!', 'error'));
    },
  },
};
</script>

<style scoped>
.input-field {
  @apply mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm;
}
.submit-btn {
  @apply w-full py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium shadow-sm;
}
</style>
