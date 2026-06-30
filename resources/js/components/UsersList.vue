<template>
    <div class="p-4 bg-white rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-4">Daftar Pengguna</h1>
      <table class="w-full border-collapse">
        <thead>
          <tr>
            <th class="border px-4 py-2">Nama</th>
            <th class="border px-4 py-2">Email</th>
            <th class="border px-4 py-2">Role</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="border px-4 py-2">{{ user.name }}</td>
            <td class="border px-4 py-2">{{ user.email }}</td>
            <td class="border px-4 py-2">{{ user.role }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        users: [],
      };
    },
    created() {
      axios.get('/api/users').then((response) => {
        this.users = response.data;
      });
    },
  };
  </script>