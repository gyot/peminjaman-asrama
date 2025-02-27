<template>
    <div class="p-4 bg-white rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-4">Daftar Fasilitas</h1>
      <button @click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded">
        Tambah
      </button>

        <!-- Modal -->
        <transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
                    <h2 class="text-2xl font-semibold mb-4">Tambah Fasilitas</h2>

                    <form @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-gray-700">Nama Fasilitas</label>
                        <input v-model="form.name" type="text" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Kapasitas</label>
                        <input v-model="form.capacity" type="number" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Satuan</label>
                        <select v-model="form.unit" class="w-full border rounded p-2" required>
                            <option value="Orang">Orang</option>
                            <option value="Unit">Unit</option>
                            <option value="Jam">Jam</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Harga</label>
                        <input v-model="form.price" type="number" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Gambar</label>
                        <input type="file" @change="handleFileUpload" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Catatan Tambahan</label>
                        <textarea v-model="form.description" class="w-full border rounded p-2" required></textarea>
                    </div>
                    <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded mr-2">Tutup</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </form>
                </div>
            </div>
        </transition>
        <!-- End Modal -->
      <table class="table-full border-collapsemt-4">
        <thead>
          <tr>
            <th class="border px-4 py-2 ">Gambar</th>
            <th class="border px-4 py-2">Nama Fasilitas</th>
            <th class="border px-4 py-2">Kapasitas</th>
            <th class="border px-4 py-2">Harga</th>
            <th class="border px-4 py-2">Unit</th>
            <th class="border px-4 py-2">Deskripsi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="facility in facilities" :key="facility.id">
            <td class="border px-4 py-2 whitespace-nowrap"><img :src="'/storage/' +facility.image" alt="" srcset="" width="100"></td>
            <td class="border px-4 py-2">{{ facility.name }}</td>
            <td class="border px-4 py-2">{{ facility.capacity }}</td>
            <td class="border px-4 py-2">{{ facility.price }}</td>
            <td class="border px-4 py-2">{{ facility.unit }}</td>
            <td class="border px-4 py-2">{{ facility.description }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  <style scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.9s ease;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0;
  }
  </style>
  <script>
  import axios from 'axios';

    export default {
        data() {
            return {
                form: {
                    name: '',
                    capacity: '',
                    unit: '',
                    price: '',
                    image: null,
                    description: ''
                },
                facilities: [],
                pollingInterval: null, // Untuk menyimpan interval polling
                isOpen: false
            };
        },
        created() {
            this.fetchFacilities(); // Ambil data saat komponen dibuat
            this.startPolling();    // Mulai polling
        },
        beforeDestroy() {
            this.stopPolling();     // Hentikan polling saat komponen dihancurkan
        },
        methods: {
            fetchFacilities() {
                axios.get('/api/facilities')
                    .then((response) => {
                    this.facilities = response.data; // Perbarui data fasilitas
                })
                .catch((error) => {
                    console.error("Error fetching facilities:", error);
                });
            },
            startPolling() {
                this.pollingInterval = setInterval(() => {
                    this.fetchFacilities(); // Panggil fungsi untuk mengambil data setiap interval
                }, 5000); // Interval 5 detik (5000 ms)
            },
            stopPolling() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval); // Hentikan polling
                }
            },
            openModal() {
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
                this.resetForm();
            },
            handleFileUpload(event) {
                this.form.image = event.target.files[0];
            },
            async submitForm() {
                let formData = new FormData();
                formData.append("name", this.form.name);
                formData.append("capacity", this.form.capacity);
                formData.append("unit", this.form.unit);
                formData.append("price", this.form.price);
                formData.append("description", this.form.description);
                if (this.form.image) {
                    formData.append("image", this.form.image);
                }

                try {
                    await axios.post("/api/setFacilities", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                    });

                    this.fetchFacilities(); // Refresh data setelah submit
                    this.closeModal();
                } catch (error) {
                    console.error("Error submitting form:", error);
                    alert("Terjadi kesalahan saat menyimpan data");
                }
            },
            resetForm() {
                this.form = {
                    name: "",
                    capacity: "",
                    unit: "",
                    price: "",
                    image: null,
                    description: "",
                };
            },
        },
    };
  </script>