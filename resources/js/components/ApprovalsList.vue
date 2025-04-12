<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Daftar Approvals</h1>

    <div v-if="loading" class="text-gray-500">Loading data...</div>
    <div v-else>
      <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
          <tr>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Dikonfirmasi Oleh</th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Application</th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Notes</th>
            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="approval in approvals" :key="approval.id" class="border-b hover:bg-gray-50">
            <td class="py-3 px-4">{{ approval.id }}</td>
            <td class="py-3 px-4">{{ approval.user?.name || 'Tidak ditemukan' }}</td>
            <td class="py-3 px-4">{{ approval.applications?.name || 'Tidak ditemukan' }}</td>
            <td class="py-3 px-4">
              <span :class="statusClass(approval.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                {{ approval.status }}
              </span>
            </td>
            <td class="py-3 px-4">{{ approval.notes || '-' }}</td>
            <td class="py-3 px-4">{{ formatDate(approval.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

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

onMounted(fetchApprovals)
</script>

<style scoped>
/* Opsional: tambahkan style tambahan jika perlu */
</style>
