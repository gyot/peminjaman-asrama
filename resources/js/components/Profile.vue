<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Profil Pengguna</h1>
    <p><strong>Nama:</strong> {{ user.name }}</p>
    <p><strong>Email:</strong> {{ user.email }}</p>
    <p><strong>Role:</strong> {{ user.role }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {
        name: 'John Doe',
        email: 'john.doe@example.com',
        role: 'Admin',
      },
    };
  },
};
</script>