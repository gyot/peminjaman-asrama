<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    
    <ApplicationForm />
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import ApplicationForm from '../components/ApplicationForm.vue';

// const authStore = useAuthStore();
</script>
