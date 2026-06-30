<template>
    <div>
        <h2>Reset Password</h2>
        <form @submit.prevent="handleReset">
            <input v-model="email" type="email" placeholder="Email" required />
            <button type="submit">Reset Password</button>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const email = ref('');

const handleReset = async () => {
    try {
        await authStore.resetPassword(email.value);
        alert('Check your email for reset instructions');
    } catch (error) {
        alert(error);
    }
};
</script>