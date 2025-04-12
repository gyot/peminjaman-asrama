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
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import globalFunctions from './globalFunctions';
import { createPinia } from 'pinia';
// di main.js atau main.ts
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'

createApp(App).use(globalFunctions).use(createPinia()).use(router).mount('#app');