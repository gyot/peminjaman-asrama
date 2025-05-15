<template>
    <div>
      <div class="flex space-x-4 border-b mb-4">
        <router-link to="/user/account" class="pb-2" active-class="border-b-2 border-blue-500 font-semibold">Akun</router-link>
        <router-link to="/user/profile" class="pb-2" active-class="border-b-2 border-blue-500 font-semibold">Profil</router-link>
        <router-link to="/user/education" class="pb-2" active-class="border-b-2 border-blue-500 font-semibold">Pendidikan</router-link>
        <router-link to="/user/position" class="pb-2" active-class="border-b-2 border-blue-500 font-semibold">Jabatan</router-link>
      </div>
  
      <router-view />
    </div>
  </template>
  