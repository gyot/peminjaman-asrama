<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Daftar Approval</h1>

    <div v-if="loading" class="text-gray-500">Loading data...</div>
    <div v-else>
      <!-- Wrapper untuk membuat tabel responsif -->
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
              <!-- <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Dikonfirmasi Oleh</th> -->
              <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Pemesan</th>
              <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
              <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal Kegiatan</th>
              <!-- <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal Konfirmasi</th> -->
              <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Detail Pengajuan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="approval in approvals" :key="approval.id" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">{{ approval.id }}</td>
              <!-- <td class="py-3 px-4">{{ approval.user?.name || 'Tidak ditemukan' }}</td> -->
              <td class="py-3 px-4">{{ approval.applications?.name || 'Tidak ditemukan' }}</td>
              <td class="py-3 px-4">
                <span :class="statusClass(approval.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                  {{ approval.status }}
                </span>
              </td>
              <td class="py-3 px-4">{{ formatDate(approval.applications?.event_start_date) || '-' }} s.d. {{
                formatDate(approval.applications?.event_end_date) || '-' }}</td>
              <!-- <td class="py-3 px-4">{{ formatDateTime(approval.created_at) }}</td> -->
              <td class="py-3 px-4">
                <router-link :to="`/applications/${approval.id_applications}/detail/approvals/`"
                  class="bg-green-500 text-white px-4 py-2 rounded w-full block text-center hover:bg-green-600 transition-colors duration-300">
                  Detail
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const approvals = ref([])
const loading = ref(true)

const fetchApprovals = async () => {
  try {
    const response = await axios.get('/api/approvals') // Ganti sesuai endpoint kamu
    approvals.value = response.data
  } catch (error) {
    console.error('Error fetching approvals:', error)
  } finally {
    loading.value = false
  }
}
const fetchDataProfil = async () => {
  try {
    const response = await axios.get(`/api/profile/${authStore.user?.id}`);
    console.log(response.data); // Lakukan sesuatu dengan data profil
  } catch (error) {
    router.push("/user/profile");
    console.error("Gagal memuat profil:", error);
  }
};
const statusClass = (status) => {
  switch (status) {
    case 'approved':
      return 'bg-green-100 text-green-700'
    case 'pending':
      return 'bg-yellow-100 text-yellow-700'
    case 'rejected':
      return 'bg-red-100 text-red-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const formatDateTime = (date) => {
  const d = new Date(date);
  const options = {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Makassar' // WITA
  };

  const formatter = new Intl.DateTimeFormat('id-ID', options);
  return formatter.format(d) + ' WITA';
}

onMounted(()=>{
  fetchApprovals();
  fetchDataProfil();
});
</script>

<style scoped>
/* Opsional: tambahkan style tambahan jika perlu */
@media (max-width: 768px) {
  table {
    font-size: 0.875rem; /* Ukuran font lebih kecil */
  }

  th, td {
    padding: 0.5rem; /* Padding lebih kecil */
  }

  td {
    white-space: nowrap; /* Mencegah teks terpotong */
  }

  .overflow-x-auto {
    overflow-x: auto; /* Aktifkan scroll horizontal */
  }
}
</style>
