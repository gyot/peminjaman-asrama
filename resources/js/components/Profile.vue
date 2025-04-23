<template>
    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-white shadow-md rounded-2xl p-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Detail User</h2>

            <div v-if="profile" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Detail Data -->
                <div class="flex flex-col" v-for="(item, index) in details" :key="index">
                    <span class="text-sm text-gray-500">{{ item.label }}:</span>

                    <span class="text-base text-gray-800" v-if="!item.isImage">{{ item.value }}</span>

                    <img v-else :src="`${item.value}`" alt="Foto" class="w-32 h-32 rounded object-cover border" />
                </div>
            </div>

            <div v-else class="text-center text-gray-500 py-8">Memuat data...</div>

            <!-- Tombol Aksi -->
            <div class="mt-6 text-center space-x-4">
                <button @click="openModal"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ubah</button>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">Ubah Data</h3>
                <p class="mb-2">
                    <strong>Tempat Lahir</strong>
                    <input v-model="form.tempat_lahir" type="text" class="w-full border rounded p-2" placeholder="" />
                </p>
                <p class="mb-2">
                    <strong>Tanggal Lahir</strong>
                    <input v-model="form.tanggal_lahir" type="date" class="w-full border rounded p-2"></input>
                </p>
                <p class="mb-2">
                    <strong>Alamat</strong>
                    <input v-model="form.alamat" type="text" class="w-full border rounded p-2"></input>
                </p>
                <p class="mb-2">
                    <strong>Nomor HP</strong>
                    <input v-model="form.nomor_hp" type="text" class="w-full border rounded p-2"></input>
                </p>
                <p class="mb-2">
                    <strong>Foto Saat Ini</strong><br />
                    <img v-if="form.foto" :src="`${form.foto}`" alt="Foto profil"
                        class="w-24 h-24 object-cover rounded mb-2 border" />
                </p>
                <p class="mb-2">
                    <strong>Ganti Foto</strong>
                    <input type="file" @change="handleFileChange" class="w-full border rounded p-2">
                </p>

                <div class="flex justify-end space-x-2">
                    <button @click="showModal = false"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">Batal</button>
                    <button @click="submitForm"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Kirim</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
import Swal from 'sweetalert2';

export default {
    data() {
        const authStore = useAuthStore();
        return {
            authStore,
            showModal: false,
            form: {
                id: authStore.user?.id || 0,
                tempat_lahir: '',
                tanggal_lahir: '',
                alamat: '',
                nomor_hp: '',
                foto: null, // Untuk menyimpan file foto baru
            },
            // tanggal_lahir: '',
            // tanggal_lahir: '',
            // alamat: '',
            // nomor_hp: '',
            // foto: '',
            profile: []
        };
    },
    computed: {
        details() {
            return [
                { label: 'Tempat Lahir', value: this.profile.tempat_lahir },
                { label: 'Tanggal Lahir', value: this.dateFormat(this.profile.tanggal_lahir) },
                { label: 'Alamat', value: this.profile.alamat },
                { label: 'Nomor HP', value: this.profile.nomor_hp },
                { label: 'Foto', value: this.profile.foto, isImage: true },
            ];
        },
    },
    created() {
        this.authStore.fetchUser().then(() => {
            this.fetchData();
        });
        // this.name = this.authStore.user.name;
        // this.email = this.authStore.user.email;
        // this.password = this.authStore.user.password;
    },
    mounted() {
        // this.fetchData();
        // this.getServerHost();
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get(`/api/profile/${this.authStore.user?.id || 0}`);
                this.profile = response.data;
                this.profile.foto = `/storage/images/${this.profile.foto}`; // Set URL untuk foto
                // console.log('Fetched profile:', response.data);

            } catch (error) {
                console.error(error);
            }
        },
        openModal() {
            this.showModal = true;
            this.form.id = this.authStore.user?.id || 0;
            this.form.tanggal_lahir = this.profile.tanggal_lahir;
            this.form.tempat_lahir = this.profile.tempat_lahir;
            this.form.alamat = this.profile.alamat;
            this.form.nomor_hp = this.profile.nomor_hp;
            this.form.foto = this.profile.foto; // Set foto saat ini untuk preview
            this.form.fotoFile = null; // Reset file foto baru
        },
        closeModal() {
            this.showModal = false;
        },
        async submitForm() {
            if (!this.form.tanggal_lahir || !this.form.tempat_lahir || !this.form.alamat || !this.form.nomor_hp) {
                Swal.fire('Oops!', 'Semua field harus diisi.', 'warning');
                return;
            }
            if (this.form.nomor_hp.length < 10) {
                Swal.fire('Oops!', 'Nomor HP tidak valid.', 'warning');
                return;
            }
            // Format nomor
            let nomor_hp = this.form.nomor_hp;
            if (nomor_hp.startsWith('+')) {
                nomor_hp = nomor_hp.slice(1);
            } else if (nomor_hp.startsWith('0')) {
                nomor_hp = '62' + nomor_hp.slice(1);
            }

            const formData = {
                ...this.form,
                nomor_hp
            };
            
            try {
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                await axios.post(`/api/update/profiles`, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })

                // this.fetchFacilities(); // Refresh data setelah submit
                Swal.fire('Berhasil', 'Data berhasil dirubah', 'success');
                this.fetchData(); // Refresh data setelah submit
                this.closeModal();
            } catch (error) {
                console.error("Error submitting form:", error);
                if (error.response && error.response.data) {
                    alert(`Terjadi kesalahan: ${error.response.data.message}`);
                } else {
                    alert("Terjadi kesalahan saat menyimpan data");
                }
            }
            // const response = await axios.post('/api/update/profile', formData, {
            //     headers: {
            //         'X-CSRF-TOKEN': csrfToken
            //     }
            // }).then(() => {
            //     this.profile=response.data;
            //     // this.showModal = false; // Close modal after success
            //     Swal.fire('Berhasil', 'Data berhasil dirubah', 'success');
            //     // this.fetchData(); // Refresh data after success
            // });

            // } catch (error) {
            //     console.error(error);
            //     Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
            // }
        },
        dateFormat(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            this.form.foto = URL.createObjectURL(file); // preview di UI
            this.form.fotoFile = file; // file asli untuk submit nanti
            console.log('File selected:', file);
            console.log('File URL:', this.foto);

        }


    }
};
</script>

<style scoped>
/* Styling opsional */
</style>