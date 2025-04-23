<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Data Pendidikan</h2>

    <form @submit.prevent="saveEducation" class="space-y-2">
      <input v-model="form.tingkat_pendidikan" placeholder="Tingkat Pendidikan" class="input" required />
      <input v-model="form.institusi" placeholder="Institusi" class="input" required />
      <input v-model="form.jurusan" placeholder="Jurusan" class="input" />
      <input v-model="form.tahun_masuk" type="date" placeholder="Tahun Masuk" class="input" required />
      <input v-model="form.tahun_lulus" type="date" placeholder="Tahun Lulus" class="input" />
      <input type="file" @change="handleFileUpload" class="input" />

      <button type="submit" class="btn-primary">
        {{ form.id ? 'Update' : 'Tambah' }} Pendidikan
      </button>
    </form>

    <ul class="mt-4 space-y-2">
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const educations = ref([])
const form = ref({
  id: null,
  tingkat_pendidikan: '',
  institusi: '',
  jurusan: '',
  tahun_masuk: '',
  tahun_lulus: '',
})
const selectedFile = ref(null)

const handleFileUpload = (e) => {
  selectedFile.value = e.target.files[0]
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
    const formData = new FormData()
    for (const key in form.value) {
      formData.append(key, form.value[key])
    }
    if (selectedFile.value) {
      formData.append('ijazah', selectedFile.value)
    }

    if (form.value.id) {
      formData.append('_method', 'PUT')
      await axios.post(`/api/educations/${form.value.id}`, formData)
      Swal.fire('Berhasil', 'Data berhasil diperbarui', 'success')
    } else {
      await axios.post('/api/educations', formData)
      Swal.fire('Berhasil', 'Data berhasil ditambahkan', 'success')
    }

    resetForm()
    fetchEducations()
  } catch (err) {
    console.error(err)
    Swal.fire('Gagal', 'Gagal menyimpan data', 'error')
  }
}

const editEducation = (edu) => {
  form.value = { ...edu }
}

const deleteEducation = async (id) => {
  const confirm = await Swal.fire({
    title: 'Yakin ingin hapus?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus'
  })
  if (confirm.isConfirmed) {
    await axios.delete(`/api/educations/${id}`)
    fetchEducations()
  }
}

onMounted(fetchEducations)
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
