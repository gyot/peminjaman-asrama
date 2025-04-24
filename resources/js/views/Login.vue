

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
            <img src="https://storage.googleapis.com/a1aa/image/GPOIRCK9DfwV8p7c2fE5QGSkWoRkudvHRL5aBHFeal4.jpg"
                alt="Illustration of hands signing a document on a computer screen" class="mx-auto mb-6" />

            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <input v-model="email" id="email" name="email" type="email" placeholder="Email Address"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div class="mb-4">
                    <input v-model="password" id="password" name="password" type="password" placeholder="Password"          
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div class="flex items-center justify-between mb-4">
                    <router-link to="/reset-password" class="text-blue-500 hover:text-blue-800 text-sm font-bold">
                        Forgot Password?
                    </router-link>
                </div>
                <p v-if="errorMessage" class="text-red-500 text-xs italic">{{ errorMessage }}</p>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    LOGIN
                </button>
            </form>
            <p class="mt-4">
                Belum punya akun? <router-link to="/register" class="text-blue-500">Daftar di sini</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const email = ref('');
const password = ref('');
const errorMessage = ref('');

const handleLogin = async () => {
    if (!email.value || !password.value) {
        errorMessage.value = 'Email dan password harus diisi.';
        return;
    }

    try {
        await authStore.login(email.value, password.value);

        const intended = localStorage.getItem('intendedRoute');
        if (intended) {
        localStorage.removeItem('intendedRoute');
        router.push('/applications');
        } else {
        router.push('/applications');
        }

    } catch (error) {
        errorMessage.value = 'Login gagal, periksa kembali email dan password.';
    }
};
</script>
