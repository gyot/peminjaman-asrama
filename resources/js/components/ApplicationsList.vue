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
                        <button @click="openModal(application.phone_number)"
                            class="bg-blue-500 text-white px-4 py-2 rounded">
                            Kirim Pesan
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <transition name="fade">
            <div v-if="isModalOpen" class="modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
                    <h2 class="text-2xl font-semibold mb-4">Konfirmasi Ke Peminjam</h2>
                    <form @submit.prevent="sendMessage" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-gray-700">Nomor WhatsApp</label>
                            <input v-model="number" class="w-full border rounded p-2"
                                placeholder="Nomor WhatsApp (628xxx)" readonly />
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700">Pesan</label>
                            <textarea v-model="message" class="w-full border rounded p-2"
                                placeholder="Pesan"></textarea>
                        </div>
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">Kirim</button>
                        <button @click="closeModal" type="button"
                            class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">Batal</button>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            isModalOpen: false,
            application: [],
            pollingInterval: null,
            serverHost: null,
            number: '',
            message: ''
        };
    },
    created() {
        this.fetchApplications();
        this.startPolling();
    },
    beforeDestroy() {
        this.stopPolling();
    },
    methods: {
        fetchApplications() {
            axios.get('/api/applications')
                .then(response => {
                    this.application = response.data;
                })
                .catch(error => {
                    Swal.fire('Error', 'Gagal mengambil data aplikasi!', 'error');
                });
        },
        startPolling() {
            this.pollingInterval = setInterval(this.fetchApplications, 5000);
        },
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        },
        async sendMessage() {
            try {
                const response = await axios.post(this.serverHost + "/api/whatsapp/send-message", {
                    number: this.number,
                    message: this.message,
                });
                Swal.fire('Sukses', response.data.message, 'success');
                this.isModalOpen = false;
            } catch (error) {
                Swal.fire({
                    icon: "error", // ✅ Ikon yang valid
                    title: "Gagal mengirim pesan!",
                    text: "Coba scan ulang QRCode whatsapp!",
                });
            }
        },
        openModal(value) {
            this.number = value;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.number = '';
            this.message = '';
        },
        getServerHost() {
            axios.get("/api/host")
                .then(response => {
                    this.serverHost = response.data[0]?.host || null;
                })
                .catch(() => Swal.fire('Error', 'Gagal mendapatkan server host!', 'error'));
        },
    },
    mounted() {
        this.getServerHost();
    }
};
</script>
