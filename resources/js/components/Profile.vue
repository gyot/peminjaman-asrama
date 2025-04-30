<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="bg-white shadow-md rounded-2xl p-6 relative">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Detail User</h2>

      <div v-if="isLoading" class="flex justify-center items-center py-10">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
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
        <h3 v-if="statusEdit === 'create'" class="text-xl font-semibold mb-4">Mohon Mengisi Data Profil</h3>
        <h3 v-else-if="statusEdit === 'edit'" class="text-xl font-semibold mb-4">Ubah Data Profil</h3>
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
          <button @click="closeModal" :disabled="statusEdit === 'create'" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
          <button @click="submitForm" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();

const profile = ref(null);
const form = reactive({
  tempat_lahir: "",
  tanggal_lahir: "",
  alamat: "",
  nomor_hp: "",
  foto: null,
  fotoFile: null,
});
const statusEdit = ref(null);
const showModal = ref(false);
const isLoading = ref(true);

const details = computed(() => [
  { label: "Tempat Lahir", value: profile.value?.tempat_lahir },
  { label: "Tanggal Lahir", value: dateFormat(profile.value?.tanggal_lahir) },
  { label: "Alamat", value: profile.value?.alamat },
  { label: "Nomor HP", value: profile.value?.nomor_hp },
  { label: "Foto", value: profile.value?.foto, isImage: true },
]);

const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`/api/profile/1`);
    profile.value = response.data;
    profile.value.foto = `../storage/${profile.value.foto}`;
  } catch (error) {
    statusEdit.value = "create";
    showModal.value = true;
    console.error("Gagal memuat profil:", error);
  } finally {
    isLoading.value = false;
  }
};

const openModal = () => {
  if (profile.value) {
    statusEdit.value = "edit";
    Object.assign(form, {
      tempat_lahir: profile.value.tempat_lahir,
      tanggal_lahir: profile.value.tanggal_lahir,
      alamat: profile.value.alamat,
      nomor_hp: profile.value.nomor_hp,
      foto: profile.value.foto,
      fotoFile: null,
    });
  } else {
    statusEdit.value = "create";
    Object.assign(form, {
      tempat_lahir: "",
      tanggal_lahir: "",
      alamat: "",
      nomor_hp: "",
      foto: null,
      fotoFile: null,
    });
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  form.foto = URL.createObjectURL(file);
  form.fotoFile = file;
};

const dateFormat = (dateString) => {
  return new Date(dateString).toLocaleDateString("id-ID", { year: "numeric", month: "long", day: "numeric" });
};

const submitForm = async () => {
  if (!form.tempat_lahir || !form.tanggal_lahir || !form.alamat || !form.nomor_hp) {
    return Swal.fire("Peringatan", "Semua field wajib diisi.", "warning");
  }

  let nomor_hp = form.nomor_hp.startsWith("0") ? "62" + form.nomor_hp.slice(1) : form.nomor_hp;

  const formData = new FormData();
  formData.append("user_id", authStore.user?.id);
  formData.append("tempat_lahir", form.tempat_lahir);
  formData.append("tanggal_lahir", form.tanggal_lahir);
  formData.append("alamat", form.alamat);
  formData.append("nomor_hp", nomor_hp);
  if (form.fotoFile) formData.append("foto", form.fotoFile);

  try {
    Swal.fire({
      title: "Loading...",
      text: "Sedang menyimpan data, harap tunggu.",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });
    console.log(statusEdit.value);
    
    if (statusEdit.value === "edit") {
      formData.append("_method", "PUT");
      const response = await axios.post(`/api/profile/profiles/${authStore.user?.id}`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      profile.value = response.data;
      profile.value.foto = `../storage/${profile.value.foto}`;
      Swal.fire("Berhasil", "Data berhasil diperbarui.", "success");
    } else if (statusEdit.value === "create") {
      const response = await axios.post("/api/profile", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      profile.value = response.data;
      profile.value.foto = `../storage/${profile.value.foto}`;
      Swal.fire("Berhasil", "Data berhasil ditambahkan.", "success");
    }
  } catch (error) {
    console.error("Gagal memproses data:", error);
    Swal.fire("Error", "Terjadi kesalahan saat memproses data.", "error");
  } finally {
    showModal.value = false;
    Swal.close();
  }
};

onMounted(() => {
  authStore.fetchUser();
  fetchData();
});
</script>

<style scoped>
/* Spinner animation sudah diatur pakai animate-spin di Tailwind */
/* Gaya untuk membuat Swal fullscreen */
.swal-fullscreen-popup {
  background-color: transparent !important;
  box-shadow: none !important;
  color: white !important;
  text-align: center !important;
  width: 100vw !important;
  height: 100vh !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 !important;
  padding: 0 !important;
}

.swal-fullscreen-popup .swal2-title {
  font-size: 2rem !important;
  color: white !important;
}

.swal-fullscreen-popup .swal2-content {
  font-size: 1.2rem !important;
  color: white !important;
}

.swal-fullscreen-popup .swal2-spinner {
  border-color: white !important;
  border-top-color: transparent !important;
}
</style>
