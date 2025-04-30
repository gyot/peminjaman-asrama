<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Riwayat Pendidikan</h2>

    <button @click="openModal()" class="btn-primary mb-4">+ Tambah Pendidikan</button>

    <ul class="space-y-2">
      <li v-for="edu in educations" :key="edu.id" class="border p-3 rounded">
        <div class="font-semibold">{{ edu.tingkat_pendidikan }} - {{ edu.institusi }}</div>
        <div class="text-sm">{{ edu.jurusan }} ({{ edu.tahun_masuk }} - {{ edu.tahun_lulus ?? 'Sekarang' }})</div>
        <a v-if="edu.ijazah" :href="`/storage/${edu.ijazah}`" target="_blank" class="text-blue-600 underline">Lihat Ijazah</a>
        <div class="mt-2">
          <button @click="editEducation(edu)" class="btn-secondary">Edit</button>
          <button @click="deleteEducation(edu.id)" class="btn-danger">Hapus</button>
        </div>
      </li>
    </ul>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Edit Pendidikan' : 'Tambah Pendidikan' }}</h3>
        <form @submit.prevent="saveEducation" class="space-y-2">
          <label for="">Tingkat Pendidikan:</label>
          <input v-model="form.tingkat_pendidikan" placeholder="Tingkat Pendidikan" class="input" required />
          Institusi:
          <input v-model="form.institusi" placeholder="Institusi" class="input" required />
          Jurusan:
          <input v-model="form.jurusan" placeholder="Jurusan" class="input" />
          Pilih Tahun Masuk:
          <select v-model="form.tahun_masuk" class="input" required>
            <option disabled value="">Pilih Tahun Masuk</option>
            <option v-for="tahun in tahunList" :key="'masuk-' + tahun" :value="tahun">{{ tahun }}</option>
          </select>
          Pilih Tahun Lulus:
          <select v-model="form.tahun_lulus" class="input">
            <option disabled value="">Pilih Tahun Lulus</option>
            <option v-for="tahun in tahunList" :key="'lulus-' + tahun" :value="tahun">{{ tahun }}</option>
          </select>
          <label for="">Ijazah:</label>
          <input type="file" @change="handleFileUpload" class="input" />

          <div class="flex justify-between">
            <button type="submit" class="btn-primary" :disabled="isLoading">
              {{ isLoading ? 'Loading...' : (form.id ? 'Update' : 'Tambah') }} Pendidikan
            </button>
            <button @click.prevent="closeModal" type="button" class="btn-danger" :disabled="isLoading">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const educations = ref([])
const showModal = ref(false)
const form = ref({
  id: null,
  tingkat_pendidikan: '',
  institusi: '',
  jurusan: '',
  tahun_masuk: '',
  tahun_lulus: '',
})
const selectedFile = ref(null)
const currentYear = new Date().getFullYear()
const startYear = currentYear - 60
const tahunList = Array.from({ length: 61 }, (_, i) => startYear + i)

const fetchDataProfil = async () => {
  try {
    const response = await axios.get(`/api/profile/${authStore.user?.id}`);
    console.log(response.data); // Lakukan sesuatu dengan data profil
  } catch (error) {
    router.push("/user/profile");
    console.error("Gagal memuat profil:", error);
  }
};

const handleFileUpload = (e) => {
  selectedFile.value = e.target.files[0]
}

const openModal = (education = null) => {
  if (education) {
    form.value = { ...education }
  } else {
    resetForm()
  }
  showModal.value = true
  console.log(form.value.id);
  
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const fetchEducations = async () => {
  const res = await axios.get('/api/educations')
  educations.value = res.data
  
}

const resetForm = () => {
  form.value = {
    id: null,
    tingkat_pendidikan: '',
    institusi: '',
    jurusan: '',
    tahun_masuk: '',
    tahun_lulus: '',
  }
  selectedFile.value = null
}

const saveEducation = async () => {
  
  try {
    // Tampilkan loading Swal
    Swal.fire({
      title: 'Loading...',
      text: 'Sedang menyimpan data, harap tunggu.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      },
    })

    const formData = new FormData()
    for (const key in form.value) {
      formData.append(key, form.value[key])
    }
    if (selectedFile.value) {
      formData.append('ijazah', selectedFile.value)
    }
    let response;
    if (form.value.id) {
      formData.append('_method', 'PUT')
      // await axios.post(`/api/educations/${form.value.id}`, formData)
      response = await axios.post(`/api/educations/${form.value.id}`, formData)
      console.log(response.data);
      
      Swal.fire('Berhasil', 'Data berhasil dirubah', 'success')
    } else {
      await axios.post('/api/educations', formData)
      Swal.fire('Berhasil', 'Data berhasil ditambahkan', 'success')
    }

    closeModal()
    fetchEducations()
  } catch (err) {
    console.error(err)
    Swal.fire('Gagal', 'Gagal menyimpan data', 'error')
  } finally {
    // Tutup loading Swal
    Swal.close()
  }
}

const editEducation = (edu) => {
  openModal(edu)
}

const deleteEducation = async (id) => {
  const confirm = await Swal.fire({
    title: 'Yakin ingin hapus?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus'
  })
  if (confirm.isConfirmed) {
    Swal.fire({
      title: 'Loading...',
      text: 'Sedang menyimpan data, harap tunggu.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      },
    })
    await axios.delete(`/api/educations/${id}`)
    fetchEducations()
    Swal.close()
    Swal.fire('Berhasil', 'Data berhasil dihapus', 'success')
  }
}

onMounted(()=>{
  fetchEducations()
  fetchDataProfil()
  })
</script>

<style scoped>
.input {
  display: block;
  width: 100%;
  padding: 0.5em;
  border: 1px solid #ccc;
  border-radius: 0.5em;
}

.btn-primary {
  background-color: #2563eb;
  color: white;
  padding: 0.5em 1em;
  border-radius: 0.5em;
}

.btn-secondary {
  background-color: #eab308;
  padding: 0.3em 0.8em;
  border-radius: 0.5em;
  color: white;
}

.btn-danger {
  background-color: #dc2626;
  padding: 0.3em 0.8em;
  border-radius: 0.5em;
  color: white;
}
</style>
