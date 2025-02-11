<template>
    <div class="p-6">
      <h1 class="text-xl font-bold mb-4">Signup</h1>
      <form @submit.prevent="submit">
        <input v-model="form.name" placeholder="Name" class="border p-2">
        <input v-model="form.email" placeholder="Email" class="border p-2 mt-2">
        <input v-model="form.password" type="password" placeholder="Password" class="border p-2 mt-2">
        <button type="submit" class="bg-blue-500 text-white p-2 mt-2">Signup</button>
      </form>
    </div>
  </template>
  <script>
  import { router } from '@inertiajs/vue3';
  export default {
    data() {
      return { form: { name: '', email: '', password: '' } };
    },
    methods: {
      submit() { router.post('/signup', this.form); }
    }
  };
  </script>