<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Daftar Fasilitas</h1>
    <button @click="openModal('add')" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah</button>

    <!-- Tabel Responsif -->
    <div class="overflow-x-auto mt-4">
      <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-100">
            <th class="border px-4 py-2">Gambar</th>
            <th class="border px-4 py-2">Nama Fasilitas</th>
            <th class="border px-4 py-2">Kapasitas</th>
            <th class="border px-4 py-2">Harga</th>
            <th class="border px-4 py-2">Unit</th>
            <th class="border px-4 py-2">Deskripsi</th>
            <th class="border px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="facility in facilities" :key="facility.id">
            <td class="border px-4 py-2 whitespace-nowrap">
              <img 
                :src="'/storage/' + facility.image" 
                alt="Gambar fasilitas" 
                class="w-16 h-16 object-cover" 
                @error="event => event.target.src='/storage/images/logo_kemdikbud.png'"
              />
            </td>
            <td class="border px-4 py-2">{{ facility.name }}</td>
            <td class="border px-4 py-2">{{ facility.capacity }}</td>
            <td class="border px-4 py-2">{{ formatCurrency(facility.price) }}</td>
            <td class="border px-4 py-2">{{ facility.unit }}</td>
            <td class="border px-4 py-2">{{ facility.description }}</td>
            <td class="border px-4 py-2">
              <button @click="openModal('edit', facility)" class="bg-yellow-500 text-white px-2 py-1 rounded mr-2">Edit</button>
              <button @click="deleteFacility(facility.id)" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">{{ modalTitle }}</h3>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Fasilitas</label>
            <input v-model="form.name" type="text" class="w-full border rounded p-2" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kapasitas</label>
            <input v-model="form.capacity" type="number" class="w-full border rounded p-2" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Harga</label>
            <input v-model="form.price" type="number" class="w-full border rounded p-2" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Unit</label>
            <input v-model="form.unit" type="text" class="w-full border rounded p-2" required />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea v-model="form.description" class="w-full border rounded p-2" required></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Gambar</label>
            <input type="file" @change="handleFileUpload" class="w-full border rounded p-2" />
          </div>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Batal</button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const facilities = ref([]);
const isOpen = ref(false);
const modalTitle = ref("Tambah Fasilitas");
const editMode = ref(false);
const editId = ref(null);

const form = ref({
  user_id: null,
  name: "",
  capacity: "",
  unit: "",
  price: "",
  image: null,
  description: "",
});

const fetchDataProfil = async () => {
  try {
    const response = await axios.get(`/api/profile/${authStore.user?.id}`);
    console.log(response.data); // Lakukan sesuatu dengan data profil
  } catch (error) {
    router.push("/user/profile");
    console.error("Gagal memuat profil:", error);
  }
};

const fetchFacilities = async () => {
  try {
    const response = await axios.get("/api/facilities");
    facilities.value = response.data;
  } catch (error) {
    console.error("Error fetching facilities:", error);
    Swal.fire("Error", "Gagal mengambil data fasilitas", "error");
  }
};

const openModal = (mode, facility = null) => {
  isOpen.value = true;
  editMode.value = mode === "edit";
  modalTitle.value = editMode.value ? "Edit Fasilitas" : "Tambah Fasilitas";
  if (editMode.value && facility) {
    editId.value = facility.id;
    form.value = { ...facility, image: null };
  } else {
    resetForm();
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
  }).format(value);
};

const closeModal = () => {
  isOpen.value = false;
  resetForm();
};

const handleFileUpload = (event) => {
  form.value.image = event.target.files[0];
};

const submitForm = async () => {
  if (!authStore.user) {
    Swal.fire("Error", "User belum terautentikasi. Harap login terlebih dahulu.", "error");
    return;
  }

  const formData = new FormData();
  formData.append("user_id", authStore.user.id);
  formData.append("name", form.value.name);
  formData.append("capacity", form.value.capacity);
  formData.append("unit", form.value.unit);
  formData.append("price", form.value.price);
  formData.append("description", form.value.description);
  if (form.value.image) {
    formData.append("image", form.value.image);
  }

  try {
    Swal.fire({
      title: "Loading...",
      text: "Sedang menyimpan data, harap tunggu.",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    if (editMode.value) {
      formData.append("_method", "PUT");
      await axios.post(`/api/facilities/${editId.value}`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      Swal.fire("Berhasil", "Data fasilitas berhasil diperbarui", "success");
    } else {
      await axios.post("/api/setFacilities", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      Swal.fire("Berhasil", "Data fasilitas berhasil ditambahkan", "success");
    }

    fetchFacilities();
    closeModal();
  } catch (error) {
    console.error("Error submitting form:", error);
    Swal.fire("Error", "Terjadi kesalahan saat menyimpan data", "error");
  } finally {
    Swal.close();
  }
};

const deleteFacility = async (id) => {
  const confirm = await Swal.fire({
    title: "Yakin ingin hapus?",
    text: "Data fasilitas akan dihapus secara permanen.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, hapus",
    cancelButtonText: "Batal",
  });

  if (confirm.isConfirmed) {
    try {
      Swal.fire({
        title: "Loading...",
        text: "Sedang menghapus data, harap tunggu.",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      await axios.delete(`/api/facilities/${id}`);
      fetchFacilities();
      Swal.fire("Berhasil", "Data fasilitas berhasil dihapus", "success");
    } catch (error) {
      console.error("Error deleting facility:", error);
      Swal.fire("Error", "Terjadi kesalahan saat menghapus data", "error");
    } finally {
      Swal.close();
    }
  }
};

const resetForm = () => {
  form.value = {
    user_id: null,
    name: "",
    capacity: "",
    unit: "",
    price: "",
    image: null,
    description: "",
  };
  editMode.value = false;
  editId.value = null;
};

onMounted(()=>{
    fetchFacilities()
  fetchDataProfil()
  })

onBeforeUnmount(() => {
  // Bersihkan polling jika ada
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.9s ease;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  table {
    font-size: 0.875rem;
  }

  th,
  td {
    padding: 0.5rem;
  }

  img {
    width: 40px;
    height: 40px;
  }
}
</style>