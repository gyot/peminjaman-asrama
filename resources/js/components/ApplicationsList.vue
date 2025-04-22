<template>
    <div class="p-4 bg-white rounded-lg shadow-md overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Daftar Permohonan Peminjaman</h1>
            <div class="space-x-2">
                <button @click="exportExcel"
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Excel</button>
                <button @click="exportPDF" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">PDF</button>
            </div>
        </div>

        <div class="mb-4">
            <input v-model="search" type="text" placeholder="Cari berdasarkan nama pemohon/kegiatan..."
                class="border rounded p-2 w-full" />
        </div>

        <table class="min-w-fit bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Nama Pemohon</th>
                    <th class="border px-4 py-2">Alamat</th>
                    <th class="border px-4 py-2">Nama Kegiatan</th>
                    <th class="border px-4 py-2">Tanggal Mulai</th>
                    <th class="border px-4 py-2">Tanggal Selesai</th>
                    <th class="border px-4 py-2">Tanggal Permohonan</th>
                    <th class="border px-4 py-2">WhatsApp</th>
                    <th class="border px-4 py-2">Ket.</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="application in paginatedData" :key="application.id">
                    <td class="border px-4 py-2">{{ application.name }}</td>
                    <td class="border px-4 py-2">{{ application.address }}</td>
                    <td class="border px-4 py-2">{{ application.event_name }}</td>
                    <td class="border px-4 py-2">{{ $formatDate(application.event_start_date) }}</td>
                    <td class="border px-4 py-2">{{ $formatDate(application.event_end_date) }}</td>
                    <td class="border px-4 py-2">{{ application.created_at }}</td>
                    <td class="border px-4 py-2">{{ application.phone_number }}</td>
                    <td class="border px-4 py-2">{{ application.notes }}</td>
                    <td class="border px-4 py-2">
                        <router-link :to="`/applications/${application.id}`"
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Detail</router-link>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center">
            <p>Menampilkan {{ paginatedData.length }} dari {{ filteredData.length }} entri</p>
            <div class="space-x-2">
                <button @click="prevPage" :disabled="currentPage === 1"
                    class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">Sebelumnya</button>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">Berikutnya</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import * as XLSX from 'xlsx';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

export default {
    data() {
        return {
            application: [],
            search: '',
            currentPage: 1,
            itemsPerPage: 10
        };
    },
    computed: {
        filteredData() {
            const keyword = this.search.toLowerCase();
            return this.application.filter(app =>
                app.name.toLowerCase().includes(keyword) ||
                app.event_name.toLowerCase().includes(keyword)
            );
        },
        totalPages() {
            return Math.ceil(this.filteredData.length / this.itemsPerPage);
        },
        paginatedData() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredData.slice(start, start + this.itemsPerPage);
        }
    },
    methods: {
        $formatDateTime(date) {
            const d = new Date(date);
            const options = {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                timeZone: 'Asia/Makassar' // WITA
            };

            const formatter = new Intl.DateTimeFormat('id-ID', options);
            return formatter.format(d) + ' WITA';
        },
        fetchData() {
            axios.get('/api/applications')
                .then(res => this.application = res.data)
                .catch(err => console.error(err));
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        exportExcel() {
            const ws = XLSX.utils.json_to_sheet(this.filteredData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Permohonan');
            XLSX.writeFile(wb, 'permohonan.xlsx');
        },
        exportPDF() {
            const doc = new jsPDF();
            doc.autoTable({
                head: [['Nama Pemohon', 'Alamat', 'Nama Kegiatan', 'Tgl Mulai', 'Tgl Selesai', 'WhatsApp', 'Ket.']],
                body: this.filteredData.map(a => [
                    a.name, a.address, a.event_name,
                    this.$formatDate(a.event_start_date),
                    this.$formatDate(a.event_end_date),
                    a.phone_number, a.notes
                ])
            });
            doc.save('permohonan.pdf');
        }
    },
    mounted() {
        this.fetchData();
    }
};
</script>