<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="bg-white shadow-md rounded-2xl p-6">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Detail Pengajuan</h2>

      <div v-if="application" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Detail Data -->
        <div class="flex flex-col" v-for="(value, label) in details" :key="label">
          <span class="text-sm text-gray-500">{{ label }}:</span>
          <span class="text-base text-gray-800">{{ value }}</span>
        </div>

        <!-- Fasilitas -->
        <div class="flex flex-col sm:col-span-2">
          <span class="text-sm text-gray-500">Fasilitas:</span>
          <ul class="list-disc list-inside text-base text-gray-800">
            <li v-for="facility in application.facilities" :key="facility.id">
              {{ facility.name }}
            </li>
          </ul>
        </div>

        <!-- Status Approval -->
        <div class="flex flex-col">
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
      <div v-if="approval === null" class="mt-6 text-center space-x-4">
        <button @click="openModal('approved')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Setujui</button>
        <button @click="openModal('rejected')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Tolak</button>
        <router-link to="/applications" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Kembali</router-link>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Kirim Pesan Konfirmasi</h3>
        <p class="mb-2">
          <strong>Nomor HP:</strong>
          <input v-model="number" class="w-full border rounded p-2" placeholder="Nomor WhatsApp (628xxx)" readonly />
        </p>

        <textarea v-model="message" class="w-full border rounded p-2 mb-4" rows="4" placeholder="Tulis pesan di sini..."></textarea>

        <div class="flex justify-end space-x-2">
          <button @click="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Batal</button>
          <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Kirim</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Router dan Auth
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// State
const id = ref(route.params.id);
const approval = ref(route.params.approvalStatus ?? null);
const application = ref(null);
const showModal = ref(false);
const approvalStatus = ref(null);
const message = ref("");
const number = ref("");
const serverHost = ref(null);

// Computed untuk detail aplikasi
const details = computed(() => ({
  "Nama Pemohon": application.value?.name,
  "Alamat": application.value?.address,
  "Nama Kegiatan": application.value?.event_name,
  "Tanggal Mulai": application.value?.event_start_date,
  "Tanggal Selesai": application.value?.event_end_date,
  "WhatsApp": application.value?.phone_number,
  "Keterangan": application.value?.notes,
}));

// Fetch data aplikasi
const fetchData = async () => {
  try {
    const response = await axios.get(`/api/applications/${id.value}`);
    application.value = response.data;
  } catch (error) {
    console.error("Error fetching application data:", error);
  }
};

// Buka modal
const openModal = (status) => {
  approvalStatus.value = status;
  showModal.value = true;
  number.value = application.value?.phone_number;
};

// Tutup modal
const closeModal = () => {
  showModal.value = false;
  number.value = "";
  message.value = "";
};

// Submit approval
const submit = async () => {
  Swal.fire({
    title: "Mohon tunggu...",
    text: "Sedang mengirim data",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  try {
    await axios.post(`/api/applications/${id.value}/approval`, {
      status: approvalStatus.value,
      message: message.value,
      id_user: authStore.user.id,
      id_applications: id.value,
    });

    await sendMessage();
    showModal.value = false;
    await fetchData();
    Swal.fire("Sukses", "Data berhasil dikirim!", "success");
  } catch (error) {
    console.error("Error submitting approval:", error);
    Swal.fire("Error", "Gagal mengirim data!", "error");
  }
};

// Kirim pesan WhatsApp
const sendMessage = async () => {
  if (!number.value || !message.value) {
    Swal.fire("Error", "Nomor WhatsApp dan pesan tidak boleh kosong!", "error");
    return;
  }
  try {
    await axios.post(`${serverHost.value}api/whatsapp/send-message`, {
      number: number.value,
      message: message.value,
    });
    Swal.fire("Sukses", "Pesan berhasil dikirim!", "success");
    router.push("/applications");
  } catch (error) {
    console.error("Error sending WhatsApp message:", error);
    Swal.fire("Error", "Gagal mengirim pesan WhatsApp! Periksa koneksi WhatsApp Gateway.", "error");
  }
};

// Fetch server host
const getServerHost = async () => {
  try {
    const response = await axios.get("/host");
    serverHost.value = response.data[0]?.host || null;
  } catch (error) {
    console.error("Error fetching server host:", error);
    Swal.fire("Error", "Gagal mendapatkan server host!", "error");
  }
};

const fetchDataProfil = async () => {
  try {
    const response = await axios.get(`/api/profile/${authStore.user?.id}`);
    console.log(response.data); // Lakukan sesuatu dengan data profil
  } catch (error) {
    router.push("/user/profile");
    console.error("Gagal memuat profil:", error);
  }
};

// Lifecycle hook
onMounted(() => {
  authStore.fetchUser();
  fetchData();
  getServerHost();
  fetchDataProfil();
});
</script>

<style scoped>
/* Styling opsional */
</style>
