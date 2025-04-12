<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 text-center px-4 py-12">
        <!-- Ikon -->
        <motion.div
          class="text-6xl text-gray-500 mb-6"
          :initial="{ opacity: 0 }"
          :animate="{ opacity: 1 }"
          transition="{ delay: 0.5, duration: 0.5 }"
        >
          <i class="fas fa-exclamation-triangle"></i>
        </motion.div>
      <!-- Animasi 404 -->
      <motion.div
        class="text-9xl font-extrabold text-indigo-600 mb-4"
        :initial="{ opacity: 0, scale: 0.5 }"
        :animate="{ opacity: 1, scale: 1 }"
        transition="{ duration: 0.8, ease: 'easeOut' }"
      >
        404
      </motion.div>
  
      <!-- Deskripsi -->
      <motion.div
        class="text-3xl font-semibold text-gray-800 mb-4"
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        transition="{ delay: 0.3, duration: 0.6 }"
      >
        Oops! Halaman tidak ditemukan.
      </motion.div>
  
  
      <!-- Pesan tambahan -->
      <motion.div
        class="text-lg text-gray-600 mb-6"
        :initial="{ opacity: 0 }"
        :animate="{ opacity: 1 }"
        transition="{ delay: 0.6, duration: 0.5 }"
      >
        Sepertinya halaman yang kamu cari tidak ada atau sudah dipindahkan.
      </motion.div>
  
      <!-- Tombol Aksi -->
      <motion.div
        class="flex space-x-4"
        :initial="{ opacity: 0 }"
        :animate="{ opacity: 1 }"
        transition="{ delay: 0.7, duration: 0.5 }"
      >
        
      </motion.div>
    </div>
  </template>
  
  <script setup>
  import { MotionDiv } from '@motionone/vue'
  
  function reloadPage() {
    window.location.reload()
  }
  </script>
  
  <style scoped>
  /* Optional: tambahan animasi atau style lainnya */
  </style>
  