<template>
    <div class="p-4 bg-white rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-4">Daftar Persetujuan</h1>
      <table class="w-full border-collapse">
        <thead>
          <tr>
            <th class="border px-4 py-2">ID Permohonan</th>
            <th class="border px-4 py-2">Disetujui Oleh</th>
            <th class="border px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="approval in approvals" :key="approval.id">
            <td class="border px-4 py-2">{{ approval.application_id }}</td>
            <td class="border px-4 py-2">{{ approval.approved_by }}</td>
            <td class="border px-4 py-2">{{ approval.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        approvals: [],
      };
    },
    created() {
      axios.get('/api/approvals').then((response) => {
        this.approvals = response.data;
      });
    },
    methods: {
      formatDate(date) {
        return new Date(date).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
        });
      },
    },
    mounted() {
      this.pollingInterval = setInterval(() => {
        axios.get('/api/approvals').then((response) => {
          this.approvals = response.data;
        });
      }, 5000);
    },
    beforeDestroy() {
      clearInterval(this.pollingInterval);
    },
    watch: {
      approvals(newVal) {
        if (newVal.length > 0) {
          this.$emit('approvals-updated', newVal);
        }
      },
    },
    computed: {
      formattedApprovals() {
        return this.approvals.map((approval) => ({
          ...approval,
          formattedDate: this.formatDate(approval.created_at),
        }));
      },
    },
  };
  </script>