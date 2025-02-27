<template>
    <div class="p-4 bg-white rounded-lg shadow-md overflow-x-auto">
        <h1 class="text-2xl font-bold mb-4">Daftar Permohonan Peminjaman</h1>
        <table class="min-w-fit bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Nama Pemohon</th>
                    <th class="border px-4 py-2">Alamat</th>
                    <th class="border px-4 py-2">Nama Kegiatan</th>
                    <th class="border px-4 py-2">Tanggal Mulai Pemakaian</th>
                    <th class="border px-4 py-2">Tanggal Selesai Pemakaian</th>
                    <th class="border px-4 py-2">WhatsApp</th>
                    <th class="border px-4 py-2">Ket.</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="application in application" :key="application.id">
                    <td class="border px-4 py-2">{{ application.name }}</td>
                    <td class="border px-4 py-2">{{ application.address }}</td>
                    <td class="border px-4 py-2">{{ application.event_name }}</td>
                    <td class="border px-4 py-2">{{ $formatDate(application.event_start_date) }}</td>
                    <td class="border px-4 py-2">{{ $formatDate(application.event_end_date) }}</td>
                    <td class="border px-4 py-2">{{ application.phone_number }}</td>
                    <td class="border px-4 py-2">{{ application.notes }}</td>
                    <td class="border px-4 py-2">
                        <button @click="openModal(application.phone_number)" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Kirim Pesan
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Modal -->
        <transition name="fade">
            <div v-if="isModalOpen" class="modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
                    <h2 class="text-2xl font-semibold mb-4">Konfirmasi Ke Peminjam</h2>
                    <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-gray-700">Nomor WhatsApp</label>
                        <input v-model="number" class="w-full border rounded p-2" placeholder="Nomor WhatsApp (628xxx)" readonly/>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Pesan</label>
                        <textarea v-model="message" class="w-full border rounded p-2" placeholder="Pesan"></textarea>
                    </div>
                    <button @click="sendMessage" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Kirim</button>
                    <button @click="closeModal" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Batal</button>
                    <!-- <button @click="sendMessage">Kirim</button>
                    <button @click="closeModal">Batal</button> -->
                    </form>
                </div>
            </div>
        </transition>
        <!-- End Modal -->
    </div>
</template>

<script>

import axios from 'axios';
// import { useFunctionStore } from '../functionStore';

// const functionStore = useFunctionStore();

export default {
    data() {
        return {
            isModalOpen: false,
            application: [],
            pollingInterval: null, // Untuk menyimpan interval polling
        };
    },
    created() {
        this.fetchAacilities(); // Ambil data saat komponen dibuat
        this.startPolling();    // Mulai polling
    },
    beforeDestroy() {
        this.stopPolling();     // Hentikan polling saat komponen dihancurkan
    },
    methods: {
        fetchAacilities() {
            axios.get('/api/applications')
                .then((response) => {
                    this.application = response.data; // Perbarui data fasilitas
                })
                .catch((error) => {
                    console.error("Error fetching application:", error);
                });
        },
        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.fetchAacilities(); // Panggil fungsi untuk mengambil data setiap interval
            }, 5000); // Interval 5 detik (5000 ms)
        },
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval); // Hentikan polling
            }
        },
        async sendMessage() {
            // try {
                console.log("Mengirim ke:", this.number);
                const response = await axios.post("http://localhost:80/send-message", {
                    number: this.number,
                    message: this.message,
                });
                console.log("Respon server:", response.data);
                // alert(response.data.message);
                this.isModalOpen = false;
            // } catch (error) {
            //     console.error("Error:", error.response?.data || error.message);
            //     alert("Gagal mengirim pesan! Cek konsol untuk detail.");
            // }
        },
        openModal(value) {
            this.number = value; // Set nilai yang ingin ditampilkan
            this.isModalOpen = true; // Tampilkan modal
        },
        closeModal() {
            this.isModalOpen = false; // Sembunyikan modal
            this.modalValue = null;   // Reset nilai modal
        },
    },
};
</script>