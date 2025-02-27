<!-- <template>
    <Sidebar />
  </template>
  
  <script>
  import Sidebar from './components/Sidebar.vue';
  
  export default {
    components: {
      Sidebar,
    },
  };
  </script> -->

  <template>
    <div class="flex h-screen">
      <!-- Sidebar -->
      <Sidebar :isOpen="isMenuOpen" @close="isMenuOpen = false" />
  
      <!-- Content -->
      <div class="flex-1 flex flex-col">
        <Header @toggleMenu="toggleMenu" />
  
        <!-- Main Content -->
        <main class="p-6">
          <MainContent />
        </main>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import Sidebar from "./components/Sidebar.vue";
  import Header from "./components/Header.vue";
  import MainContent from "./components/MainContent.vue";
  
  const isMenuOpen = ref(false);
  
  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  };
  </script>
  