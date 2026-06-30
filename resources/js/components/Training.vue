<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Riwayat Pelatihan</h2>

    <button @click="openModal()" class="btn-primary mb-4">+ Tambah Pelatihan</button>

    <ul class="space-y-2">
      <li v-for="training in trainings" :key="training.id" class="border p-3 rounded">
        <div class="font-semibold">{{ training.nama_pelatihan }} ({{ training.tingkat || '-' }})</div>
        <div class="text-sm mb-1">
          {{ formatDate(training.tanggal_mulai) }} - {{ formatDate(training.tanggal_selesai) || 'Sekarang' }}
        </div>
        <div class="mb-1">
          Penyelenggara: {{ training.penyelenggara || '-' }} | Lokasi: {{ training.lokasi || '-' }}
        </div>
        <div class="mb-1">
          Durasi: {{ training.durasi || '-' }} | Jenis: {{ training.jenis_pelatihan || '-' }}
        </div>
        <div v-if="training.materi" class="mb-1 text-sm italic">
          Materi: {{ training.materi }}
        </div>
        <a v-if="training.sertifikat" :href="`/storage/${training.sertifikat}`" target="_blank" class="text-blue-600 underline">
          Lihat Sertifikat
        </a>
        <div class="mt-2">
          <button @click="editTraining(training)" class="btn-secondary">Edit</button>
          <button @click="deleteTraining(training.id)" class="btn-danger">Hapus</button>
        </div>
      </li>
    </ul>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-auto">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Edit Pelatihan' : 'Tambah Pelatihan' }}</h3>
        <form @submit.prevent="saveTraining" enctype="multipart/form-data" class="space-y-3">
          <label>Nama Pelatihan:</label>
          <input v-model="form.nama_pelatihan" placeholder="Nama Pelatihan" class="input" required />

          <label>Durasi:</label>
          <input v-model="form.durasi" placeholder="Misal: 3 hari, 1 minggu" class="input" />

          <label>Materi:</label>
          <textarea v-model="form.materi" placeholder="Ringkasan materi pelatihan" class="input" rows="3"></textarea>

          <label>Penyelenggara:</label>
          <input v-model="form.penyelenggara" placeholder="Lembaga penyelenggara" class="input" />

          <label>Tanggal Mulai:</label>
          <input type="date" v-model="form.tanggal_mulai" class="input" />

          <label>Tanggal Selesai:</label>
          <input type="date" v-model="form.tanggal_selesai" class="input" />

          <label>Lokasi:</label>
          <input v-model="form.lokasi" placeholder="Lokasi pelatihan" class="input" />

          <label>Tingkat Pelatihan:</label>
          <input v-model="form.tingkat" placeholder="Internal, Nasional, dll." class="input" />

          <label>Jenis Pelatihan:</label>
          <input v-model="form.jenis_pelatihan" placeholder="Teknis, Fungsional, dll." class="input" />

          <label>Upload Sertifikat (PDF/JPG/PNG):</label>
          <input type="file" @change="handleFileUpload" accept=".pdf,.jpg,.jpeg,.png" class="input" />

          <label>Catatan:</label>
          <textarea v-model="form.catatan" placeholder="Catatan tambahan" class="input" rows="2"></textarea>

          <div class="flex justify-between">
            <button type="submit" class="btn-primary" :disabled="isLoading">
              {{ isLoading ? 'Loading...' : (form.id ? 'Update' : 'Tambah') }} Pelatihan
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

const trainings = ref([])
const showModal = ref(false)
const selectedFile = ref(null)
const isLoading = ref(false)

const form = ref({
  id: null,
  nama_pelatihan: '',
  durasi: '',
  materi: '',
  penyelenggara: '',
  tanggal_mulai: '',
  tanggal_selesai: '',
  lokasi: '',
  tingkat: '',
  jenis_pelatihan: '',
  catatan: '',
})

const handleFileUpload = (e) => {
  selectedFile.value = e.target.files[0]
}

const openModal = (training = null) => {
  if (training) {
    form.value = { ...training }
  } else {
    resetForm()
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const fetchTrainings = async () => {
  try {
    const res = await axios.get('/api/training-histories')
    trainings.value = res.data
  } catch (err) {
    console.error(err)
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    nama_pelatihan: '',
    durasi: '',
    materi: '',
    penyelenggara: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    lokasi: '',
    tingkat: '',
    jenis_pelatihan: '',
    catatan: '',
  }
  selectedFile.value = null
}

const saveTraining = async () => {
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
      formData.append('sertifikat', selectedFile.value)
    }

    if (form.value.id) {
      formData.append('_method', 'PUT')
      await axios.post(`/api/training-histories/${form.value.id}`, formData)
      Swal.fire('Berhasil', 'Data berhasil diperbarui', 'success')
    } else {
      await axios.post('/api/training-histories', formData)
      Swal.fire('Berhasil', 'Data berhasil ditambahkan', 'success')
    }

    closeModal()
    fetchTrainings()
  } catch (err) {
    console.error(err)
    Swal.fire('Gagal', 'Gagal menyimpan data', 'error')
  } finally {
    isLoading.value = false
    Swal.close()
  }
}

const editTraining = (training) => {
  openModal(training)
}

const deleteTraining = async (id) => {
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
      await axios.delete(`/api/training-histories/${id}`)
      await fetchTrainings()
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
  fetchTrainings()
})
</script>

<style scoped>
.input {
  display: block;
  width: 100%;
  border: 1px solid #ccc;
  padding: 0.4rem 0.6rem;
  border-radius: 0.3rem;
  margin-bottom: 0.5rem;
}
.btn-primary {
  background-color: #2563eb;
  color: white;
  padding: 0.4rem 1rem;
  border-radius: 0.3rem;
  border: none;
  cursor: pointer;
}
.btn-primary:disabled {
  background-color: #93c5fd;
  cursor: not-allowed;
}
.btn-secondary {
  background-color: #6b7280;
  color: white;
  padding: 0.3rem 0.8rem;
  border-radius: 0.3rem;
  margin-right: 0.5rem;
  border: none;
  cursor: pointer;
}
.btn-danger {
  background-color: #dc2626;
  color: white;
  padding: 0.3rem 0.8rem;
  border-radius: 0.3rem;
  border: none;
  cursor: pointer;
}
</style>
