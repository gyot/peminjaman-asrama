<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <!-- <h1 class="text-2xl font-bold mb-4">Selamat Datang di Admin Panel</h1> -->
    <ApplicationForm />
  </div>
</template>

<script>
import ApplicationForm from '../components/ApplicationForm.vue';

export default {
  components: {
    ApplicationForm,
  },
};
</script>