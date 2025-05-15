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
    <router-view />
  </template>
  
  
  <!-- <script setup>
  import { ref } from "vue";
  import Sidebar from "./components/Sidebar.vue";
  import Header from "./components/Header.vue";
  import MainContent from "./components/MainContent.vue";
  
  const isMenuOpen = ref(false);
  
  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  };
  </script> -->
  <!-- File: src/App.vue -->

<script setup>
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();
authStore.fetchUser(); // Panggil sekali saat App.vue dimuat
</script>

  