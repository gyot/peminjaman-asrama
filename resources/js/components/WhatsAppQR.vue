<template>
  <div class="qr-container">
    <div class="p-4">
      <h2>Update Host</h2>
      <input v-model="host" placeholder="Masukkan Host https://abcd" />
      <button @click="updateHost()">Kirim</button>
    </div>
    <h2>Scan QR Code WhatsApp</h2>
    <img v-if="qrCode" :src="qrCode" alt="QR Code WhatsApp" />
    <p v-else>Menunggu QR Code...</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      qrCode: null,
      number: '',
      message: '',
      serverHost: '',
      interval: null, // Untuk menyimpan referensi interval
    };
  },
  methods: {
    fetchQRCode(host) {
      axios.get(host + "api/whatsapp/qr-code")
      .then((response) => {
        console.log("QR Code response:", response.data);

        if (response.data.qr_code) {
          this.qrCode = response.data.qr_code;
          console.log("QR Code berhasil diatur:", this.qrCode);
        } else {
          if (response.data.status === "authenticated") {
            console.log("QR Code sudah di-scan dan terautentikasi!");
            clearInterval(this.pollingInterval); // stop polling
          } else {
            console.log("QR Code mungkin sudah di-scan atau kadaluarsa.");
          }
          this.qrCode = null;
        }
      })
      .catch((error) => {
        if (error.response && error.response.status === 404) {
          console.log("QR sudah discan"); // Ini yang kamu mau
          // alert("QR sudah discan, silakan lanjutkan!");
          // Atau update UI sesuai kebutuhan
        } else {
          console.error("Error fetching QR Code:", error);
          // alert("Terjadi kesalahan saat mengambil QR Code.");
        }
      });
    },
    async sendMessage() {
      try {
        console.log("Mengirim ke:", this.number);
        const response = await axios.post(this.serverHost + "/send-message", {
          number: this.number,
          message: this.message,
        });
        console.log("Respon server:", response.data);
        alert(response.data.message);
      } catch (error) {
        console.error("Error:", error.response?.data || error.message);
        alert("Gagal mengirim pesan! Cek konsol untuk detail.");
      }
    },
    getServerHost() {
      axios.get("/api/host")
        .then((response) => {
          console.log(response.data[0].host);
          if (response.data[0].host) {
            this.serverHost = response.data[0].host;
            console.log(this.serverHost + " test "); // Log serverHost setelah di-set
            this.fetchQRCode(response.data[0].host); // Panggil fetchQRCode setelah serverHost di-set
            this.interval = setInterval(() => {
              this.fetchQRCode(response.data[0].host);
            }, 5000); // Refresh QR Code setiap 5 detik
          } else {
            console.error("Host tidak ditemukan");
          }
        })
        .catch((error) => console.error("Error fetching server host:", error));
    },
  },
  mounted() {
    this.getServerHost(); // Hanya panggil getServerHost di sini
  },
  beforeDestroy() {
    if (this.interval) {
      clearInterval(this.interval); // Bersihkan interval saat komponen dihancurkan
    }
  },
};
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