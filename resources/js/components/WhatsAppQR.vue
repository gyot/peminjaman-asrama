<template>
  <div class="qr-container">
    <div class="p-4">
      <h2>Update Host</h2>
      <input v-model="host" placeholder="Masukkan Host https://abcd" class="border rounded p-2" />
      <button @click="updateHost" class="bg-blue-500 text-white px-4 py-2 rounded">Kirim</button>
    </div>
    <h2>Scan QR Code WhatsApp</h2>
    <img v-if="qrCode" :src="qrCode" alt="QR Code WhatsApp" />
    <p v-else>Menunggu QR Code...</p>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const qrCode = ref(null);
const number = ref("");
const message = ref("");
const serverHost = ref("");
const interval = ref(null);
const host = ref("");

// Fungsi untuk mengambil QR Code
const fetchQRCode = async (host) => {
  try {
    const response = await axios.get(`${host}api/whatsapp/qr-code`);
    console.log("QR Code response:", response.data);

    if (response.data.qr_code) {
      qrCode.value = response.data.qr_code;
      console.log("QR Code berhasil diatur:", qrCode.value);
    } else {
      if (response.data.status === "authenticated") {
        console.log("QR Code sudah di-scan dan terautentikasi!");
        clearInterval(interval.value); // Hentikan polling
      } else {
        console.log("QR Code mungkin sudah di-scan atau kadaluarsa.");
      }
      qrCode.value = null;
    }
  } catch (error) {
    if (error.response && error.response.status === 404) {
      console.log("QR sudah discan");
    } else {
      console.error("Error fetching QR Code:", error);
    }
  }
};

// Fungsi untuk mengirim pesan
const sendMessage = async () => {
  try {
    console.log("Mengirim ke:", number.value);
    const response = await axios.post(`${serverHost.value}/send-message`, {
      number: number.value,
      message: message.value,
    });
    console.log("Respon server:", response.data);
    alert(response.data.message);
  } catch (error) {
    console.error("Error:", error.response?.data || error.message);
    alert("Gagal mengirim pesan! Cek konsol untuk detail.");
  }
};

// Fungsi untuk mendapatkan host server
const getServerHost = async () => {
  try {
    const response = await axios.get("host");
    console.log(response.data[0].host);
    if (response.data[0].host) {
      serverHost.value = response.data[0].host;
      console.log(`${serverHost.value} test`); // Log serverHost setelah di-set
      fetchQRCode(serverHost.value); // Panggil fetchQRCode setelah serverHost di-set
      interval.value = setInterval(() => {
        fetchQRCode(serverHost.value);
      }, 5000); // Refresh QR Code setiap 5 detik
    } else {
      console.error("Host tidak ditemukan");
    }
  } catch (error) {
    console.error("Error fetching server host:", error);
  }
};

// Fungsi untuk memperbarui host
const updateHost = async () => {
  try {
    const response = await axios.post("host", { host: host.value });
    console.log("Host diperbarui:", response.data);
    alert("Host berhasil diperbarui!");
    getServerHost(); // Refresh host setelah diperbarui
  } catch (error) {
    console.error("Error updating host:", error);
    alert("Gagal memperbarui host! Cek konsol untuk detail.");
  }
};
const fetchDataProfil = async () => {
  try {
    const response = await axios.get(window.location.origin+`/api/profile/${authStore.user?.id}`);
    console.log(response.data); // Lakukan sesuatu dengan data profil
  } catch (error) {
    router.push("/user/profile");
    console.error("Gagal memuat profil:", error);
  }
};
// Lifecycle hooks
onMounted(() => {
  getServerHost(); // Panggil getServerHost saat komponen dimuat
  fetchDataProfil();
});

onBeforeUnmount(() => {
  if (interval.value) {
    clearInterval(interval.value); // Bersihkan interval saat komponen dihancurkan
  }
});
</script>

<style scoped>
.qr-container {
  text-align: center;
}

img {
  width: 250px;
  height: auto;
  border: 2px solid #000;
}
</style>