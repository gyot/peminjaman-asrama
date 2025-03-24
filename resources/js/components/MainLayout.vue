<template>
    <div class="flex h-screen">
      <!-- Sidebar -->
      <Sidebar :isOpen="isMenuOpen" @close="isMenuOpen = false" />
  
      <!-- Content -->
      <div class="flex-1 flex flex-col">
        <Header @toggleMenu="toggleMenu" />
  
        <!-- Main Content -->
        <main class="p-6">
          <router-view />
        </main>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import Sidebar from "../components/Sidebar.vue";
  import Header from "../components/Header.vue";
  import { onMounted } from 'vue';
  import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();

  onMounted(() => {
    authStore.fetchUser();
  });
  const isMenuOpen = ref(false);
  
  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  };
  </script>
  