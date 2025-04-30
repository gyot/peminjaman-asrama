<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Riwayat Jabatan</h2>

    <button @click="openModal()" class="btn-primary mb-4">+ Tambah Jabatan</button>

    <ul class="space-y-2">
      <li v-for="positions in positions" :key="positions.id" class="border p-3 rounded">
        <div class="font-semibold">{{ positions.tingkat_pendidikan }} - {{ positions.institusi }}</div>
        <div class="text-sm">{{ positions.jurusan }} ({{ positions.tahun_masuk }} - {{ positions.tahun_lulus ?? 'Sekarang' }})</div>
        <a v-if="positions.sk" :href="`/storage/${positions.sk}`" target="_blank" class="text-blue-600 underline">Lihat SK</a>
        <div class="mt-2">
          <button @click="editEducation(positions)" class="btn-secondary">Edit</button>
          <button @click="deleteEducation(positions.id)" class="btn-danger">Hapus</button>
        </div>
      </li>
    </ul>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">{{ form.id ? 'Edit Pendidikan' : 'Tambah Pendidikan' }}</h3>
        <form @submit.prevent="saveEducation" class="space-y-2">
          <label for="">Nama Jabatan:</label>
          <input v-model="form.nama_jabatan" placeholder="Tingkat Pendidikan" class="input" required />
          <label for="">Unit Kerja:</label>
          <input v-model="form.unit_kerja" placeholder="Institusi" class="input" required />
          <label for="">Tanggal Mulai Jabatan:</label>
          <input v-model="form.mulai_jabatan" type="date" placeholder="Jurusan" class="input" />
          <label for="">Tanggal Selesai Jabatan (Kosongkan jika masih aktif):</label>
          <input v-model="form.akhir_jabatan" type="date" placeholder="Tahun Lulus" class="input" />
          
          <label for="">SK:</label>
          <input type="file" @change="handleFileUpload" class="input" />

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

const positions = ref([])
const showModal = ref(false)
const form = ref({
  id: null,
  nama_jabatan:'',
  unit_kerja:'',
  mulai_jabatan:'',
  akhir_jabatan:'',
})
const selectedFile = ref(null)

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

const openModal = (positions = null) => {
  if (positions) {
    form.value = { ...positions }
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

const fetchPositions = async () => {
  const res = await axios.get('/api/positions')
  positions.value = res.data
  
}

const resetForm = () => {
  form.value = {
    id: null,
    nama_jabatan:'',
    unit_kerja:'',
    mulai_jabatan:'',
    akhir_jabatan:'',
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
      formData.append('sk', selectedFile.value)
    }
    let response;
    if (form.value.id) {
      formData.append('_method', 'PUT')
      // await axios.post(`/api/positions/${form.value.id}`, formData)
      response = await axios.post(`/api/positions/${form.value.id}`, formData)
      console.log(response.data);
      
      Swal.fire('Berhasil', 'Data berhasil dirubah', 'success')
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
    // Tutup loading Swal
    Swal.close()
  }
}

const editEducation = (positions) => {
  openModal(positions)
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
    await axios.delete(`/api/positions/${id}`)
    fetchPositions()
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
