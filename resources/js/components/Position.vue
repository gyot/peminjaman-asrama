<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Riwayat Jabatan</h2>

    <button @click="openModal()" class="btn-primary mb-4">+ Tambah Jabatan</button>

    <ul class="space-y-2">
      <li v-for="edu in positions" :key="edu.id" class="border p-3 rounded">
        <div class="font-semibold">{{ edu.nama_jabatan }} - {{ edu.unit_kerja }}</div>
        <div class="text-sm">{{ formatDate(edu.mulai_jabatan) }} - {{ formatDate(edu.akhir_jabatan) || 'Sekarang' }}</div>
        <a v-if="edu.sk" :href="`/storage/${edu.sk}`" target="_blank" class="text-blue-600 underline">Lihat SK</a>
        <div class="mt-2">
          <button @click="editPosition(edu)" class="btn-secondary">Edit</button>
          <button @click="deletePosition(edu.id)" class="btn-danger">Hapus</button>
        </div>
      </li>
    </ul>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Edit Jabatan' : 'Tambah Jabatan' }}</h3>
        <form @submit.prevent="savePosition" enctype="multipart/form-data" class="space-y-2">
          <label for="">Nama Jabatan:</label>
          <input v-model="form.nama_jabatan" placeholder="Nama Jabatan" class="input" required />

          <label for="">Unit Kerja:</label>
          <input v-model="form.unit_kerja" placeholder="Unit Kerja" class="input" required />

          <label for="">Mulai Jabatan:</label>
          <input type="date" v-model="form.mulai_jabatan" class="input" required />

          <!-- <label for="">Akhir Jabatan:</label>
          <input type="date" v-model="form.akhir_jabatan" class="input" required /> -->

          <label for="">Upload SK (PDF/JPG/PNG):</label>
          <input type="file" @change="handleFileUpload" accept=".pdf,.jpg,.jpeg,.png" class="input" />

          <div class="flex justify-between">
            <button type="submit" class="btn-primary" :disabled="isLoading">
              {{ isLoading ? 'Loading...' : (form.id ? 'Update' : 'Tambah') }} Jabatan
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
import { useRouter } from "vue-router"
import { useAuthStore } from "../stores/auth"

const router = useRouter()
const authStore = useAuthStore()

const positions = ref([])
const showModal = ref(false)
const selectedFile = ref(null)
const isLoading = ref(false)

const form = ref({
  id: null,
  nama_jabatan: '',
  unit_kerja: '',
  mulai_jabatan: '',
  akhir_jabatan: '',
})

const handleFileUpload = (e) => {
  selectedFile.value = e.target.files[0]
}

const openModal = (position = null) => {
  if (position) {
    form.value = {
      id: position.id,
      nama_jabatan: position.nama_jabatan,
      unit_kerja: position.unit_kerja,
      mulai_jabatan: position.mulai_jabatan,
      akhir_jabatan: position.akhir_jabatan,
    }
  } else {
    resetForm()
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const fetchPositions = async () => {
  try {
    const res = await axios.get('/api/positions')
    positions.value = res.data
  } catch (err) {
    console.error(err)
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    nama_jabatan: '',
    unit_kerja: '',
    mulai_jabatan: '',
    akhir_jabatan: '',
  }
  selectedFile.value = null
}

const savePosition = async () => {
  isLoading.value = true
  try {
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
      formData.append('sk', selectedFile.value)
    }

    if (form.value.id) {
      formData.append('_method', 'PUT')
      await axios.post(`/api/positions/${form.value.id}`, formData)
      Swal.fire('Berhasil', 'Data berhasil diperbarui', 'success')
    } else {
      await axios.post('/api/positions', formData)
      Swal.fire('Berhasil', 'Data berhasil ditambahkan', 'success')
    }

    closeModal()
    fetchPositions()
  } catch (err) {
    console.error(err)
    Swal.fire('Gagal', 'Gagal menyimpan data', 'error')
  } finally {
    isLoading.value = false
    Swal.close()
  }
}

const editPosition = (position) => {
  openModal(position)
}

const deletePosition = async (id) => {
  const confirm = await Swal.fire({
    title: 'Yakin ingin hapus?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus'
  })

  if (confirm.isConfirmed) {
    Swal.fire({
      title: 'Loading...',
      text: 'Sedang menghapus data...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      },
    })

    try {
      await axios.delete(`/api/positions/${id}`)
      await fetchPositions()
      Swal.fire('Berhasil', 'Data berhasil dihapus', 'success')
    } catch (err) {
      console.error(err)
      Swal.fire('Gagal', 'Gagal menghapus data', 'error')
    } finally {
      Swal.close()
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return null
  return new Date(dateString).toLocaleDateString("id-ID", { year: "numeric", month: "2-digit", day: "2-digit" })
}

onMounted(() => {
  fetchPositions()
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
