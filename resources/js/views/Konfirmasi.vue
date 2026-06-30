<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 text-center px-4 py-12">
    <!-- Ikon Sukses -->
    <motion.div class="text-6xl text-green-500 mb-6" :initial="{ opacity: 0 }" :animate="{ opacity: 1 }"
      transition="{ delay: 0.5, duration: 0.5 }">
      <i class="fas fa-check-circle"></i>
    </motion.div>

    <!-- Judul -->
    <motion.div class="text-3xl font-semibold text-gray-800 mb-4" :initial="{ opacity: 0, y: 20 }"
      :animate="{ opacity: 1, y: 0 }" transition="{ delay: 0.3, duration: 0.6 }">
      Data Berhasil Dikirim!
    </motion.div>

    <!-- Pesan tambahan -->
    <motion.div class="text-lg text-gray-600 mb-6" :initial="{ opacity: 0 }" :animate="{ opacity: 1 }"
      transition="{ delay: 0.6, duration: 0.5 }">
      Terima kasih telah mengirimkan data. Kami akan memprosesnya segera.
    </motion.div>

    <!-- Tombol Aksi -->
    <motion.div class="flex space-x-4" :initial="{ opacity: 0 }" :animate="{ opacity: 1 }"
      transition="{ delay: 0.7, duration: 0.5 }">
      <router-link to="/dashboard"
        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow">
        Mau mengisi form lagi?
      </router-link>
    </motion.div>
  </div>
</template>

<script setup>
import { MotionDiv } from '@motionone/vue'

function goBack() {
  this.$router.push('/dashboard')
}
</script>

<style scoped>
/* Optional: tambahan animasi atau style lainnya */
</style>
