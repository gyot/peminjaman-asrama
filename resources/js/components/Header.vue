<template>
    <header class="flex justify-between items-center bg-white p-2 shadow-md">
        <button @click="$emit('toggleMenu')" class="md:hidden text-blue-900">☰</button>
        <div class="text-xl font-semibold">{{title}}</div>
        <div>
            <button class="px-4 py-1 text-blue-900 border border-blue-900 rounded">Daftar</button>
            <button class="px-4 py-1 bg-blue-900 border border-blue-900 text-white rounded">Masuk</button>
        </div>
    </header>
</template>
  