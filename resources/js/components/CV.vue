<template>
  <button
    @click="downloadPDF"
    class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
  >
    Download PDF
  </button>

  <div
    ref="cvContent"
    v-if="user"
    class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-md font-sans text-gray-800"
  >
    <!-- Header & Foto -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-4 sm:space-y-0">
      <img
        :src="getFotoUrl(user.profile)"
        alt="Foto Profil"
        class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 mx-auto sm:mx-0"
      />
      <div>
        <h1 class="text-3xl font-semibold">{{ user.name }}</h1>
        <p class="text-lg text-gray-500">{{ professionSummary || "Memuat profesi..." }}</p>
      </div>
    </div>

    <!-- Data Pribadi -->
    <div class="mt-6 space-y-2">
      <div class="flex items-center space-x-2">
        <span class="material-icons">email</span>
        <p>{{ user.email }}</p>
      </div>
      <div class="flex items-center space-x-2">
        <span class="material-icons">phone</span>
        <p>{{ user.profile[0]?.nomor_hp }}</p>
      </div>
      <div class="flex items-center space-x-2">
        <span class="material-icons">calendar_today</span>
        <p>{{ user.profile[0]?.tempat_lahir }}, {{ formatDate(user.profile[0]?.tanggal_lahir) }}</p>
      </div>
      <div class="flex items-start space-x-2">
        <span class="material-icons">location_on</span>
        <p>{{ user.profile[0]?.alamat }}</p>
      </div>
    </div>

    <!-- Pendidikan -->
    <div v-if="user.educations && user.educations.length" class="mt-6">
      <h2 class="text-xl font-semibold mb-2">Pendidikan</h2>
      <ul class="list-disc list-inside space-y-1">
        <li v-for="edu in user.educations" :key="edu.id">
          {{ edu.tingkat_pendidikan }} - {{ edu.institusi }} ({{ edu.tahun_masuk }} - {{ edu.tahun_lulus }})
        </li>
      </ul>
    </div>

    <!-- Pengalaman -->
    <div v-if="user.positions && user.positions.length" class="mt-6">
      <h2 class="text-xl font-semibold mb-2">Pengalaman Kerja</h2>
      <ul class="list-disc list-inside space-y-1">
        <li v-for="pos in user.positions" :key="pos.id">
          {{ pos.nama_jabatan }} - {{ pos.unit_kerja }}
          ({{ formatDate(pos.mulai_jabatan) }} - {{ pos.akhir_jabatan ? formatDate(pos.akhir_jabatan) : 'Sekarang' }})
        </li>
      </ul>
    </div>

    <!-- Pelatihan -->
    <div v-if="user.training_histories && user.training_histories.length" class="mt-6">
      <h2 class="text-xl font-semibold mb-2">Pelatihan</h2>
      <ul class="list-disc list-inside space-y-1">
        <li v-for="pelatihan in user.training_histories" :key="pelatihan.id">
          {{ pelatihan.nama_pelatihan }} - {{ pelatihan.penyelenggara }}
          ({{ formatDate(pelatihan.tanggal_mulai) }} - {{ formatDate(pelatihan.tanggal_selesai) }})
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../stores/auth";
import html2pdf from "html2pdf.js";

const authStore = useAuthStore();
const user = ref(null);
const cvContent = ref(null);
const professionSummary = ref("");

const fetchUserCV = async () => {
  try {
    const res = await axios.get(`/api/cv/${authStore.user.id}`);
    user.value = res.data;
    await generateProfessionSummary(user.value);
  } catch (err) {
    console.error(err);
  }
};

function formatDate(date) {
  if (!date) return null;
  const d = new Date(date);
  return d.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

function getFotoUrl(profileArray) {
  if (!profileArray || profileArray.length === 0 || !profileArray[0].foto) {
    return "https://via.placeholder.com/150";
  }
  return `/storage/${profileArray[0].foto}`;
}

function downloadPDF() {
  const element = cvContent.value;
  const opt = {
    margin: 0.3,
    filename: `CV-${user.value.name}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
  };
  html2pdf().set(opt).from(element).save();
}

async function generateProfessionSummary(userData) {
  // console.log(userData);
  let experiences = '';
  experiences = userData.positions?.map(pos =>
    `Riwayat Kerja ${pos.nama_jabatan} di ${pos.unit_kerja} (${formatDate(pos.mulai_jabatan)} - ${pos.akhir_jabatan ? formatDate(pos.akhir_jabatan) : 'sekarang'})`
  ).join("; ") || "";
  experiences += userData.training_histories?.map(training =>
    `Riwayat Pelatihan ${training.nama_pelatihan} di ${training.penyelenggara} (${formatDate(training.tanggal_mulai)} - ${formatDate(training.tanggal_selesai)})`
  ).join("; ") || "";
  experiences += userData.educations?.map(edu =>
    `Riwayat Pendidikan ${edu.tingkat_pendidikan} di ${edu.institusi} (${edu.tahun_masuk} - ${edu.tahun_lulus})`
  ).join("; ") || "";
  const prompt = `Jawab dalam bahasa Indonesia sejelas dan sesingkat mungkin. Berdasarkan riwayat kerja, pelatihan dan pendidikan berikut:\n${experiences}\n\nSimpulkan satu frasa pendek yang menggambarkan diri saya tidak perlu tambahan alasan dan penjelasan detail lainnya.`;
  // log("Prompt:", prompt);
  console.log("Prompt:", prompt);
  try {
    const response = await fetch("http://127.0.0.1:1234/v1/chat/completions", {
  method: "POST",
  headers: {
    "Content-Type": "application/json"
  },
  body: JSON.stringify({
    model: "mistral-7b-instruct-v0.2",
    messages: [
      { role: "user", content: prompt }
    ],
    temperature: 0.7,
    max_tokens: 100
  })
});

    const result = await response.json();
    professionSummary.value = result.choices?.[0]?.message?.content?.trim() || "Professional Web Administrator";
  } catch (error) {
    console.error("Gagal menyimpulkan profesi:", error);
    professionSummary.value = "Professional Web Administrator"; // fallback
  }
}

onMounted(() => {
  fetchUserCV();
});
</script>

<style scoped>
.material-icons {
  font-size: 1.2rem;
}
</style>
