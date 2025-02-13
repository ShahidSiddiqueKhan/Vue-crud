import { createRouter, createWebHistory } from 'vue-router';
import Tasks from '../Pages/Tasks/Index.vue';
import Users from '../Pages/Users/Index.vue';

const routes = [
  { path: '/tasks', component: Tasks },
  { path: '/users', component: Users },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
