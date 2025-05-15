<template>
    <header class="flex justify-between items-center bg-white p-2 shadow-md">
        <!-- Tombol menu -->
        <button @click="$emit('toggleMenu')" class="md:hidden text-blue-900">☰</button>
        
        <!-- Judul -->
        <div class="text-xl font-semibold">{{ title }}</div>
        
        <!-- Informasi User & Tombol -->
        <div class="flex items-center gap-4">
            <h1>Welcome, {{ authStore.user ? authStore.user.name : 'Guest' }}</h1>

            <!-- Tombol untuk user yang belum login -->
            <template v-if="!authStore.user">
                <router-link to="/register">
                    <button class="px-4 py-1 text-blue-900 border border-blue-900 rounded">Daftar</button>
                </router-link>
                <router-link to="/login">
                    <button class="px-4 py-1 bg-blue-900 border border-blue-900 text-white rounded">Masuk</button>
                </router-link>
            </template>

            <!-- Tombol logout untuk user yang sudah login -->
            <template v-else>
                <button @click="handleLogout" class="px-4 py-1 bg-red-600 border border-red-600 text-white rounded">Keluar</button>
            </template>
        </div>
    </header>
</template>

<script setup>
import { defineProps } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

defineProps(['title']);
const authStore = useAuthStore();
const router = useRouter();

// Fungsi Logout
const handleLogout = () => {
    authStore.logout();
    router.push('/login');
};
</script>
