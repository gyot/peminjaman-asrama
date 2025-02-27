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

createApp(App).use(globalFunctions).use(router).mount('#app');