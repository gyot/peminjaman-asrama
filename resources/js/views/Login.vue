<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
      <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <img
          src="https://storage.googleapis.com/a1aa/image/GPOIRCK9DfwV8p7c2fE5QGSkWoRkudvHRL5aBHFeal4.jpg"
          alt="Illustration"
          class="mx-auto mb-6"
        />
  
        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <input
              v-model="email"
              id="email"
              name="email"
              type="email"
              placeholder="Email Address"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <div class="mb-4 relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              id="password"
              name="password"
              placeholder="Password"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-2 text-gray-600"
              tabindex="-1"
            >
              <span v-if="showPassword">🙈</span>
              <span v-else>👁️</span>
            </button>
          </div>
  
          <div class="flex items-center justify-between mb-4">
            <router-link
              to="/reset-password"
              class="text-blue-500 hover:text-blue-800 text-sm font-bold"
            >
              Forgot Password?
            </router-link>
          </div>
  
          <p v-if="errorMessage" class="text-red-500 text-xs italic">{{ errorMessage }}</p>
  
          <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
          >
            LOGIN
          </button>
        </form>
  
        <p class="mt-4">
          Belum punya akun?
          <router-link to="/register" class="text-blue-500">Daftar di sini</router-link>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useAuthStore } from '../stores/auth';
  import { useRouter } from 'vue-router';
  import Swal from 'sweetalert2';
  
  const authStore = useAuthStore();
  const router = useRouter();
  
  const email = ref('');
  const password = ref('');
  const errorMessage = ref('');
  const showPassword = ref(false);
  
  const handleLogin = async () => {
    if (!email.value || !password.value) {
      errorMessage.value = 'Email dan password harus diisi.';
      return;
    }
  
    try {
      // Tampilkan loading
      Swal.fire({
        title: 'Logging in...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
  
      await authStore.login(email.value, password.value);
  
      Swal.close(); // Tutup loading
  
      const intended = localStorage.getItem('intendedRoute');
      if (intended) {
        localStorage.removeItem('intendedRoute');
        router.push('/applications');
      } else {
        router.push('/applications');
      }
    } catch (error) {
      Swal.close(); // Tutup loading jika gagal
      errorMessage.value = 'Login gagal, periksa kembali email dan password.';
    }
  };
  </script>
  