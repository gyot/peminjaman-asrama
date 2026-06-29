// import './bootstrap';
// import { createApp } from 'vue';
// import App from './App.vue'; // Komponen root
// import router from './router'; // Import router

// // Buat aplikasi Vue
// const app = createApp(App);

// // Gunakan router
// app.use(router);

// // Mount aplikasi
// app.mount('#app');
// import { createApp } from 'vue';
// import App from './App.vue';
// import router from './router';
// import globalFunctions from './globalFunctions';
// import { createPinia } from 'pinia';
// import { useAuthStore } from './stores/auth'
// // di main.js atau main.ts
// import '@fortawesome/fontawesome-free/css/all.css'
// import '@fortawesome/fontawesome-free/js/all.js'

// createApp(App).use(globalFunctions).use(createPinia()).use(router).mount('#app');
// const auth = useAuthStore()
// auth.initAuth()

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import globalFunctions from './globalFunctions';
import { createPinia } from 'pinia';
import { useAuthStore } from './stores/auth';
import '@fortawesome/fontawesome-free/css/all.css';
import '@fortawesome/fontawesome-free/js/all.js';
import axios from 'axios';

const token = localStorage.getItem("token");
if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// Inisialisasi
const app = createApp(App);
const pinia = createPinia();

// Pasang semua plugin ke app
app.use(globalFunctions);
app.use(pinia);
app.use(router);

// Mount app
app.mount('#app');

// ✅ Pastikan dipanggil setelah Pinia dipasang
const auth = useAuthStore();
auth.initAuth();
