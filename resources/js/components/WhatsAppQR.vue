<template>
    <div class="qr-container">
        <div class="p-4">
            <h2>Kirim Pesan WhatsApp</h2>
            <input v-model="number" placeholder="Nomor WhatsApp (628xxx)" />
            <textarea v-model="message" placeholder="Pesan"></textarea>
            <button @click="sendMessage">Kirim</button>
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
        message: ''
      };
    },
    methods: {
        fetchQRCode() {
            axios.get("http://localhost/get-qr")
            .then((response) => {
                if (response.data.qr) {
                this.qrCode = response.data.qr;
                } else {
                this.qrCode = null;
                }
            })
            .catch((error) => console.error("Error fetching QR Code:", error));
        },
        async sendMessage() {
            try {
                console.log("Mengirim ke:", this.number);
                const response = await axios.post("http://localhost/send-message", {
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
    },
    mounted() {
      this.fetchQRCode();
      setInterval(this.fetchQRCode, 5000); // Refresh QR Code setiap 5 detik
      
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
  