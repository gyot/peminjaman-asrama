<!-- <template>
  <div class="flex h-screen bg-gray-100">

    <aside class="w-64 bg-gray-800 text-white">
      <div class="p-4">
        <span class="text-lg font-bold">Admin Panel</span>
      </div>
      <ul class="space-y-2">
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/dashboard" class="text-white">Dashboard</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/applications" class="text-white">Applications</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/facilities" class="text-white">Facilities</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/users" class="text-white">Users</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/approvals" class="text-white">Approvals</router-link>
        </li>
      </ul>
    </aside>

    <main class="flex-1 p-6">
      <router-view></router-view>
    </main>
  </div>
</template> -->

<template>
  <div
    :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 w-64 bg-blue-900 text-white transform transition-transform md:relative md:translate-x-0 z-50"
    @click.stop
  >
    <div class="p-4 font-bold text-lg flex justify-between">
      LAYANAN BPMP NTB
      <button @click="$emit('close')" class="md:hidden text-white">✖</button>
    </div>
    <nav class="mt-4">
      <ul class="space-y-2">
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/whatsapp" class="text-white">Whatsapp Web</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/dashboard" class="text-white">Dashboard</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/applications" class="text-white">Daftar Pesanan</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/approvals" class="text-white">Pengajuan Terkonfirmasi</router-link>
        </li>
        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/facilities" class="text-white">Fasilitas</router-link>
        </li>

        <li class="px-4 py-2 hover:bg-gray-700 cursor-pointer">
          <router-link to="/users" class="text-white">Users</router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { ref } from "vue";

defineProps(["isOpen"]);
defineEmits(["close"]);

const expanded = ref(false);
</script>
