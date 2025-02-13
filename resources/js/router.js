import { createRouter, createWebHistory } from 'vue-router';
import Users from './Pages/Users/Index.vue';
import Tasks from './Pages/Tasks/Index.vue';

const routes = [
    { path: '/users', component: Users },
    { path: '/tasks', component: Tasks }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
