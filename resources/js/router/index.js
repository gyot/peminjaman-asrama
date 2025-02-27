import { createRouter, createWebHistory } from 'vue-router';
import WhatsApp from '../views/WhatsApp.vue';
import Dashboard from '../views/Dashboard.vue';
import Applications from '../views/Applications.vue';
import Facilities from '../views/Facilities.vue';
import Users from '../views/Users.vue';
import Approvals from '../views/Approvals.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/whatsapp', name: 'whatsapp', component: WhatsApp },
  { path: '/dashboard', name: 'dashboard', component: Dashboard },
  { path: '/applications', name: 'applications', component: Applications },
  { path: '/facilities', name: 'facilities', component: Facilities },
  { path: '/users', name: 'users', component: Users },
  { path: '/approvals', name: 'approvals', component: Approvals },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;