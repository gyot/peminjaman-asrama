<template>
    <div>
        <h2>Register</h2>
        <form @submit.prevent="handleRegister">
            <input v-model="name" type="text" placeholder="Name" required />
            <input v-model="email" type="email" placeholder="Email" required />
            <input v-model="password" type="password" placeholder="Password" required />
            <button type="submit">Register</button>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const name = ref('');
const email = ref('');
const password = ref('');

const handleRegister = async () => {
    try {
        await authStore.register(name.value, email.value, password.value);
        router.push('/login');
    } catch (error) {
        alert(error);
    }
};
</script>