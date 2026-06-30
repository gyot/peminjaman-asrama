// import { defineStore } from 'pinia';

// export const useFunctionStore = defineStore('functionStore', {
//   actions: {
//     formatCurrency(amount) {
//       return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
//     },
//     formatDate(dateString, locale = 'id-ID') {
//         if (!dateString) return '-';
//         const date = new Date(dateString);
//         return date.toLocaleDateString(locale, {
//           day: '2-digit',
//           month: 'long',
//           year: 'numeric'
//         });
//       }
//   }
// });

export default {
  install(app) {
    app.config.globalProperties.$formatDate = (dateString, locale = 'id-ID') => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString(locale, {
          day: '2-digit',
          month: 'long',
          year: 'numeric'
        });
    };
  }
};
