<template>
    <div>
      <h2 class="text-xl font-bold mb-4">Riwayat Jabatan</h2>
      <div v-if="authStore" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Detail Data -->
        <div class="flex flex-col" v-for="(value, label) in details" :key="label">
          <span class="text-sm text-gray-500">{{ label }}:</span>
          <span class="text-base text-gray-800">{{ value }}</span>
        </div>
      </div>

      <div v-else class="text-center text-gray-500 py-8">Memuat data...</div>
    </div>
  </template>
  
  <script setup>
  // Tambahkan script jika perlu
  </script>
  
  <style scoped>
  </style>
  