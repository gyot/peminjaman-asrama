<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Daftar Permohonan Peminjaman</h1>
      <div class="space-x-2">
        <button @click="exportExcel" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Excel</button>
        <button @click="exportPDF" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">PDF</button>
      </div>
    </div>

    <div class="mb-4">
      <input v-model="search" type="text" placeholder="Cari berdasarkan nama pemohon/kegiatan..."
        class="border rounded p-2 w-full" />
    </div>

    <!-- Tabel Responsif -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr>
            <th class="border px-4 py-2">Nama Pemohon</th>
            <th class="border px-4 py-2">Tanggal Permohonan</th>
            <th class="border px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="application in paginatedData" :key="application.id">
            <td class="border px-4 py-2">{{ application.name }}</td>
            <td class="border px-4 py-2">{{ formatDateTime(application.created_at) }}</td>
            <td class="border px-4 py-2">
              <router-link :to="`/applications/${application.id}`"
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Detail</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-between items-center">
      <p>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} entri</p>
      <div class="space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1"
          class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">Sebelumnya</button>
        <button @click="nextPage" :disabled="currentPage === totalPages"
          class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">Berikutnya</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import * as XLSX from "xlsx";
import jsPDF from "jspdf";
import "jspdf-autotable";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const application = ref([]);
const search = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(10);

const filteredData = computed(() => {
  const keyword = search.value.toLowerCase();
  return application.value.filter(app =>
    app.name.toLowerCase().includes(keyword) ||
    app.event_name.toLowerCase().includes(keyword)
  );
});

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value));

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredData.value.slice(start, start + itemsPerPage.value);
});

// const fetchDataProfil = async () => {
//   try {
//     const response = await axios.get(`/api/profile/${authStore.user?.id}`);
//     // console.log(response.data); // Lakukan sesuatu dengan data profil
//   } catch (error) {
//     router.push("/user/profile");
//     console.error("Gagal memuat profil:", error);
//   }
// };

const formatDateTime = (date) => {
  const d = new Date(date);
  const options = {
    day: "2-digit",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: false,
    timeZone: "Asia/Makassar", // WITA
  };
  const formatter = new Intl.DateTimeFormat("id-ID", options);
  return formatter.format(d) + " WITA";
};

const fetchData = async () => {
  try {
    const response = await axios.get("/api/applications");
    application.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const exportExcel = () => {
  const ws = XLSX.utils.json_to_sheet(filteredData.value);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Permohonan");
  XLSX.writeFile(wb, "permohonan.xlsx");
};

const exportPDF = () => {
  const doc = new jsPDF();
  doc.autoTable({
    head: [["Nama Pemohon", "Tanggal Permohonan"]],
    body: filteredData.value.map(a => [a.name, formatDateTime(a.created_at)]),
  });
  doc.save("permohonan.pdf");
};

onMounted(() => {
  fetchData();
  // fetchDataProfil();
});
</script>

<style scoped>
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

  img {
    width: 40px; /* Ukuran gambar lebih kecil */
    height: 40px;
  }
}
</style>