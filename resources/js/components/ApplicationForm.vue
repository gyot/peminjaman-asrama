<template>
  <div class="max-w-xl mx-auto p-2 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">- Formulir Peminjaman Aula/Asrama -</h2>
    <form @submit.prevent="submitForm" class="space-y-4">
      <!-- Nama Pemohon -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Pemohon</label>
        <input v-model="form.name" type="text" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>

      <!-- Alamat -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Alamat</label>
        <textarea v-model="form.address" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
      </div>

      <!-- Nama Kegiatan -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
        <textarea v-model="form.event_name" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
      </div>

      <!-- Tanggal Pemakaian -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Tanggal Mulai Pemakaian</label>
        <input v-model="form.event_start_date" type="date" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Tanggal Selesai Pemakaian</label>
        <input v-model="form.event_end_date" type="date" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>

      <!-- Nomor HP -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
        <input v-model="form.phone_number" @input="validatePhoneNumber" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
      </div>

      <!-- Daftar Aula/Asrama -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Daftar Aula/Asrama</label>
        <select v-model="form.facility_id" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <option v-for="facility in facilities" :key="facility.id" :value="facility.id">
            {{ facility.name }} / {{ facility.price }} / {{ facility.unit }}
          </option>
        </select>
      </div>

      <!-- Catatan Tambahan -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
        <textarea v-model="form.notes" required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
      </div>

      <!-- Submit Button -->
      <button type="submit"
        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
        facility_id: null,
        notes: '',
        serverHost: null,
      },
      facilities: [],
      title: 'Formulir Permohonan Peminjaman'
    };
  },
  created() {
    axios.get('/api/facilities').then((response) => {
      this.facilities = response.data;
    });
    this.getServerHost();
  },
  methods: {
    validatePhoneNumber() {
      this.form.phone_number = this.form.phone_number.replace(/[^0-9+]/g, "");
    },
    messages(id, name) {
      return `Halo, terdapat permohonan penggunaan sarpras dari ${name}. Silakan cek detailnya di: https://pinjam-sarpras.gdoank.my.id/applications/${id}`;
    },
    async sendMessage(message) {
      try {
       
        await axios.post(this.serverHost+"api/whatsapp/send-message", {
          number: '6287865811603',
          message: message,
        }).then((response) => {
          console.log(response.data);
          
        });
      } catch (error) {
        console.error('Error sending message:', error);
      }
    },
    async submitForm() {
      let phone_number = 0;
      if (this.form.phone_number.charAt(0) == '+') {
        phone_number = this.form.phone_number.replace(this.form.phone_number.charAt(0), '')
      } else if (this.form.phone_number.charAt(0) == '0') {
        phone_number = this.form.phone_number.replace(this.form.phone_number.charAt(0), '62')
      }
      const formData = {
        facility_id: this.form.facility_id,
        name: this.form.name,
        event_name: this.form.event_name,
        event_start_date: this.form.event_start_date,
        event_end_date: this.form.event_end_date,
        address: this.form.address,
        phone_number: phone_number,
        notes: this.form.notes,
      };
      // console.log(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      console.log(formData);
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      try {
        Swal.fire({
          title: 'Harap Tunggu',
          text: 'Sedang memproses data...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        })
        await axios.post('/api/setApplications', formData, {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        }).then((response) => {
          const id = response.data.id;
          const name = this.form.name;
  
          // Buat pesan
          const message = this.messages(id, name);
  
          // Kirim pesan WhatsApp
          this.sendMessage(message);
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Pesan berhasil dikirim!',
            showConfirmButton: false,
            timer: 1500
          });
          this.$router.push('/selesai');
        });
        // Simpan data form dulu (contoh saja)
        // const response = await axios.post('/api/submit', this.form);

        // Ambil ID dari response backend (sesuaikan dengan backend kamu)

        // Reset form atau kasih notifikasi sukses
        this.resetForm();
      } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan saat mengirim formulir.');
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
        facility_id: '',
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
