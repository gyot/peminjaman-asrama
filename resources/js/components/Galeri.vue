<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Daftar Sarpras BPMP Provinsi NTB</h1>
      <p class="text-sm mt-2">
      Untuk pemesanan, silakan <a :href="windowLocation+ '/dashboard'" target="_blank" class="text-blue-600 underline">klik di sini</a>.
      </p>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="item in facilities"
          :key="item.id"
          class="bg-white rounded-2xl shadow-md p-4 hover:shadow-xl transition-shadow"
        >
          <img
            :src="windowLocation+'/storage/' + item.image"
            alt="Gambar Sarpras"
            class="w-full h-40 object-cover rounded-xl mb-3"
            @error="event => event.target.src=windowLocation+'/storage/images/logo_kemdikbud.png'"
          />

          <h2 class="text-lg font-semibold">{{ item.name }}</h2>
          <p class="text-blue-600 font-bold">Rp {{ formatRupiah(item.price) }}/{{ item.unit }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from "vue";
  import axios from "axios";
  import Swal from "sweetalert2";
  
  const facilities = ref([]);
  const windowLocation = window.location.origin;
  const formatRupiah = (value) => {
    return value.toLocaleString("id-ID", { style: "currency", currency: "IDR" });
  };

  const kirimTinggiIframe = () => {
    const tinggi = document.body.scrollHeight;
    parent.postMessage({ tinggi }, "*");
  };

  
  const fetchFacilities = async () => {
  try {
    const response = await axios.get("/get/facilities");
    facilities.value = response.data;

    // ⬇ kirim tinggi setelah render selesai
    setTimeout(kirimTinggiIframe, 500); // berikan delay kecil agar DOM selesai dirender
  } catch (error) {
    console.error("Error fetching facilities:", error);
    Swal.fire("Error", "Gagal mengambil data fasilitas", "error");
  }
};

  
onMounted(() => {
  fetchFacilities();
  window.addEventListener("resize", kirimTinggiIframe);
});

  </script>
  
  <style scoped>
  </style>
  